# Upgrade Guide

## Upgrading from v2 to v3

Xendivel `v3.0.0` is a major release focused on PDF rendering internals and Laravel 13 support.

## What Changed

- Xendivel now renders PDFs through `typesetsh/typesetsh` core directly.
- `typesetsh/laravel-wrapper` is no longer required by Xendivel.
- A new public configuration surface is available at `xendivel.typesetsh.*`.

## Migration Steps

1. Update your package dependency:

```bash
composer require glennraya/xendivel:^3.0
```

2. Keep your Typeset private repository and auth configured in the root Laravel application:

```bash
composer config repositories.typesetsh composer https://packages.typeset.sh
composer config -g http-basic.packages.typeset.sh "{PUBLIC_ID}" "{TOKEN}"
```

3. Republish or manually merge Xendivel config updates:

```bash
php artisan vendor:publish --tag=xendivel-config
```

4. Review `config/xendivel.php` and set resolver options for your templates when needed:

```php
// config/xendivel.php
return [
    'typesetsh' => [
        'allowed_directories' => [public_path()],
        'allowed_protocols' => ['http', 'https'],
        'base_dir' => '',
        'cache_dir' => storage_path('framework/cache/typesetsh'),
        'timeout' => 15,
        'download_limit' => 1024 * 1024 * 5,
    ],
];
```

## Notes for Custom Templates

- If your invoice template references local assets, ensure their parent directories are listed in `typesetsh.allowed_directories`.
- If your template downloads assets over the network, confirm the scheme is allowed in `typesetsh.allowed_protocols`.
- Orientation behavior (`portrait` / `landscape`) is unchanged and is still applied on the generated PDF document.

## Verification Checklist

```bash
composer validate --strict
./vendor/bin/pest --configuration phpunit.xml.dist tests/Unit/InvoicePdfTest.php
```
