# Changelog

All notable changes to `glennraya/xendivel` are documented in this file.

## v3.2.0 - 2026-06-06

### Added

- **QR Code payments.** Generate QR codes customers scan to pay with their banking or e-wallet app, with asynchronous confirmation via webhook. Built on Xendit's **Payments API** (`POST /payment_requests`), which is active by default — no separate channel activation required.
  - `Xendivel::createQrCode($request)` — create a QR payment request; returns the scannable value at `payment_method.qr_code.channel_properties.qr_string`.
  - `Xendivel::getQrCode($id)` — poll a payment request (`pr-…`); `status` flips `PENDING` → `SUCCEEDED` once paid.
  - `Xendivel::simulateQrPayment($id, $amount)` — test-mode simulation; accepts either the QR reference or a payment request id (`pr-…`, auto-resolved).
- **DYNAMIC and STATIC QR types** — `DYNAMIC` maps to `ONE_TIME_USE` (fixed amount), `STATIC` to `MULTIPLE_USE` (payer enters the amount).
- **Dedicated webhook event + listener** — publishable `App\Events\QrPaymentEvents` and `App\Listeners\QrPaymentWebhookListener` stubs; the listener handles the unified Payments webhook (`data.status === 'SUCCEEDED'`).
- **New config keys** — `qr_webhook_url`, `qr_channel_code` (default `QRPH`), `qr_currency` (default `PHP`); the channel/currency are also overridable per request.
- **Working demo** — a QR Code tab in all three bundled checkout templates (Blade, JSX, TSX): generate, render the QR image, poll status, and simulate a payment in test mode.

### Changed

- Checkout templates restructured for the third payment tab — each tab now shows only its own fields (card/e-wallet/QR no longer leak across tabs).

### Tests

- Added QR feature coverage (create dynamic/static, auto-id, channel/currency override, validation, get, simulate, `pr-…` resolution), demo-route tests, and a webhook auth-guard test.
- Split the monolithic payment test file into per-type files (`XendivelCardPaymentsTest`, `XendivelEwalletPaymentsTest`, `XendivelQrCodePaymentsTest`, `XendivelOtcPaymentsTest`).

### Upgrade Notes

- Republish config and assets to pick up the new QR keys, event/listener stubs, and updated checkout templates:
  ```bash
  php artisan vendor:publish --tag=xendivel --force
  ```
- Register the listener in `app/Providers/EventServiceProvider.php`:
  ```php
  QrPaymentEvents::class => [QrPaymentWebhookListener::class],
  ```
- In your Xendit dashboard, add a webhook under the **Payment** (`payment.succeeded`) callback type pointing at `qr_webhook_url` (default `/xendit/qr/webhook`).
- The default `/xendit/*` CSRF exclusion already covers the QR webhook path.
- If you use the React templates, rebuild assets (`npm run build`) and re-add your publishable key.

## v3.0.1 - 2026-04-18

### Changed

- Reverted invoice PDF rendering from Typeset.sh back to Spatie Browsershot.
- Kept the existing public invoice API unchanged for `Invoice::make()`, `save()`, `download()`, `template()`, `paperSize()`, `orientation()`, and `fileName()`.
- Replaced `xendivel.typesetsh.*` config with `xendivel.browsershot.*` runtime options.
- Updated installation docs to require Node 22+, npm, Puppeteer 23+, and Chrome/Chromium instead of Typeset.sh Composer credentials.

### Dependency Updates

- Added: `spatie/browsershot:^5.2.3`.
- Dropped: `typesetsh/typesetsh:^0.27.0`.
- Dropped: `typesetsh/font-noto-cjk-sub:^1.0`.

### Tests

- Updated invoice PDF tests to validate Browsershot-backed generation, paper sizes, orientation, downloads, and email attachments.
- Browser integration tests now skip cleanly when the local Browsershot runtime is unavailable.

## v3.0.0 - 2026-04-11

### Breaking Changes

- PDF generation now uses `typesetsh/typesetsh` core directly.
- `typesetsh/laravel-wrapper` is no longer a package dependency.
- `spatie/browsershot`-based PDF rendering is no longer supported.
- Custom invoice templates that rely on file or remote assets should review the new `xendivel.typesetsh.*` configuration surface.

### Added

- Official Laravel 13 support in package constraints and release documentation.
- New Typeset.sh resolver configuration keys under `xendivel.typesetsh`:
  - `allowed_directories`
  - `allowed_protocols`
  - `base_dir`
  - `cache_dir`
  - `timeout`
  - `download_limit`
- PDF tests for resolver-backed allowed directory resources and safe handling of blocked resource paths.
- New upgrade guide: `UPGRADE.md`.

### Changed

- README installation and support matrix documentation aligned to Laravel 13 support.
- README no longer references `typesetsh/laravel-wrapper`.
- Package tests bootstrap no longer registers the Typeset wrapper service provider.

### Dependency Updates (from v2.1.1)

- Dropped: `spatie/browsershot` (`^5.0` in `v2.1.1`) in favor of Typeset.sh-based PDF rendering.
- Added: `typesetsh/typesetsh:^0.27.0` and `typesetsh/font-noto-cjk-sub:^1.0`.
- Upgraded: `pestphp/pest` from `^2.31` to `^4.0`.
- Upgraded: `orchestra/testbench` from `^8.0|^9.0` to `^10.0|^11.0`.
- Expanded support constraints:
  - PHP from `~8.2|~8.3|~8.4` to `~8.2|~8.3|~8.4|~8.5`.
  - Laravel from `^10|^11|^12` to `^10|^11|^12|^13`.

### Support Matrix

- PHP: `8.2`, `8.3`, `8.4`, `8.5`
- Laravel: `10`, `11`, `12`, `13`
