# HimeDoll v9.0.0 Product Intelligence

日本向け WooCommerce 商城、AI运营、ERP 与可组合商品数据系统。

## v9.0 Product Intelligence

- 独立管理 Body、Head、Accessory 组件
- 组件管理代码、厂家、材质、尺寸、重量、罩杯、肤色、成本与交期
- 商品绑定默认 Body / Head
- 设置兼容 Body、兼容 Head 与推荐配件
- 商品页显示组合选择器
- SKU 模板及保存时自动生成
- Product Intelligence 后台总览
- 保持 v8 ERP、v7 AI SEO、会员、营销和企业接口兼容

## 推荐升级步骤

1. 备份数据库和 `wp-content`。
2. 覆盖主题与 `himedoll-core` 插件文件。
3. 停用后重新启用 HimeDoll Core，或进入一次后台触发升级。
4. 在 **Product Intelligence** 中先建立 Body 与 Head。
5. 编辑一个测试商品，绑定组件并验证前台组合器。

正式销售中的价格差异仍建议使用 WooCommerce Variable Product 管理；v9 的组件层用于复用规格、建立兼容关系和统一 SKU。
