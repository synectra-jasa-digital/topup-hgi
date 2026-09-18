# Manual Bank Transfer & QRIS Payment (replaces Midtrans)

## Why
Midtrans (automated Snap gateway) is removed. All payment now goes through
admin-managed bank accounts and/or QRIS images, verified manually by the
admin from an uploaded proof-of-transfer image.

## Data model

**New table `payment_channels`**
- `id`, `type` (`bank` | `qris`), `name` (e.g. "BCA", "QRIS"),
  `account_number` (nullable — bank only), `account_holder` (nullable — bank
  only), `qr_image_path` (nullable — qris only), `sort_order`, `is_active`,
  timestamps.

**`orders` table changes**
- Drop `snap_token` (dead).
- Add `payment_channel_id` (nullable FK, set null on delete) — chosen at
  checkout.
- Add `payment_proof_path`, `payment_proof_uploaded_at` — set when customer
  uploads proof.
- Add `payment_verified_by`, `payment_verified_at` — set when admin
  approves.

**`order_payments` table**: dropped entirely (Midtrans-only, no longer
written by anything). `OrderPaymentModel` removed.

**Order status flow**
`menunggu_pembayaran` → (customer uploads proof) → `menunggu_verifikasi` →
(admin approves) → `diproses` → (admin marks done, unchanged) → `selesai`.
Admin can reject from `menunggu_verifikasi` back to `menunggu_pembayaran`
(customer re-uploads). `gagal`/`dibatalkan` unchanged/untouched.

Voucher commit/release logic (previously in the Midtrans webhook) moves to
the new admin verify/reject actions, reusing `VoucherModel::commitReservation`
/`releaseReservation` exactly as before. The `wablas_notification_claimed`
atomic-claim pattern for "notify admin of new paid order" also moves to the
verify action.

## Customer flow
1. Checkout (`buy_mode.php` step 4 and `checkout/form.php` step 2): replace
   the hardcoded QRIS/e-wallet/VA cards with real `payment_channels` from DB.
   No more per-method "admin fee" gimmick (it was decorative before, tied to
   nothing real) — every channel is free.
2. `OrderController::store()`: validate the chosen channel is active, save
   `payment_channel_id`, no Midtrans call, redirect to invoice as before.
3. Invoice page, status `menunggu_pembayaran`: show the chosen channel's QR
   image or account number/holder to transfer to, plus an upload-proof form
   (image or PDF, rate-limited endpoint).
4. Invoice page, status `menunggu_verifikasi`: show "menunggu verifikasi
   admin", no upload form (can't resubmit until admin rejects).

## Admin flow
1. New **Metode Pembayaran** admin section (CRUD, mirrors
   `BongkarPayoutMethodController`): manage bank accounts and QRIS entries,
   with image upload for the QR (reusing the existing `upload_helper`
   validation pattern used by banners/products).
2. Order detail page: when status is `menunggu_verifikasi`, show the
   uploaded proof image + channel used, with **Konfirmasi** (→ `diproses`,
   commits voucher, sends the existing "new order" WA to admin) and
   **Tolak** (→ `menunggu_pembayaran`, clears proof so the customer can
   retry) actions.

## Removed
- `MidtransController`, `MidtransService`, route `webhook/midtrans`.
- Snap.js script + "Bayar Sekarang" Midtrans button in `invoice.php`.
- `tests/Feature/MidtransWebhookTest.php` (tests deleted code; was already
  flaky/pre-existing-broken independent of this change).
- The `midtrans/midtrans-php` composer package is left installed but unused
  — removing a dependency is a separate, explicitly-confirmed action.

## Out of scope (YAGNI)
- No payment-proof history/audit table (one proof per order is enough for
  now; the order row itself carries the single current proof).
- No customer-facing WA notification on verification (only the existing
  admin-new-order and order-completed notifications are wired).
- No generic order cancellation flow beyond what exists today.
