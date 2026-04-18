# Upgrade Guide

## Upgrading from v2 to v3

Xendivel `v3` keeps the public invoice API stable while updating Laravel/PHP support and standardizing PDF rendering on Spatie Browsershot.

## What Changed

- Xendivel renders invoice PDFs with `spatie/browsershot`.
- Typeset.sh is no longer required for PDF generation.
- Xendivel exposes Browsershot runtime options at `xendivel.browsershot.*`.
- Laravel 13 is officially supported while Laravel 10 through 12 remain supported.

## Migration Steps

1. Update your package dependency:

```bash
composer require glennraya/xendivel:^3.0
```

2. Install the browser runtime required by Browsershot in the root Laravel application:

```bash
npm install puppeteer
npx puppeteer browsers install chrome
```

3. Republish or manually merge Xendivel config updates:

```bash
php artisan vendor:publish --tag=xendivel-config
```

4. Review `config/xendivel.php` and set Browsershot options only when your environment needs custom binary paths or browser flags:

```php
// config/xendivel.php
return [
    'browsershot' => [
        'timeout' => 60,
        'node_binary' => env('XENDIVEL_BROWSERSHOT_NODE_BINARY'),
        'npm_binary' => env('XENDIVEL_BROWSERSHOT_NPM_BINARY'),
        'chrome_path' => env('XENDIVEL_BROWSERSHOT_CHROME_PATH'),
        'node_module_path' => env('XENDIVEL_BROWSERSHOT_NODE_MODULE_PATH'),
        'include_path' => env('XENDIVEL_BROWSERSHOT_INCLUDE_PATH'),
        'content_url' => env('XENDIVEL_BROWSERSHOT_CONTENT_URL', env('APP_URL')),
        'no_sandbox' => env('XENDIVEL_BROWSERSHOT_NO_SANDBOX', false),
    ],
];
```

## Notes for Custom Templates

- `paperSize()` still supports `A4`, `Letter`, and `Legal`; invalid or empty values fall back to `A4`.
- `orientation()` still accepts `portrait` and `landscape`; any other value falls back to `portrait`.
- If invoice templates use relative images, CSS, or font paths, set `XENDIVEL_BROWSERSHOT_CONTENT_URL` to the app URL that Chrome can access.
- Linux containers or restricted servers may need `XENDIVEL_BROWSERSHOT_NO_SANDBOX=true`.

## Verification Checklist

```bash
composer validate --strict
composer test
./vendor/bin/pest --configuration phpunit.xml.dist tests/Unit/InvoicePdfTest.php
```
