# HimeDoll 6.0.0 — Japan Commerce Stable

## 本版重点

本版优先解决“代码存在但没有加载”的生产问题。核心插件现在会统一加载商城、营销、留存、AI、ERP、企业和后台模块。

## 升级步骤

1. 备份数据库和 `wp-content`。
2. 上传并覆盖主题 `himedoll` 与插件 `himedoll-core`。
3. 在 WordPress 后台停用后重新启用 HimeDoll Core。
4. 确认 WooCommerce 已启用。
5. 打开 HimeDoll 设置向导和系统检查。
6. 编辑商品，补充新版筛选字段。
7. 清除页面缓存、对象缓存和 CDN 缓存。

## 兼容要求

- WordPress 6.0+
- PHP 8.0+
- WooCommerce（商品、订单和会员功能必需）
