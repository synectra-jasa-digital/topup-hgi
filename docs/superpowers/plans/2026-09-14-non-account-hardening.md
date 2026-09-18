# Non-Account Hardening Implementation Plan

> **For agentic workers:** Execute task-by-task with regression tests and verification after each slice.

**Goal:** Harden Ayong Store outside admin account/login flows: money correctness, payment lifecycle, privacy, uploads, runtime security, reports, and backups.

**Architecture:** Keep CodeIgniter 4 MVC boundaries. Add small domain helpers/services for money, order access, voucher lifecycle, and notification idempotency; use additive migrations for existing installations. Preserve local development behavior while enabling secure production defaults through environment configuration.

**Tech Stack:** PHP 8.2+, CodeIgniter 4, MySQL/SQLite test database, PHPUnit, Midtrans Snap, Wablas, Tailwind.

**Spec:** `TODO.md` section “Hardening Non-Akun”.

## Global Constraints

- Do not change admin authentication/account behavior.
- Do not expose secrets in source, HTML, logs, or public responses.
- All monetary values are integer rupiah at business boundaries.
- Every external input is validated server-side.
- Existing tests must remain green.
- Use additive migrations and preserve existing order/customer data.

### Task 1: Money and voucher lifecycle
- Add integer-rupiah normalization and explicit rounding.
- Add voucher reservation/commit/release fields and methods.
- Update checkout/webhook/expiry paths and tests.

### Task 2: Checkout and webhook idempotency
- Add checkout request token and unique payment event constraints.
- Make duplicate and concurrent webhook notifications safe.
- Add double-submit and duplicate-webhook tests.

### Task 3: Invoice privacy and dynamic caching
- Add separate invoice access token.
- Mask customer identifiers on public invoice.
- Exclude invoice/admin/session pages from page cache.
- Add access-token and enumeration tests.

### Task 4: Public endpoint resilience
- Extend rate limits to checkout and invoice/status paths.
- Make limiter storage behavior safe and bounded.
- Add bongkar status-transition validation and notification state.

### Task 5: Upload hardening
- Enforce image dimensions and upload-directory execution restrictions.
- Remove replaced assets safely.
- Add upload regression coverage.

### Task 6: Production security
- Enable secure headers and HTTPS/CSP through production-safe configuration.
- Set database strictness/encryption and trusted host guidance.
- Document required environment variables.

### Task 7: Store settings integration
- Remove plaintext credential rendering/storage behavior.
- Make settings integration explicit and compatible with environment fallback.
- Implement maintenance mode filter and tests.

### Task 8: Reports and backups
- Replace date functions with sargable ranges.
- Stream backup rows, add retention and safe download behavior.
- Add report/backup regression coverage.

### Task 9: Final verification
- Run PHPUnit, PHP syntax checks, Composer validation, CSS build, and diff review.
- Update operational documentation and mark TODO items only after evidence.
