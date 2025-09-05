# 升級到 1.2.2

## 從 1.2.1 或以前升級

### 主要變更

- 更新 `bot_user_agents` 配置

## 升級步驟

### `bot_user_agents` 新增 googleother

`config/laravel-hits.php`

```php
<?php

return [
    'bot_user_agents' => [
        'googleother',
    ],
];
```
