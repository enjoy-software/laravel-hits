# Laravel Hits 文檔

這裡包含了 Laravel Hits 套件的詳細使用範例和指南。

## 文檔目錄

- [基本使用方法](./basic-usage.md) - 開始使用 Laravel Hits

## 快速範例

### 模型設定

```php
use EnjoySoftware\LaravelHits\Traits\Hittable;

class Post extends Model
{
    use Hittable;
}
```

### 記錄瀏覽次數

```php
$post = Post::find(1);
$post->recordHit();
```

### 取得統計資料

```php
$totalHits = $post->getHitsCount();
$popularPosts = Post::getPopular(10);
```
