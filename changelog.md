# Changelog

All notable changes to `Essence` will be documented in this file.

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

