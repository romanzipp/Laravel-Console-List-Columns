# Laravel Console List Columns

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-console-list-columns)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-console-list-columns)
[![License](https://img.shields.io/packagist/l/romanzipp/laravel-console-list-columns.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-console-list-columns)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/Laravel-Console-List-Columns/Tests?style=flat-square)](https://github.com/romanzipp/Laravel-Console-List-Columns/actions)

When working with many database migrations you can quickly lose the overview about table structures.
With this package you can **get a simple overview about table and column information** on your command line.

## Installation

```
composer require romanzipp/laravel-console-list-columns
```

## Configuration

Copy configuration to config folder:

```
php artisan vendor:publish --provider="romanzipp\ColumnList\Providers\ColumnListProvider"
```

## Usage

```
php artisan db:cols
    {table}          Comma separated table names to print out
    {--connection=}  Specified database connection
    {--no-colors}    Don't use colors in output
    {--no-emojis}    Don't use emojis in output
```

```
php artisan db:cols users
```

![Preview](https://raw.githubusercontent.com/romanzipp/Laravel-Console-List-Columns/master/preview.png)

## Testing

```
./vendor/bin/phpunit
```
