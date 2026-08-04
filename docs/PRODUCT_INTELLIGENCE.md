# Product Intelligence 使用说明

## 数据模型

- Body：身体主体、材质、身高、重量、罩杯、成本。
- Head：头雕、厂家、肤色、管理代码。
- Accessory：假发、服装、加热、清洁等附件。
- Product：WooCommerce 销售商品，绑定默认和兼容组件。

## SKU 模板

可用占位符：`{ID}`、`{BODY}`、`{HEAD}`、`{MATERIAL}`。

示例：`HD-{BODY}-{HEAD}-{MATERIAL}`。

组件有管理代码时优先使用管理代码，否则使用 `B+ID` / `H+ID`。

## 注意

组件选择器目前用于规格展示。需要按选项加价、独立库存或独立图片时，应继续建立 WooCommerce variations，再把组件 ID 写入对应 variation 的扩展字段。
