# Laravel Console List Columns

List database tables columns & information.

**IN DEVELOPMENT**

## Installation

```
composer require romanzipp/laravel-console-list-columns
```

Or add `romanzipp/laravel-console-list-columns` to your `composer.json`

```
"romanzipp/laravellaravel-console-list-columns-twitch": "^0.1"
```

Run `composer install` to pull the latest version.

**If you use Laravel 5.5+ you are already done, otherwise continue:**

Add Service Provider to your `app.php` configuration file:

```php
romanzipp\ColumnList\Providers\ColumnListProvider::class,
```

## Configuration

Copy configuration to config folder:

```
$ php artisan vendor:publish --provider="romanzipp\ColumnList\Providers\ColumnListProvider"
```

## Usage

```
$ php artisan db:columns {--table=} {--connection=}
```

## TODO

- [ ] Add support for multiple database connections
- [ ] Fetch all tables if not explicitly given
- [ ] Add trailing table characters
