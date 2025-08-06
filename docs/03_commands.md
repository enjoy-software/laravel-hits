# 指令使用說明

使用 Artisan 命令清理舊的點擊記錄。

## 清理指令

### 選項參數

**`--days`** - 指定保留天數

```bash
# 清理 365 天前的記錄（預設值）
php artisan laravel-hits:cleanup

# 清理 30 天前的記錄
php artisan laravel-hits:cleanup --days=30

# 清理 1 年前的記錄
php artisan laravel-hits:cleanup --days=365
```
