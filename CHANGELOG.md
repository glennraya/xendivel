# Changelog

All notable changes to `glennraya/xendivel` are documented in this file.

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
