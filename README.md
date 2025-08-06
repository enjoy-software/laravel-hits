# Laravel Hits

一個簡潔且高效的 Laravel 套件，專注於手動記錄模型瀏覽次數和訪問統計。

## 📚 文檔

- [基本使用方法](./docs/basic-usage.md) - 快速開始使用 Laravel Hits
- [控制器範例](./docs/controller-examples.md) - 詳細的控制器使用範例
- [完整文檔](./docs/README.md) - 查看所有文檔

## 功能特色

- 🔥 追蹤任何 Eloquent 模型的瀏覽次數
- 📊 記錄詳細的訪問資訊（IP、User Agent、來源等）
- 🛡️ 內建防機器人和冷卻機制
- 📈 豐富的統計方法和查詢範圍
- 💡 簡單直觀的手動記錄方式
- ⚡ 高效能查詢和資料庫索引
- 🧹 Artisan 命令清理舊記錄

## 安裝

使用 Composer 安裝套件：

```bash
composer require enjoy-software/laravel-hits
```

（可選）發布遷移檔案：

```bash
php artisan vendor:publish --tag=laravel-hits-migrations
```

執行遷移：

```bash
php artisan migrate
```

（可選）發布配置檔案：

```bash
php artisan vendor:publish --tag=laravel-hits-config
```

## 基本使用

### 1. 為模型添加 Hittable Trait

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EnjoySoftware\LaravelHits\Traits\Hittable;

class Post extends Model
{
    use Hittable;
    
    // ... 你的模型程式碼
}
```

### 2. 記錄瀏覽次數

```php
// 手動記錄瀏覽次數
$post = Post::find(1);
$hit = $post->recordHit();

// 帶額外資料記錄
$hit = $post->recordHit([
    'method' => 'POST',
    'custom_data' => 'any value'
]);

// recordHit 方法會自動處理機器人檢測和冷卻時間檢查
// 如果是機器人或在冷卻時間內，會返回 null
if ($hit) {
    // 成功記錄
} else {
    // 因為機器人檢測或冷卻時間而跳過記錄
}
```

## 統計方法

### 基本統計

```php
$post = Post::find(1);

// 總瀏覽次數
$totalHits = $post->getHitsCount();

// 唯一 IP 瀏覽次數
$uniqueHits = $post->getUniqueVisitorsCount();

// 今日瀏覽次數
$todayHits = $post->getTodayHitsCount();

// 本週瀏覽次數
$weekHits = $post->getThisWeekHitsCount();

// 本月瀏覽次數
$monthHits = $post->getThisMonthHitsCount();
```

### 進階統計

```php
// 取得完整統計資料
$stats = [
    'total_hits' => $post->getHitsCount(),
    'unique_hits' => $post->getUniqueVisitorsCount(),
    'today_hits' => $post->getTodayHitsCount(),
    'week_hits' => $post->getThisWeekHitsCount(),
    'month_hits' => $post->getThisMonthHitsCount(),
    'unique_visitors' => $post->getUniqueVisitorsCount(),
];

// 檢查是否被特定 IP 瀏覽過
$hasBeenHit = $post->hasBeenHitBy('192.168.1.1');

// 取得熱門文章（依瀏覽次數排序）
$popularPosts = Post::getPopular(10);
```

### 查詢範圍

```php
use EnjoySoftware\LaravelHits\Models\Hit;

// 特定模型的瀏覽記錄
$hits = Hit::fromModel($post)->get();

// 特定 IP 的瀏覽記錄
$hits = Hit::fromIp('192.168.1.1')->get();

// 特定使用者的瀏覽記錄
$hits = Hit::fromUser(123)->get();

// 日期範圍查詢
$hits = Hit::betweenDates('2024-01-01', '2024-01-31')->get();

// 今日瀏覽記錄
$hits = Hit::today()->get();

// 本週瀏覽記錄
$hits = Hit::thisWeek()->get();

// 本月瀏覽記錄
$hits = Hit::thisMonth()->get();
```

## 配置選項

### 主要配置

```php
// config/laravel-hits.php

return [
    // 是否記錄錯誤到日誌
    'log_errors' => true,
    
    // 只追蹤已認證使用者
    'authenticated_only' => false,
    
    // 忽略機器人請求
    'ignore_bots' => true,
    
    // 冷卻時間（分鐘）- 同一 IP 重複瀏覽的最小間隔
    'cooldown_minutes' => 5,
];
```

## 進階用法

### 自定義瀏覽數據

```php
$post->recordHit([
    'user_id' => 123,
    'ip' => '192.168.1.100',
    'custom_field' => 'custom_value',
]);
```

### 使用服務類別

```php
use App\Models\Post;

class PostController extends Controller
{
    public function show(Post $post)
    {
        // 記錄瀏覽
        $post->recordHit();
        
        // 取得統計 (可選)
        $stats = [
            'total_hits' => $post->getHitsCount(),
            'today_hits' => $post->getTodayHitsCount(),
        ];
        
        return view('posts.show', compact('post', 'stats'));
    }
}```

### 清理舊記錄

使用 Artisan 命令清理舊的點擊記錄：

```bash
# 清理 365 天前的記錄（預設值）
php artisan laravel-hits:cleanup

# 清理 30 天前的記錄
php artisan laravel-hits:cleanup --days=30

# 清理 1 年前的記錄
php artisan laravel-hits:cleanup --days=365
```

你可以將此命令添加到 Laravel 排程中自動執行：

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('laravel-hits:cleanup --days=365')
        ->monthly(); // 每月執行一次
}
```

## 資料庫結構

套件會創建一個 `hits` 資料表：

```sql
CREATE TABLE hits (
    id BIGINT UNSIGNED PRIMARY KEY,
    hittable_type VARCHAR(255),
    hittable_id BIGINT UNSIGNED,
    ip VARCHAR(45),
    user_agent TEXT,
    user_id BIGINT UNSIGNED NULL,
    referer VARCHAR(500) NULL,
    method VARCHAR(10) DEFAULT 'GET',
    url TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_hittable (hittable_type, hittable_id),
    INDEX idx_ip_created (ip, created_at),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_created (created_at)
);
```

## 效能考量

1. **索引優化**：資料表包含多個索引以提高查詢效能
2. **冷卻機制**：避免短時間內重複記錄相同 IP 的瀏覽
3. **機器人過濾**：自動忽略常見的爬蟲和機器人
4. **批次清理**：定期清理舊記錄以維持效能

## 授權

MIT License

## 貢獻

歡迎提交 PR 或回報 Issue！

## 支援

如有問題請至 [GitHub Issues](https://github.com/enjoy-software/laravel-hits/issues) 回報。
