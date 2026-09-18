# Manual Bank Transfer & QRIS Payment Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Midtrans automated payment gateway with admin-managed
bank accounts / QRIS images, verified manually from a customer-uploaded
proof-of-transfer image.

**Architecture:** New `payment_channels` table (admin CRUD, mirrors the
existing `BongkarPayoutMethodModel`/`Controller` pattern). `orders` gains
columns for the chosen channel and proof lifecycle. `order_payments`
(Midtrans-only) and all Midtrans code is deleted. Voucher-commit and
admin-WA-notify logic that lived in the Midtrans webhook moves to a new
admin "verify payment" action.

**Tech Stack:** CodeIgniter 4, MySQL (dev) / SQLite (tests), Tailwind,
vanilla JS (existing `interactive.php` state machine).

**Spec:** `docs/superpowers/specs/2026-09-15-manual-bank-payment-design.md`

## Global Constraints
- Follow existing admin CRUD pattern exactly: see
  `app/Controllers/Admin/BongkarPayoutMethodController.php` +
  `app/Views/admin/bongkar_payout_methods/{index,form}.php`.
- File uploads reuse `app/Helpers/upload_helper.php` validators
  (`validate_uploaded_image_dimensions`, `validate_uploaded_image_size`,
  `delete_public_asset`) and the `getRandomName()` + `mime_in`/`ext_in`
  validation style from `app/Controllers/Admin/BannerController.php`.
- Every new admin route sits inside the existing `admin` route group in
  `app/Config/Routes.php` (auth-filtered already).
- New public endpoints (`uploadProof`) get the existing `ratelimit:N:60`
  filter, matching `bongkar/submit`.
- No new composer dependencies. Do not remove `midtrans/midtrans-php` from
  composer.json (leave installed, unused).
- Reuse `$orderModel->db->transStart()/transComplete()` transaction pattern
  (see current `MidtransController::webhook`) for any multi-table write.

---

### Task 1: Database migrations

**Files:**
- Create: `app/Database/Migrations/2026-09-15-100000_CreatePaymentChannelsTable.php`
- Create: `app/Database/Migrations/2026-09-15-100100_AddPaymentFieldsToOrders.php`
- Create: `app/Database/Migrations/2026-09-15-100200_DropOrderPayments.php`

**Interfaces:**
- Produces: table `payment_channels(id, type, name, account_number, account_holder, qr_image_path, sort_order, is_active, created_at, updated_at)`.
- Produces: `orders.payment_channel_id`, `orders.payment_proof_path`, `orders.payment_proof_uploaded_at`, `orders.payment_verified_by`, `orders.payment_verified_at`. Removes `orders.snap_token`.
- Produces: `order_payments` table dropped.

- [ ] **Step 1: Write `CreatePaymentChannelsTable`**

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentChannelsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'type'           => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'bank'],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'account_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'account_holder' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'qr_image_path'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payment_channels');
    }

    public function down()
    {
        $this->forge->dropTable('payment_channels');
    }
}
```

- [ ] **Step 2: Write `AddPaymentFieldsToOrders`**

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentFieldsToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'payment_channel_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'voucher_id'],
            'payment_proof_path'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'payment_channel_id'],
            'payment_proof_uploaded_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'payment_proof_path'],
            'payment_verified_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'payment_proof_uploaded_at'],
            'payment_verified_at'       => ['type' => 'DATETIME', 'null' => true, 'after' => 'payment_verified_by'],
        ]);
        $this->forge->dropColumn('orders', 'snap_token');
    }

    public function down()
    {
        $this->forge->addColumn('orders', [
            'snap_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->dropColumn('orders', ['payment_channel_id', 'payment_proof_path', 'payment_proof_uploaded_at', 'payment_verified_by', 'payment_verified_at']);
    }
}
```

- [ ] **Step 3: Write `DropOrderPayments`**

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropOrderPayments extends Migration
{
    public function up()
    {
        $this->forge->dropTable('order_payments');
    }

    public function down()
    {
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'midtrans_order_id'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'midtrans_transaction_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'notification_key'       => ['type' => 'CHAR', 'constraint' => 64, 'null' => true],
            'payment_method'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'payment_channel'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'paid_at'                => ['type' => 'DATETIME', 'null' => true],
            'raw_notification'       => ['type' => 'TEXT', 'null' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('order_payments');
    }
}
```

- [ ] **Step 4: Run migrations and confirm schema**

Run: `php spark migrate` then `php spark db:table orders` and
`php spark db:table payment_channels` — confirm the new columns/table exist
and `snap_token`/`order_payments` are gone.

- [ ] **Step 5: Commit**

```bash
git add app/Database/Migrations/2026-09-15-100000_CreatePaymentChannelsTable.php app/Database/Migrations/2026-09-15-100100_AddPaymentFieldsToOrders.php app/Database/Migrations/2026-09-15-100200_DropOrderPayments.php
git commit -m "feat: add payment_channels table and order payment-proof columns"
```

---

### Task 2: Models

**Files:**
- Create: `app/Models/PaymentChannelModel.php`
- Modify: `app/Models/OrderModel.php`
- Delete: `app/Models/OrderPaymentModel.php`

**Interfaces:**
- Consumes: `payment_channels` table from Task 1.
- Produces: `PaymentChannelModel::listActive(): array` (used by checkout
  views and `OrderController::store()` validation).
- Produces: `OrderModel::adminStatuses()` now includes
  `'menunggu_verifikasi'`.

- [ ] **Step 1: Create `PaymentChannelModel`**

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentChannelModel extends Model
{
    protected $table         = 'payment_channels';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['type', 'name', 'account_number', 'account_holder', 'qr_image_path', 'sort_order', 'is_active'];
    protected $useTimestamps = true;
    protected $validationRules = [
        'id'   => 'permit_empty|is_natural',
        'type' => 'required|in_list[bank,qris]',
        'name' => 'required|max_length[100]',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order')->findAll();
    }
}
```

- [ ] **Step 2: Update `OrderModel`**

In `app/Models/OrderModel.php`:
- Replace the `$allowedFields` array (currently ends `..., 'processed_by', 'completed_at', 'snap_token',`) with:

```php
    protected $allowedFields = [
        'invoice_number', 'product_id', 'product_name_snapshot', 'nominal_snapshot',
        'price_snapshot', 'game_id', 'whatsapp_number', 'voucher_id', 'discount_amount',
        'voucher_reserved', 'voucher_committed', 'voucher_reserved_until', 'idempotency_token', 'wablas_notification_claimed',
        'payment_channel_id', 'payment_proof_path', 'payment_proof_uploaded_at', 'payment_verified_by', 'payment_verified_at',
        'total_amount', 'status', 'processed_by', 'completed_at',
    ];
```

- Update `adminStatuses()`:

```php
    public static function adminStatuses(): array
    {
        return ['menunggu_pembayaran', 'menunggu_verifikasi', 'diproses', 'selesai', 'gagal', 'dibatalkan'];
    }
```

- [ ] **Step 3: Delete `OrderPaymentModel.php`**

- [ ] **Step 4: Commit**

```bash
git add app/Models/PaymentChannelModel.php app/Models/OrderModel.php
git rm app/Models/OrderPaymentModel.php
git commit -m "feat: add PaymentChannelModel, retire OrderPaymentModel"
```

---

### Task 3: Status label/badge for the new status

**Files:**
- Modify: `app/Helpers/order_helper.php`

- [ ] **Step 1: Add `menunggu_verifikasi` to both helper arrays**

```php
    function order_status_label(string $status): string
    {
        return [
            'menunggu_pembayaran'  => 'Menunggu Pembayaran',
            'menunggu_verifikasi'  => 'Menunggu Verifikasi',
            'diproses'             => 'Diproses',
            'selesai'              => 'Selesai',
            'gagal'                => 'Gagal',
            'dibatalkan'           => 'Dibatalkan',
        ][$status] ?? $status;
    }
```

(drop the old `'dibayar'` entry — that status is no longer produced anywhere)

```php
    function order_status_badge_class(string $status): string
    {
        return [
            'menunggu_pembayaran' => 'badge-warning',
            'menunggu_verifikasi' => 'badge-warning',
            'diproses'            => 'badge-primary',
            'selesai'             => 'badge-success',
            'gagal'               => 'badge-danger',
            'dibatalkan'          => 'badge-danger',
        ][$status] ?? 'badge-primary';
    }
```

- [ ] **Step 2: `php -l app/Helpers/order_helper.php`, then commit**

```bash
git add app/Helpers/order_helper.php
git commit -m "feat: add menunggu_verifikasi order status label/badge"
```

---

### Task 4: Admin payment channel CRUD

**Files:**
- Create: `app/Controllers/Admin/PaymentChannelController.php`
- Create: `app/Views/admin/payment_channels/index.php`
- Create: `app/Views/admin/payment_channels/form.php`
- Modify: `app/Config/Routes.php` (add routes)
- Modify: `app/Views/layouts/admin.php` (sidebar link)

**Interfaces:**
- Consumes: `PaymentChannelModel` (Task 2), `upload_helper` functions
  (`validate_uploaded_image_dimensions`, `validate_uploaded_image_size`,
  `delete_public_asset`).

- [ ] **Step 1: Write the controller**

```php
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentChannelModel;

class PaymentChannelController extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'assets/uploads/payment_channels/';

    protected PaymentChannelModel $channels;

    public function __construct()
    {
        helper('upload');
        $this->channels = new PaymentChannelModel();
    }

    public function index()
    {
        return view('admin/payment_channels/index', [
            'channels' => $this->channels->orderBy('sort_order')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/payment_channels/form', ['channel' => null]);
    }

    public function store()
    {
        $rules = $this->channels->getValidationRules();
        $type = $this->request->getPost('type') === 'qris' ? 'qris' : 'bank';

        if ($type === 'bank') {
            $rules['account_number'] = 'required|max_length[50]';
            $rules['account_holder'] = 'required|max_length[150]';
        } else {
            $rules['qr_image'] = 'uploaded[qr_image]|max_size[qr_image,2048]|is_image[qr_image]|mime_in[qr_image,image/jpeg,image/png,image/webp]|ext_in[qr_image,jpg,jpeg,png,webp]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->channelData($type);
        if ($type === 'qris') {
            $data['qr_image_path'] = $this->storeQrImage();
        }

        if (! $this->channels->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->channels->errors());
        }

        return redirect()->to('/admin/metode-bayar')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $channel = $this->channels->find($id);
        if (! $channel) {
            return redirect()->to('/admin/metode-bayar')->with('error', 'Metode pembayaran tidak ditemukan.');
        }

        return view('admin/payment_channels/form', ['channel' => $channel]);
    }

    public function update(int $id)
    {
        $channel = $this->channels->find($id);
        if (! $channel) {
            return redirect()->to('/admin/metode-bayar')->with('error', 'Metode pembayaran tidak ditemukan.');
        }

        $type = $this->request->getPost('type') === 'qris' ? 'qris' : 'bank';
        $qrImage = $this->request->getFile('qr_image');

        $rules = $this->channels->getValidationRules();
        if ($type === 'bank') {
            $rules['account_number'] = 'required|max_length[50]';
            $rules['account_holder'] = 'required|max_length[150]';
        } elseif ($qrImage && $qrImage->isValid()) {
            if (! validate_uploaded_image_dimensions($qrImage)) {
                return redirect()->back()->withInput()->with('errors', ['qr_image' => 'Dimensi gambar tidak valid atau melebihi batas.']);
            }
            $rules['qr_image'] = 'max_size[qr_image,2048]|is_image[qr_image]|mime_in[qr_image,image/jpeg,image/png,image/webp]|ext_in[qr_image,jpg,jpeg,png,webp]';
        } elseif (empty($channel['qr_image_path'])) {
            $rules['qr_image'] = 'uploaded[qr_image]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->channelData($type);
        $data['id'] = $id;

        if ($type === 'qris' && $qrImage && $qrImage->isValid()) {
            $data['qr_image_path'] = $this->storeQrImage();
        }

        if (! $this->channels->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->channels->errors());
        }

        if (isset($data['qr_image_path']) && ! empty($channel['qr_image_path']) && $channel['qr_image_path'] !== $data['qr_image_path']) {
            delete_public_asset($channel['qr_image_path']);
        }

        return redirect()->to('/admin/metode-bayar')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $channel = $this->channels->find($id);
        if ($channel) {
            $this->channels->delete($id);
            delete_public_asset($channel['qr_image_path'] ?? null);
        }

        return redirect()->to('/admin/metode-bayar')->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    private function channelData(string $type): array
    {
        return [
            'type'           => $type,
            'name'           => trim((string) $this->request->getPost('name')),
            'account_number' => $type === 'bank' ? trim((string) $this->request->getPost('account_number')) : null,
            'account_holder' => $type === 'bank' ? trim((string) $this->request->getPost('account_holder')) : null,
            'sort_order'     => (int) $this->request->getPost('sort_order'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    private function storeQrImage(): string
    {
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $image    = $this->request->getFile('qr_image');
        $filename = $image->getRandomName();
        $image->move(self::UPLOAD_PATH, $filename);

        return 'assets/uploads/payment_channels/' . $filename;
    }
}
```

- [ ] **Step 2: Write `admin/payment_channels/index.php`**

Copy the structure of `app/Views/admin/bongkar_payout_methods/index.php`
verbatim, but: title "Metode Pembayaran", empty-state icon `payments`,
empty-state copy "Belum ada metode pembayaran. Tambahkan rekening bank atau
QRIS agar customer bisa membayar.", table columns `Tipe` / `Nama` /
`No. Rekening` / `Urutan` / `Status` / `Aksi` (show `$channel['account_number'] ?: '—'`
in the No. Rekening column), links pointing at
`admin/metode-bayar/...` instead of `admin/bongkar-metode-pencairan/...`.

- [ ] **Step 3: Write `admin/payment_channels/form.php`**

```php
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $channel = $channel ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $channel ? 'Ubah' : 'Tambah' ?> Metode Pembayaran</h1>
    <p class="section-subtitle">Atur rekening bank atau QRIS yang tampil di halaman checkout &amp; invoice.</p>
</div>

<form method="post" action="<?= $channel ? base_url('admin/metode-bayar/' . $channel['id'] . '/ubah') : base_url('admin/metode-bayar/tambah') ?>" enctype="multipart/form-data" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="type" class="form-label">Tipe</label>
        <select id="type" name="type" class="form-input" onchange="document.getElementById('bank-fields').classList.toggle('hidden', this.value!=='bank'); document.getElementById('qris-fields').classList.toggle('hidden', this.value!=='qris');">
            <option value="bank" <?= old('type', $channel['type'] ?? 'bank') === 'bank' ? 'selected' : '' ?>>Transfer Bank</option>
            <option value="qris" <?= old('type', $channel['type'] ?? '') === 'qris' ? 'selected' : '' ?>>QRIS</option>
        </select>
    </div>
    <div>
        <label for="name" class="form-label">Nama Tampilan</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $channel['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" placeholder="BCA / QRIS Toko" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>

    <div id="bank-fields" class="space-y-5 <?= ($channel['type'] ?? 'bank') === 'qris' ? 'hidden' : '' ?>">
        <div>
            <label for="account_number" class="form-label">Nomor Rekening</label>
            <input type="text" id="account_number" name="account_number" value="<?= esc(old('account_number', $channel['account_number'] ?? '')) ?>" class="<?= isset($errors['account_number']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['account_number'])): ?><p class="form-error"><?= esc($errors['account_number']) ?></p><?php endif; ?>
        </div>
        <div>
            <label for="account_holder" class="form-label">Nama Pemilik Rekening</label>
            <input type="text" id="account_holder" name="account_holder" value="<?= esc(old('account_holder', $channel['account_holder'] ?? '')) ?>" class="<?= isset($errors['account_holder']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['account_holder'])): ?><p class="form-error"><?= esc($errors['account_holder']) ?></p><?php endif; ?>
        </div>
    </div>

    <div id="qris-fields" class="space-y-3 <?= ($channel['type'] ?? 'bank') === 'qris' ? '' : 'hidden' ?>">
        <label for="qr_image" class="form-label">Gambar QR</label>
        <?php if (! empty($channel['qr_image_path'])): ?>
            <img src="<?= base_url($channel['qr_image_path']) ?>" alt="QR saat ini" class="mb-2 h-32 w-32 rounded-lg border border-neutral-200 object-contain">
        <?php endif; ?>
        <input type="file" id="qr_image" name="qr_image" accept="image/png,image/jpeg,image/webp" class="<?= isset($errors['qr_image']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['qr_image'])): ?><p class="form-error"><?= esc($errors['qr_image']) ?></p><?php endif; ?>
        <p class="form-help">PNG/JPG/WEBP, maks 2MB. Kosongkan saat ubah data jika tidak mengganti gambar.</p>
    </div>

    <div>
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $channel['sort_order'] ?? 0)) ?>" class="form-input">
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $channel['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/metode-bayar') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
```

- [ ] **Step 4: Add routes**

In `app/Config/Routes.php`, inside the `admin` group (near the
`bongkar-metode-pencairan` routes), add:

```php
    $routes->get('metode-bayar', 'Admin\PaymentChannelController::index');
    $routes->get('metode-bayar/tambah', 'Admin\PaymentChannelController::create');
    $routes->post('metode-bayar/tambah', 'Admin\PaymentChannelController::store');
    $routes->get('metode-bayar/(:num)/ubah', 'Admin\PaymentChannelController::edit/$1');
    $routes->post('metode-bayar/(:num)/ubah', 'Admin\PaymentChannelController::update/$1');
    $routes->post('metode-bayar/(:num)/hapus', 'Admin\PaymentChannelController::delete/$1');
```

- [ ] **Step 5: Add sidebar link**

In `app/Views/layouts/admin.php`, in `$sidebarItems`, add after the
"Voucher Diskon" row:

```php
        ['label' => 'Metode Pembayaran', 'icon' => 'payments', 'href' => base_url('admin/metode-bayar'), 'match' => 'metode-bayar'],
```

- [ ] **Step 6: Lint everything and manually verify**

Run: `php -l app/Controllers/Admin/PaymentChannelController.php app/Views/admin/payment_channels/index.php app/Views/admin/payment_channels/form.php app/Config/Routes.php app/Views/layouts/admin.php`

Log into `/admin/login`, visit `/admin/metode-bayar`, add one `bank` channel
and one `qris` channel (with a real small PNG), confirm both list rows show
correctly and the QR image renders in the edit form.

- [ ] **Step 7: Commit**

```bash
git add app/Controllers/Admin/PaymentChannelController.php app/Views/admin/payment_channels app/Config/Routes.php app/Views/layouts/admin.php
git commit -m "feat: add admin CRUD for bank/QRIS payment channels"
```

---

### Task 5: Checkout — select a payment channel

**Files:**
- Modify: `app/Controllers/OrderController.php` (`create()`, `store()`)
- Modify: `app/Views/checkout/form.php`
- Modify: `app/Views/catalog/partials/buy_mode.php` (Step 4 section)
- Modify: `app/Views/catalog/partials/interactive.php` (pay-method-card handler, submit handler)
- Modify: `app/Views/catalog/partials/overlays.php` (hidden form field)
- Modify: `app/Controllers/Home.php` (pass `$paymentChannels` to the homepage)

**Interfaces:**
- Consumes: `PaymentChannelModel::listActive()`.
- Produces: `orders.payment_channel_id` populated on every new order.

- [ ] **Step 1: `Home::index()` — pass channels to the homepage**

In `app/Controllers/Home.php`, add `use App\Models\PaymentChannelModel;` and
in the `view('catalog/index', [...])` array add:

```php
            'paymentChannels' => (new PaymentChannelModel())->listActive(),
```

- [ ] **Step 2: `OrderController::create()` — pass channels to the standalone checkout page**

```php
    public function create(int $productId): string
    {
        $product = $this->findActiveProduct($productId);
        session()->set('checkout_idempotency_token', bin2hex(random_bytes(32)));
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return view('checkout/form', [
            'title'   => 'Checkout - Ayong Store',
            'noindex' => true,
            'product' => $product,
            'paymentChannels' => (new \App\Models\PaymentChannelModel())->listActive(),
        ]);
    }
```

- [ ] **Step 3: `OrderController::store()` — validate channel, drop Midtrans**

Replace the whole method body. Remove the `$this->midtrans` property/constructor
arg and the `use App\Libraries\MidtransService;` import entirely (grep the
file for `midtrans` to catch every reference — constructor, property type,
`$this->midtrans->getSnapToken`, `$data['snap_token']`).

```php
    public function store(int $productId)
    {
        $product = $this->findActiveProduct($productId);
        $idempotencyToken = trim((string) $this->request->getPost('idempotency_token'));

        if ($idempotencyToken !== '') {
            $existingOrder = $this->orders->findByToken($idempotencyToken);
            if ($existingOrder) {
                return redirect()->to('/pesanan/' . $existingOrder['invoice_number']);
            }
        }

        if (! $this->validate($this->orders->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $paymentChannelId = (int) $this->request->getPost('payment_channel_id');
        $paymentChannel = $paymentChannelId > 0
            ? (new \App\Models\PaymentChannelModel())->where('is_active', 1)->find($paymentChannelId)
            : null;
        if (! $paymentChannel) {
            return redirect()->back()->withInput()->with('errors', ['payment_channel_id' => 'Silakan pilih metode pembayaran.']);
        }

        $subtotal    = Money::rupiah($product['sell_price']);
        $this->orders->releaseExpiredVoucherReservations();
        $voucherCode = trim((string) $this->request->getPost('voucher_code'));
        $voucher     = null;
        $discount    = 0;

        if ($voucherCode !== '') {
            $voucher = $this->vouchers->findValid($voucherCode, $subtotal);
            if (! $voucher) {
                return redirect()->back()->withInput()->with('errors', ['voucher_code' => 'Kode voucher tidak valid atau tidak berlaku.']);
            }
            $discount = $this->vouchers->calculateDiscount($voucher, $subtotal);
        }

        $data = [
            'invoice_number'        => $this->orders->generateInvoiceNumber(),
            'product_id'            => $product['id'],
            'product_name_snapshot' => $product['name'],
            'nominal_snapshot'      => $product['nominal'],
            'price_snapshot'        => $subtotal,
            'game_id'               => $this->request->getPost('game_id'),
            'whatsapp_number'       => $this->request->getPost('whatsapp_number'),
            'voucher_id'            => $voucher['id'] ?? null,
            'voucher_reserved'      => $voucher ? 1 : 0,
            'voucher_committed'     => 0,
            'voucher_reserved_until' => $voucher ? date('Y-m-d H:i:s', time() + 900) : null,
            'discount_amount'       => $discount,
            'total_amount'          => $subtotal - $discount,
            'status'                => 'menunggu_pembayaran',
            'idempotency_token'     => $idempotencyToken !== '' ? $idempotencyToken : null,
            'payment_channel_id'    => $paymentChannel['id'],
        ];

        $this->orders->db->transStart();
        if (! $this->orders->save($data)) {
            $this->orders->db->transRollback();
            return redirect()->back()->withInput()->with('errors', $this->orders->errors());
        }

        if ($voucher && ! $this->vouchers->reserve((int) $voucher['id'])) {
            $this->orders->db->transRollback();
            return redirect()->back()->withInput()->with('errors', ['voucher_code' => 'Voucher baru saja habis digunakan.']);
        }
        $this->orders->db->transComplete();

        if (! $this->orders->db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Pesanan gagal disimpan. Silakan coba lagi.');
        }

        return redirect()->to('/pesanan/' . $data['invoice_number']);
    }
```

Also delete the now-unused `protected MidtransService $midtrans;` property
and constructor parameter/assignment at the top of the class.

- [ ] **Step 4: `checkout/form.php` — real payment method step**

Insert this block right after the "Step 2: Voucher Code" `<hr>` (before
"Action Submit"), renumbering the voucher step to 2 stays, submit becomes
step 3 conceptually (no need to renumber the printed "2" on voucher):

```php
            <hr class="border-neutral-100">

            <!-- Step 3: Payment Channel -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-primary text-white font-bold text-xs flex items-center justify-center">3</span>
                    <h3 class="font-bold text-sm text-on-surface font-sans">Metode Pembayaran</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    <?php foreach ($paymentChannels as $index => $channel): ?>
                        <label class="flex flex-col gap-1 rounded-xl border p-3 text-xs cursor-pointer transition-all <?= $index === 0 ? 'border-primary bg-primary/5' : 'border-neutral-200 hover:border-primary/60' ?>">
                            <input type="radio" name="payment_channel_id" value="<?= (int) $channel['id'] ?>" class="sr-only peer" <?= $index === 0 ? 'checked' : '' ?> required>
                            <span class="font-bold text-on-surface"><?= esc($channel['name']) ?></span>
                            <span class="text-neutral-500"><?= $channel['type'] === 'qris' ? 'QRIS' : esc($channel['account_number']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (isset($errors['payment_channel_id'])): ?>
                    <p class="text-danger text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> <?= esc($errors['payment_channel_id']) ?></p>
                <?php endif; ?>
            </div>
```

Change the submit button label from "Lanjut ke Pembayaran Snap" to "Buat
Pesanan".

- [ ] **Step 5: `buy_mode.php` — replace hardcoded payment cards**

Replace the entire "Group A: QRIS & E-Wallet" + "Group B: Virtual Account"
block (from `<!-- Group A: QRIS & E-Wallet -->` through the closing `</div>`
of Group B) with:

```php
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
    <?php foreach ($paymentChannels as $index => $channel): ?>
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-2xs cursor-pointer group <?= $index === 0 ? 'selected border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : '' ?>" data-channel-id="<?= (int) $channel['id'] ?>" data-method="<?= esc($channel['name']) ?>" type="button">
        <div class="text-xs font-black text-slate-900 group-hover:text-blue-600"><?= esc($channel['name']) ?></div>
        <div class="text-[10px] text-slate-500"><?= $channel['type'] === 'qris' ? 'Scan QRIS' : esc($channel['account_number']) ?></div>
      </button>
    <?php endforeach; ?>
  </div>
```

Also remove the `<span class="text-slate-500 font-mono text-[10px]">+Rp1.000 Biaya VA</span>`-style
fee labels and the `BEBAS BIAYA ADMIN (RP0)` header line above the grid — replace
the section header text with plain "Pilih Metode Pembayaran" (no fee copy,
since none of these channels charge a fee).

- [ ] **Step 6: `overlays.php` — hidden field**

In the `#backend-checkout-form`, add:

```html
  <input type="hidden" name="payment_channel_id" id="hidden-payment-channel-id">
```

- [ ] **Step 7: `interactive.php` — wire channel selection into state + submit**

Replace the `state` object's `payMethod: "QRIS Resmi", adminFee: 0,` with
`payMethod: "", payMethodChannelId: null,` (no more fake admin fee).

Replace the pay-method-card click handler body (around line 202-213):

```javascript
    const payMethodCards = document.querySelectorAll('.pay-method-card');
    payMethodCards.forEach(payCard => {
      payCard.addEventListener('click', () => {
        payMethodCards.forEach(p => {
          p.classList.remove('selected', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          p.classList.add('border-slate-200');
        });
        payCard.classList.add('selected', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        payCard.classList.remove('border-slate-200');
        state.payMethod = payCard.getAttribute('data-method') || '';
        state.payMethodChannelId = payCard.getAttribute('data-channel-id');
        updateReceiptUI();
      });
    });
```

(If the original handler referenced `state.adminFee` anywhere else —
`grandTotal` calc in `updateReceiptUI()`/`openCheckoutModal()` — change
`state.basePrice + state.adminFee - state.discount` to just
`state.basePrice - state.discount` in both places, and delete the
`calc-admin-fee`/admin-fee-related DOM update lines since there is no more
fee.)

In `openCheckoutModal()`, add a guard before the existing whatsapp check:

```javascript
      if (!state.payMethodChannelId) {
        alert('Silakan pilih metode pembayaran.');
        return;
      }
```

In the `btnSubmitPay` handler, add alongside the other hidden fields:

```javascript
      const hiddenPaymentChannel = document.getElementById('hidden-payment-channel-id');
      ...
      if (hiddenPaymentChannel) hiddenPaymentChannel.value = state.payMethodChannelId || '';
```

- [ ] **Step 8: Lint and manually verify**

Run: `php -l app/Controllers/OrderController.php app/Controllers/Home.php app/Views/checkout/form.php`

In the browser: pick a product on the homepage, confirm the payment method
grid shows real channels (from Task 4's seeded data), select one, fill
game id/WA, submit, confirm redirect to `/pesanan/{invoice}` with no PHP
errors. Also load `/checkout/{productId}` directly and submit that form.

- [ ] **Step 9: Commit**

```bash
git add app/Controllers/OrderController.php app/Controllers/Home.php app/Views/checkout/form.php app/Views/catalog/partials/buy_mode.php app/Views/catalog/partials/interactive.php app/Views/catalog/partials/overlays.php
git commit -m "feat: select a real payment channel at checkout instead of Midtrans"
```

---

### Task 6: Upload payment proof (customer side)

**Files:**
- Modify: `app/Controllers/OrderController.php` (add `uploadProof()`)
- Modify: `app/Config/Routes.php` (add route)
- Modify: `app/Views/checkout/invoice.php` (replace Midtrans button with channel display + upload form)
- Create: `tests/Feature/PaymentProofUploadTest.php`

**Interfaces:**
- Consumes: `OrderModel::findByInvoice()`, `upload_helper` validators.
- Produces: order transitions `menunggu_pembayaran` → `menunggu_verifikasi`
  with `payment_proof_path` set.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use App\Models\PaymentChannelModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\HTTP\Files\UploadedFile;

class PaymentProofUploadTest extends CIUnitTestCase
{
    use DatabaseTestTrait, FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests')->latest();
    }

    private function makeOrder(string $status = 'menunggu_pembayaran'): array
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Cat', 'slug' => 'cat-' . uniqid(), 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Prod', 'nominal' => '1',
            'cost_price' => 10, 'sell_price' => 20, 'is_active' => 1,
        ]);
        $channelModel = new PaymentChannelModel();
        $channelId = $channelModel->insert(['type' => 'bank', 'name' => 'BCA', 'account_number' => '123', 'account_holder' => 'Test', 'is_active' => 1]);
        $orderModel = new OrderModel();
        $orderModel->insert([
            'invoice_number' => 'INVPROOF' . uniqid(), 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Prod', 'nominal_snapshot' => '1', 'price_snapshot' => 20,
            'game_id' => '123', 'whatsapp_number' => '0811111111', 'total_amount' => 20,
            'payment_channel_id' => $channelId, 'status' => $status,
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');

        return $orderModel->find($orderModel->getInsertID());
    }

    public function testUploadProofMovesOrderToMenungguVerifikasi(): void
    {
        $order = $this->makeOrder();
        $file = new UploadedFile(
            SUPPORTPATH . 'Images/circle.png',
            'bukti.png',
            'image/png',
            null,
            UPLOAD_ERR_OK,
            true
        );

        $result = $this->withSession()
            ->withHeaders([csrf_header() => csrf_hash()])
            ->call('post', 'pesanan/' . $order['invoice_number'] . '/bukti', [], ['proof' => $file]);

        $result->assertRedirect();
        $updated = (new OrderModel())->findByInvoice($order['invoice_number']);
        $this->assertSame('menunggu_verifikasi', $updated['status']);
        $this->assertNotEmpty($updated['payment_proof_path']);
    }

    public function testUploadProofRejectsWhenNotAwaitingPayment(): void
    {
        $order = $this->makeOrder('diproses');
        $file = new UploadedFile(SUPPORTPATH . 'Images/circle.png', 'bukti.png', 'image/png', null, UPLOAD_ERR_OK, true);

        $result = $this->withSession()
            ->withHeaders([csrf_header() => csrf_hash()])
            ->call('post', 'pesanan/' . $order['invoice_number'] . '/bukti', [], ['proof' => $file]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('error'));
        $unchanged = (new OrderModel())->findByInvoice($order['invoice_number']);
        $this->assertSame('diproses', $unchanged['status']);
    }
}
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php vendor/bin/phpunit --filter PaymentProofUploadTest`
Expected: FAIL (route/method doesn't exist yet — 404 on the POST).

If `SUPPORTPATH . 'Images/circle.png'` doesn't exist, check
`tests/_support/` for any existing small PNG fixture used elsewhere in the
suite (search: `grep -rl "UploadedFile(" tests/`); if none, drop a tiny
valid 1x1 PNG at `tests/_support/Images/circle.png` first (any real PNG
byte content — copy one from `public/assets` if available).

- [ ] **Step 3: Add the route**

In `app/Config/Routes.php`, replace the line
`$routes->get('pesanan/(:segment)', 'OrderController::invoice/$1', ['filter' => 'ratelimit:30:60']);`
with (add the new line right after it):

```php
$routes->get('pesanan/(:segment)', 'OrderController::invoice/$1', ['filter' => 'ratelimit:30:60']);
$routes->post('pesanan/(:segment)/bukti', 'OrderController::uploadProof/$1', ['filter' => 'ratelimit:10:60']);
```

- [ ] **Step 4: Implement `uploadProof()`**

Add to `app/Controllers/OrderController.php` (needs `helper('upload');` —
add it in the constructor alongside the existing property assignments):

```php
    public function uploadProof(string $invoiceNumber)
    {
        $order = $this->orders->findByInvoice($invoiceNumber);
        if (! $order) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($order['status'] !== 'menunggu_pembayaran') {
            return redirect()->to('/pesanan/' . $invoiceNumber)->with('error', 'Bukti pembayaran hanya bisa diunggah saat pesanan menunggu pembayaran.');
        }

        helper('upload');
        $proof = $this->request->getFile('proof');
        if (! $proof || ! $proof->isValid()) {
            return redirect()->to('/pesanan/' . $invoiceNumber)->with('error', 'File bukti transfer tidak valid.');
        }

        $rules = ['proof' => 'max_size[proof,5120]|ext_in[proof,jpg,jpeg,png,webp,pdf]|mime_in[proof,image/jpeg,image/png,image/webp,application/pdf]'];
        if (! $this->validate($rules)) {
            return redirect()->to('/pesanan/' . $invoiceNumber)->with('error', 'Bukti transfer harus berupa gambar/PDF maksimal 5MB.');
        }

        $uploadPath = FCPATH . 'assets/uploads/payment_proofs/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $filename = $proof->getRandomName();
        $proof->move($uploadPath, $filename);

        $this->orders->update($order['id'], [
            'payment_proof_path'        => 'assets/uploads/payment_proofs/' . $filename,
            'payment_proof_uploaded_at' => date('Y-m-d H:i:s'),
            'status'                    => 'menunggu_verifikasi',
        ]);

        return redirect()->to('/pesanan/' . $invoiceNumber)->with('success', 'Bukti transfer terkirim. Admin akan memverifikasi pembayaran Anda.');
    }
```

Also add `use CodeIgniter\Exceptions\PageNotFoundException;` if not already
imported (it already is, per the existing `invoice()` method).

- [ ] **Step 5: Run the test again to confirm it passes**

Run: `php vendor/bin/phpunit --filter PaymentProofUploadTest`
Expected: PASS (2 tests).

- [ ] **Step 6: Update `checkout/invoice.php`**

Remove the entire "Tombol Pembayaran Midtrans Snap" block (the
`<?php if ($order['status'] === 'menunggu_pembayaran' && !empty($order['snap_token'])): ?> ... <?php endif; ?>`
around the "Bayar Sekarang" button) and the whole trailing
`<?php if ($order['status'] === 'menunggu_pembayaran' && !empty($order['snap_token'])): ?> <script>...snap.pay...</script> <?php endif; ?>`
block at the bottom of the file (including the `snap.js` `<script>` tag).

Replace the removed button block with:

```php
        <?php if ($order['status'] === 'menunggu_pembayaran'): ?>
            <?php $channel = ! empty($order['payment_channel_id']) ? (new \App\Models\PaymentChannelModel())->find($order['payment_channel_id']) : null; ?>
            <?php if ($channel): ?>
                <div class="pt-2 space-y-3 border-t border-slate-100">
                    <p class="text-xs font-bold text-neutral-800">Transfer ke <?= esc($channel['name']) ?>:</p>
                    <?php if ($channel['type'] === 'qris' && ! empty($channel['qr_image_path'])): ?>
                        <img src="<?= base_url($channel['qr_image_path']) ?>" alt="QRIS <?= esc($channel['name']) ?>" class="mx-auto h-48 w-48 rounded-xl border border-slate-200 object-contain">
                    <?php else: ?>
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm">
                            <div class="font-mono font-bold text-neutral-900 text-base"><?= esc($channel['account_number']) ?></div>
                            <div class="text-neutral-600">a.n. <?= esc($channel['account_holder']) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('pesanan/' . $order['invoice_number'] . '/bukti') ?>" enctype="multipart/form-data" class="space-y-2">
                        <?= csrf_field() ?>
                        <label for="proof" class="block text-xs font-bold text-neutral-800">Upload Bukti Transfer</label>
                        <input type="file" id="proof" name="proof" accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full text-xs rounded-xl border border-slate-300 p-2.5" required>
                        <button type="submit" class="w-full py-3 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer border border-blue-700">
                            <span class="material-symbols-outlined text-[18px]">upload</span>
                            <span>Kirim Bukti Transfer</span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php elseif ($order['status'] === 'menunggu_verifikasi'): ?>
            <div class="pt-2 border-t border-slate-100">
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 font-semibold text-center">
                    Bukti transfer terkirim. Menunggu verifikasi admin.
                </div>
            </div>
        <?php endif; ?>
```

- [ ] **Step 7: Lint and manually verify**

Run: `php -l app/Controllers/OrderController.php app/Views/checkout/invoice.php app/Config/Routes.php`

In the browser, open an invoice with status `menunggu_pembayaran`, confirm
the channel details + upload form render, submit a real small image,
confirm it redirects back showing the "menunggu verifikasi" panel and the
print-receipt block (unaffected) still works.

- [ ] **Step 8: Commit**

```bash
git add app/Controllers/OrderController.php app/Config/Routes.php app/Views/checkout/invoice.php tests/Feature/PaymentProofUploadTest.php
git commit -m "feat: let customers upload payment proof for manual verification"
```

---

### Task 7: Admin payment verification

**Files:**
- Modify: `app/Controllers/Admin/OrderController.php` (add `verifyPayment()`, `rejectPayment()`)
- Modify: `app/Views/admin/orders/detail.php`
- Modify: `app/Config/Routes.php`
- Create: `tests/Feature/AdminPaymentVerificationTest.php`

**Interfaces:**
- Consumes: `VoucherModel::commitReservation()`/`releaseReservation()`
  (already exist, used previously by `MidtransController`),
  `WablasGateway::sendToAdminNewOrder()` (already exists).
- Produces: order transitions `menunggu_verifikasi` → `diproses` (approve)
  or → `menunggu_pembayaran` (reject).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use App\Models\PaymentChannelModel;
use App\Models\VoucherModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class AdminPaymentVerificationTest extends CIUnitTestCase
{
    use DatabaseTestTrait, FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests')->latest();
    }

    private function loginAsAdmin(): void
    {
        $admins = new \App\Models\AdminModel();
        $admins->db->query('PRAGMA foreign_keys = OFF');
        $id = $admins->insert(['name' => 'Owner', 'email' => 'owner+' . uniqid() . '@test.com', 'password' => password_hash('secret123', PASSWORD_DEFAULT), 'role' => 'owner', 'is_active' => 1]);
        $admins->db->query('PRAGMA foreign_keys = ON');
        session()->set(['admin_id' => $id, 'admin_role' => 'owner']);
    }

    private function makeOrderAwaitingVerification(): array
    {
        $categoryModel = new \App\Models\ProductCategoryModel();
        $categoryModel->db->query('PRAGMA foreign_keys = OFF');
        $categoryModel->insert(['name' => 'Cat', 'slug' => 'cat-' . uniqid(), 'is_active' => 1]);
        $productModel = new \App\Models\ProductModel();
        $productModel->insert([
            'category_id' => $categoryModel->getInsertID(), 'name' => 'Prod', 'nominal' => '1',
            'cost_price' => 10, 'sell_price' => 20, 'is_active' => 1,
        ]);
        $channelId = (new PaymentChannelModel())->insert(['type' => 'bank', 'name' => 'BCA', 'account_number' => '123', 'account_holder' => 'Test', 'is_active' => 1]);
        $voucherId = (new VoucherModel())->insert(['code' => 'V' . uniqid(), 'type' => 'nominal', 'value' => 5, 'min_purchase' => 0, 'quota' => 5, 'is_active' => 1]);
        $orderModel = new OrderModel();
        $id = $orderModel->insert([
            'invoice_number' => 'INVVER' . uniqid(), 'product_id' => $productModel->getInsertID(),
            'product_name_snapshot' => 'Prod', 'nominal_snapshot' => '1', 'price_snapshot' => 20,
            'game_id' => '123', 'whatsapp_number' => '0811111111', 'total_amount' => 15,
            'payment_channel_id' => $channelId, 'voucher_id' => $voucherId, 'voucher_reserved' => 1,
            'payment_proof_path' => 'assets/uploads/payment_proofs/fake.png',
            'status' => 'menunggu_verifikasi',
        ]);
        $categoryModel->db->query('PRAGMA foreign_keys = ON');

        return $orderModel->find($id);
    }

    public function testVerifyPaymentMovesToDiprosesAndCommitsVoucher(): void
    {
        $this->loginAsAdmin();
        $order = $this->makeOrderAwaitingVerification();

        $result = $this->withSession()->withHeaders([csrf_header() => csrf_hash()])->post('admin/pesanan/' . $order['id'] . '/verifikasi');

        $result->assertRedirect();
        $updated = (new OrderModel())->find($order['id']);
        $this->assertSame('diproses', $updated['status']);
        $this->assertSame(1, (int) $updated['voucher_committed']);
        $this->assertSame(0, (int) $updated['voucher_reserved']);
        $this->assertNotNull($updated['payment_verified_at']);
    }

    public function testRejectPaymentReturnsOrderToAwaitingPayment(): void
    {
        $this->loginAsAdmin();
        $order = $this->makeOrderAwaitingVerification();

        $result = $this->withSession()->withHeaders([csrf_header() => csrf_hash()])->post('admin/pesanan/' . $order['id'] . '/tolak');

        $result->assertRedirect();
        $updated = (new OrderModel())->find($order['id']);
        $this->assertSame('menunggu_pembayaran', $updated['status']);
        $this->assertNull($updated['payment_proof_path']);
    }
}
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php vendor/bin/phpunit --filter AdminPaymentVerificationTest`
Expected: FAIL (routes don't exist — 404).

If `App\Models\AdminModel` isn't the actual admin model class name, check
`app/Models/` for the real one (search: `grep -l "admins'" app/Models/*.php`)
and use that instead.

- [ ] **Step 3: Add routes**

In `app/Config/Routes.php`, inside the `admin` group, near
`pesanan/(:num)/selesai`, add:

```php
    $routes->post('pesanan/(:num)/verifikasi', 'Admin\OrderController::verifyPayment/$1');
    $routes->post('pesanan/(:num)/tolak', 'Admin\OrderController::rejectPayment/$1');
```

- [ ] **Step 4: Implement the two actions**

Add to `app/Controllers/Admin/OrderController.php` (needs
`use App\Models\PaymentChannelModel;` and `use App\Models\VoucherModel;`,
plus `use App\Helpers\...` isn't needed — `delete_public_asset` comes from
the `upload` helper, so add `helper('upload');` in the constructor):

```php
    public function verifyPayment(int $id)
    {
        $order = $this->orders->find($id);
        if (! $order || $order['status'] !== 'menunggu_verifikasi') {
            return redirect()->back()->with('error', 'Hanya pesanan berstatus Menunggu Verifikasi yang dapat dikonfirmasi.');
        }

        $adminId = (int) session()->get('admin_id');
        $voucherModel = new VoucherModel();

        $this->orders->db->transStart();
        if (! (int) $order['voucher_committed'] && ! empty($order['voucher_id'])) {
            if (! $voucherModel->commitReservation((int) $order['voucher_id'])) {
                $this->orders->db->transRollback();
                return redirect()->back()->with('error', 'Gagal mengonfirmasi voucher pesanan ini.');
            }
        }
        $this->orders->update($id, [
            'status'               => 'diproses',
            'voucher_reserved'     => 0,
            'voucher_committed'    => ! empty($order['voucher_id']) ? 1 : (int) $order['voucher_committed'],
            'voucher_reserved_until' => null,
            'payment_verified_by'  => $adminId,
            'payment_verified_at'  => date('Y-m-d H:i:s'),
        ]);
        $this->orders->db->transComplete();

        if (! $this->orders->db->transStatus()) {
            return redirect()->back()->with('error', 'Pesanan gagal diverifikasi.');
        }

        $claimed = $this->orders->builder()->where('id', $id)->where('wablas_notification_claimed', 0)->update(['wablas_notification_claimed' => 1]);
        if ($claimed && $this->orders->db->affectedRows() === 1) {
            $this->wablas->sendToAdminNewOrder($this->orders->find($id));
        }

        return redirect()->to('/admin/pesanan/' . $id)->with('success', 'Pembayaran dikonfirmasi, pesanan masuk status Diproses.');
    }

    public function rejectPayment(int $id)
    {
        $order = $this->orders->find($id);
        if (! $order || $order['status'] !== 'menunggu_verifikasi') {
            return redirect()->back()->with('error', 'Hanya pesanan berstatus Menunggu Verifikasi yang dapat ditolak.');
        }

        helper('upload');
        delete_public_asset($order['payment_proof_path'] ?? null);

        $this->orders->update($id, [
            'status'             => 'menunggu_pembayaran',
            'payment_proof_path' => null,
            'payment_proof_uploaded_at' => null,
        ]);

        return redirect()->to('/admin/pesanan/' . $id)->with('warning', 'Bukti transfer ditolak, customer dapat mengunggah ulang.');
    }
```

- [ ] **Step 5: Run the test again to confirm it passes**

Run: `php vendor/bin/phpunit --filter AdminPaymentVerificationTest`
Expected: PASS (2 tests).

- [ ] **Step 6: Add the verification UI to `admin/orders/detail.php`**

Replace the `<?php if ($order['status'] === 'diproses'): ?> ... <?php elseif ($order['status'] === 'selesai'): ?> ... <?php else: ?> ... <?php endif; ?>`
block with one more branch prepended:

```php
        <?php if ($order['status'] === 'menunggu_verifikasi'): ?>
            <?php if (! empty($order['payment_proof_path'])): ?>
                <div class="mt-6 space-y-2">
                    <p class="text-sm font-semibold text-neutral-700">Bukti Transfer</p>
                    <a href="<?= base_url($order['payment_proof_path']) ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?= base_url($order['payment_proof_path']) ?>" alt="Bukti transfer" class="max-h-72 w-full rounded-lg border border-neutral-200 object-contain">
                    </a>
                </div>
            <?php endif; ?>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <form method="post" action="<?= base_url('admin/pesanan/' . $order['id'] . '/verifikasi') ?>" data-confirm="Konfirmasi pembayaran pesanan ini?" data-confirm-title="Konfirmasi Pembayaran" data-confirm-button="Ya, konfirmasi">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary w-full justify-center">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Konfirmasi
                    </button>
                </form>
                <form method="post" action="<?= base_url('admin/pesanan/' . $order['id'] . '/tolak') ?>" data-confirm="Tolak bukti transfer ini? Customer akan diminta upload ulang." data-confirm-title="Tolak Bukti" data-confirm-button="Ya, tolak">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary w-full justify-center">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Tolak
                    </button>
                </form>
            </div>
        <?php elseif ($order['status'] === 'diproses'): ?>
```

(the existing `diproses`/`selesai`/`else` branches stay unchanged after this)

- [ ] **Step 7: Lint and manually verify**

Run: `php -l app/Controllers/Admin/OrderController.php app/Views/admin/orders/detail.php app/Config/Routes.php`

In the browser: upload a proof as a customer (Task 6), open that order in
`/admin/pesanan/{id}`, confirm the proof image + Konfirmasi/Tolak buttons
show, click Konfirmasi, confirm status becomes Diproses and the existing
"Tandai Selesai" button now appears.

- [ ] **Step 8: Commit**

```bash
git add app/Controllers/Admin/OrderController.php app/Views/admin/orders/detail.php app/Config/Routes.php tests/Feature/AdminPaymentVerificationTest.php
git commit -m "feat: admin can verify or reject uploaded payment proof"
```

---

### Task 8: Remove Midtrans entirely

**Files:**
- Delete: `app/Controllers/MidtransController.php`
- Delete: `app/Libraries/MidtransService.php`
- Delete: `tests/Feature/MidtransWebhookTest.php`
- Modify: `app/Config/Routes.php` (remove the webhook route)
- Modify: `tests/Feature/CheckoutTest.php` (seed + send `payment_channel_id`)

**Interfaces:**
- Consumes: nothing new.
- Produces: no route/file left referencing Midtrans in `app/`.

- [ ] **Step 1: Remove the webhook route**

In `app/Config/Routes.php`, delete the line
`$routes->post('webhook/midtrans', 'MidtransController::webhook');`.

- [ ] **Step 2: Delete the dead files**

```bash
git rm app/Controllers/MidtransController.php app/Libraries/MidtransService.php tests/Feature/MidtransWebhookTest.php
```

- [ ] **Step 3: Update `CheckoutTest.php`**

In `tests/Feature/CheckoutTest.php`, in `setUp()`'s migration block stays
the same. Inside `testDoubleSubmitCheckoutCreatesOnlyOneOrder()`, after
creating the product and before the first `$this->get(...)` call, insert a
payment channel:

```php
        $channelId = (new \App\Models\PaymentChannelModel())->insert([
            'type' => 'bank', 'name' => 'BCA', 'account_number' => '123', 'account_holder' => 'Test', 'is_active' => 1,
        ]);
```

Add `"payment_channel_id" => $channelId,` to both `$result1` and `$result2`
POST payload arrays.

- [ ] **Step 4: Grep for any remaining reference**

Run: `grep -ril "midtrans" app/ tests/ --include=*.php`
Expected: no results (the `.env`/`env` sample keys and composer package are
fine to leave — they're outside `app/`/`tests/`).

- [ ] **Step 5: Run the full suite**

Run: `php vendor/bin/phpunit`
Expected: all tests pass (no more `MidtransWebhookTest` in the list).

- [ ] **Step 6: Commit**

```bash
git add app/Config/Routes.php tests/Feature/CheckoutTest.php
git commit -m "chore: remove Midtrans integration entirely"
```

---

### Task 9: Final verification pass

**Files:** none (verification only)

- [ ] **Step 1: Full lint sweep**

Run: `find app tests -name "*.php" | xargs -n1 php -l | grep -v "No syntax errors"`
Expected: no output.

- [ ] **Step 2: Full test suite**

Run: `php vendor/bin/phpunit`
Expected: all green.

- [ ] **Step 3: Rebuild CSS if any Tailwind classes changed**

Run: `npm run build:css`

- [ ] **Step 4: Manual end-to-end walkthrough**

1. Admin adds a bank channel and a QRIS channel (`/admin/metode-bayar`).
2. Customer buys a product from the homepage, picks a channel, submits.
3. Customer uploads a proof image on the invoice page.
4. Admin opens the order, sees the proof, clicks Konfirmasi.
5. Order shows Diproses; admin clicks "Tandai Selesai"; order shows Selesai.
6. Repeat steps 2-3, this time admin clicks Tolak; confirm the invoice page
   shows the upload form again (status back to `menunggu_pembayaran`).

- [ ] **Step 5: Commit any final fixups**

```bash
git add -A
git commit -m "chore: final verification pass for manual payment feature"
```
