# Laravel Console List Columns

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-console-list-columns)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-console-list-columns)
[![License](https://img.shields.io/packagist/l/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-console-list-columns)
[![Code Quality](https://img.shields.io/scrutinizer/g/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://scrutinizer-ci.com/g/romanzipp/laravel-console-list-columns/?branch=master)
[![Build Status](https://img.shields.io/travis/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://travis-ci.org/romanzipp/laravel-console-list-columns)

List database tables columns & information.

## Installation

```
composer require romanzipp/laravel-console-list-columns
```

Or add `romanzipp/laravel-console-list-columns` to your `composer.json`

```
"romanzipp/laravel-console-list-columns": "^0.1"
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
$ php artisan db:columns
    {table}          Comma separated table names to print out
    {--connection=}  Specified database connection
    {--no-colors}    Don't use colors in output
    {--no-emojis}    Don't use emojis in output
```

![Preview](https://raw.githubusercontent.com/romanzipp/Laravel-Console-List-Columns/master/preview.png)
