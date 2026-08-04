# AI 设置

后台路径：

`HimeDoll → AI 运营中心`

需要填写：

- API Base URL
- API Key
- 模型名称
- 超时时间

支持 OpenAI 兼容接口，例如：

- OpenAI
- OpenRouter
- 自定义中转接口
- 兼容 `/v1/chat/completions` 的服务

## 安全

API Key 保存在 WordPress 数据库，不写入 Git。
建议生产环境改为 `wp-config.php` 常量：

```php
define('HIMEDOLL_AI_API_KEY', 'your-key');
```
