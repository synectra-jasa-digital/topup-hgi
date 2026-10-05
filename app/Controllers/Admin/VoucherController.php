<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VoucherModel;

class VoucherController extends BaseController
{
    protected VoucherModel $vouchers;

    public function __construct()
    {
        $this->vouchers = new VoucherModel();
    }

    public function index()
    {
        return view('admin/vouchers/index', [
            'vouchers' => $this->vouchers->orderBy('created_at', 'DESC')->paginate(20),
            'pager'    => $this->vouchers->pager,
        ]);
    }

    public function create()
    {
        return view('admin/vouchers/form', ['voucher' => null]);
    }

    public function store()
    {
        if (! $this->validateVoucherInput()) {
            return redirect()->back()->withInput()->with('errors', $this->errors);
        }

        $data = $this->voucherData();

        if (! $this->vouchers->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->vouchers->errors());
        }

        return redirect()->to('/admin/voucher')->with('success', 'Voucher berhasil ditambahkan.');
    }

    /**
     * Tolak nilai voucher yang tidak masuk akal.
     *
     * Tanpa ini, percentage 100% membuat pesanan gratis dan nilai negatif
     * lolos ke database, karena calculateDiscount hanya membatasi ke subtotal.
     *
     * @var list<string>
     */
    private array $errors = [];

    private function validateVoucherInput(): bool
    {
        $this->errors = [];

        $type = (string) $this->request->getPost('type');

        if (! in_array($type, ['nominal', 'percentage'], true)) {
            $this->errors['type'] = 'Tipe diskon tidak valid.';
        }

        $value = $this->numericPost('value');

        if ($value === null) {
            $this->errors['value'] = 'Nilai diskon wajib diisi dengan angka.';
        } elseif ($type === 'percentage' && ($value <= 0 || $value > 100)) {
            $this->errors['value'] = 'Persentase diskon harus lebih dari 0 dan maksimal 100.';
        } elseif ($type === 'nominal' && $value < 0) {
            $this->errors['value'] = 'Nominal diskon tidak boleh negatif.';
        }

        $minPurchase = $this->numericPost('min_purchase');

        if ($minPurchase === null && trim((string) $this->request->getPost('min_purchase')) !== '') {
            $this->errors['min_purchase'] = 'Minimum pembelian harus berupa angka.';
        } elseif ($minPurchase !== null && $minPurchase < 0) {
            $this->errors['min_purchase'] = 'Minimum pembelian tidak boleh negatif.';
        }

        $maxDiscount = $this->numericPost('max_discount');

        if ($maxDiscount === null && trim((string) $this->request->getPost('max_discount')) !== '') {
            $this->errors['max_discount'] = 'Maksimum diskon harus berupa angka.';
        } elseif ($maxDiscount !== null && $maxDiscount < 0) {
            $this->errors['max_discount'] = 'Maksimum diskon tidak boleh negatif.';
        }

        $quota = $this->request->getPost('quota');

        if ($quota !== null && trim((string) $quota) !== '' && ! is_numeric(trim((string) $quota))) {
            $this->errors['quota'] = 'Kuota harus berupa angka.';
        } elseif ((int) ($quota ?: 0) < 0) {
            $this->errors['quota'] = 'Kuota tidak boleh negatif.';
        }

        $endDate = $this->request->getPost('end_date');
        $startDate = $this->request->getPost('start_date');

        if ($startDate && $endDate && $endDate < $startDate) {
            $this->errors['end_date'] = 'Tanggal berakhir tidak boleh lebih awal dari tanggal mulai.';
        }

        return $this->errors === [];
    }

    public function edit(int $id)
    {
        $voucher = $this->vouchers->find($id);
        if (! $voucher) {
            return redirect()->to('/admin/voucher')->with('error', 'Voucher tidak ditemukan.');
        }

        return view('admin/vouchers/form', ['voucher' => $voucher]);
    }

    public function update(int $id)
    {
        $voucher = $this->vouchers->find($id);
        if (! $voucher) {
            return redirect()->to('/admin/voucher')->with('error', 'Voucher tidak ditemukan.');
        }
        if (! $this->validateVoucherInput()) {
            return redirect()->back()->withInput()->with('errors', $this->errors);
        }

        $data       = $this->voucherData();
        $data['id'] = $id;

        if (! $this->vouchers->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->vouchers->errors());
        }

        return redirect()->to('/admin/voucher')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->vouchers->delete($id);
        return redirect()->to('/admin/voucher')->with('success', 'Voucher berhasil dihapus.');
    }

    private function voucherData(): array
    {
        return [
            'code'         => strtoupper(trim($this->request->getPost('code'))),
            'type'         => $this->request->getPost('type'),
            'value'        => $this->numericPost('value'),
            'min_purchase' => $this->numericPost('min_purchase') ?? 0.0,
            'max_discount' => $this->numericPost('max_discount'),
            'quota'        => (int) ($this->request->getPost('quota') ?: 0),
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /**
     * Baca field numerik dari POST, kembalikan null bila tidak valid.
     *
     * Cast (float) biasa mengubah "abc" menjadi 0 dan "-10" menjadi negatif
     * tanpa error. Nilai yang tidak dapat dipercaya harus menolak, bukan
     * diam-diam menjadi angka yang salah.
     */
    private function numericPost(string $field): ?float
    {
        $raw = $this->request->getPost($field);

        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }

        if (! is_numeric(trim((string) $raw))) {
            return null;
        }

        return (float) trim((string) $raw);
    }
}
