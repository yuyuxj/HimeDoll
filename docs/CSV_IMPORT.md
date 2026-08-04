# 商品 CSV 导入

后台位置：

`HimeDoll → 商品批量导入`

## 支持字段

- sku
- name
- regular_price
- sale_price
- description
- short_description
- stock_quantity
- brand
- categories
- image_url
- height
- height_numeric
- weight
- material
- cup
- skin
- ai
- delivery
- warranty
- seo_title
- seo_description
- status

## categories 格式

多个分类使用 `|` 分隔：

`TPE|AIシリーズ`

## 注意

- CSV 使用 UTF-8。
- 图片 URL 必须允许服务器访问。
- SKU 相同的商品会更新，不会重复创建。
