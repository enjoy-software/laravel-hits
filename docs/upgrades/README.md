# 升級指南

## 說明

從當前版本依序往上參考對應的文件進行升級。

## 常用指令

### 更新版本

```bash
composer update enjoy-software/laravel-hits
```

### 強制更新配置檔

請記得備份，以免資料遺失。

```bash
php artisan vendor:publish --tag=laravel-hits-config --force
```
