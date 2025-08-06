# 基本使用方法

這份文檔將引導你完成 Laravel Hits 套件的基本設定和使用。

## 模型設定

首先，在你想要追蹤瀏覽次數的模型中加入 `Hittable` trait：

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use EnjoySoftware\LaravelHits\Traits\Hittable;

class Post extends Model
{
    use Hittable;
    
    protected $fillable = ['title', 'content', 'author_id'];
}
```

## 手動記錄瀏覽次數

### 基本記錄

```php
// 在控制器中
public function show(Post $post)
{
    // 記錄一次瀏覽
    $post->recordHit();
    
    return view('posts.show', compact('post'));
}
```

### 自訂瀏覽資料

```php
$post->recordHit([
    'ip' => '192.168.1.100',
    'method' => 'GET',
    'referer' => 'https://example.com',
    'url' => 'https://example.com',
    'user_agent' => 'Custom User Agent',
    'user_id' => 22,
]);
```

## 取得統計資料

### 基本統計

```php
// 總瀏覽次數
$totalHits = $post->getHitsCount();

// 唯一訪客數
$uniqueHits = $post->getUniqueVisitorsCount();

// 今日瀏覽次數
$todayHits = $post->getTodayHitsCount();

// 本週瀏覽次數
$weekHits = $post->getThisWeekHitsCount();

// 本月瀏覽次數
$monthHits = $post->getThisMonthHitsCount();
```

## 查詢熱門內容

### 取得最熱門的文章

```php
// 取得前 10 篇最熱門的文章
$popularPosts = Post::getPopular(10);
```

### 取得趨勢內容

```php
// 取得過去 7 天最熱門的 5 篇文章
$trendingPosts = Post::getTrending(7, 5);
```

## 檢查瀏覽記錄

### 檢查 IP 是否瀏覽過

```php
$hasViewed = $post->hasBeenHitBy('192.168.1.1');

if ($hasViewed) {
    echo '這個 IP 已經瀏覽過這篇文章';
}
```
