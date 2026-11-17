# Changelog

All notable changes to `Essence` will be documented in this file.

## Version 4.0.3

- Security fix for Carbon 3 (Laravel 11+): `diffInMinutes()` now returns a signed value, so password recovery tokens and change-email requests never expired. Expiration checks now use the absolute difference.
- Adding regression tests for token expiration.

## Version 4.0.2

- Laravel 10+ compatibility: replacing the removed `Model::$dates` with `datetime` casts in `User`, `Device`, `DeviceLocationHistory` and `ChangeEmailRequest`.
- Adding `DateFormat::ISO_*` formats for `Carbon::isoFormat()`, equivalent to the `LOCALIZED_*` ones, which are now deprecated (`strftime`/`formatLocalized()` are removed in Carbon 3).

## Version 4.0.1

- Adding support for Laravel 9, 10, 11 and 12 (`illuminate/support ~7|~8|^9|^10|^11|^12`).
- Dev: allowing `orchestra/testbench` up to ^10 and `phpunit/phpunit` up to ^11.
- Dev: removing unused `sempro/phpunit-pretty-print` (incompatible with PHPUnit 10+).

## Version 3.0.0

- Library was rewrote using [laravel auth](https://laravel.com/docs/7.x/authentication)
- Adding devices and applications
- Changing sentinel to [laravel auth](https://laravel.com/docs/7.x/authentication) to improve 
compatibility and prevent the use of another dependencies, roles and permissions were 
custom implemented worked similar to sentinel, these permissions were called **Claims**.
- Adding notifications by default and added a little trait and interface to help us to 
categorize and show notifications type. 
- Removing states, municipalities and suburbs, this does not should stay in this library. 
- Adding change  logs, contributing and first changes in readme.md  

## Version 2.0.*

- Use infrastructure based on [cartalyst/sentinel](https://cartalyst.com/manual/sentinel/4.x) authentication.  

