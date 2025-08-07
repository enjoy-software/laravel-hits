# 安裝說明

## 系統需求

- PHP 8.1 或更高版本
- Laravel 12.0 或更高版本

## 安裝步驟

使用 Composer 安裝套件：

```bash
composer require enjoy-software/laravel-hits
```

發布遷移檔案：

```bash
php artisan vendor:publish --tag=laravel-hits-migrations
```

執行遷移：

```bash
php artisan migrate
```

## 配置選項

（可選）發布配置檔案：

```bash
php artisan vendor:publish --tag=laravel-hits-config
```
