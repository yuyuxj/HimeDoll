# v5.0 企业功能设置

后台新增：

- HimeDoll → 企业设置
- HimeDoll → 企业总览

## 会员等级

默认：

- Bronze：0
- Silver：累计消费 300,000 日元
- Gold：累计消费 800,000 日元
- VIP：累计消费 1,500,000 日元

## 积分

默认每消费 100 日元获得 1 积分。

## REST API

生产环境建议在 `wp-config.php` 中设置：

```php
define('HIMEDOLL_ENTERPRISE_API_KEY', 'replace-with-long-random-key');
```

## Webhook

后台填写订单状态回调 URL。
该功能是基础实现，正式对接外部 ERP 前仍需在测试环境验证签名、重试和失败告警。
