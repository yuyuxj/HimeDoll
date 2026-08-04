# Japan Operations

## Checkout

The checkout requires an 18+ confirmation and records discreet packaging, preferred delivery date and time slot. Delivery dates must be at least three days from the current date.

## Shipment tracking

Open a WooCommerce order and use the **HimeDoll 配送追跡** box to select a carrier, enter the tracking number and shipment date. A customer-visible order note is created whenever a new tracking number is saved.

Supported direct tracking links:

- Yamato Transport
- Sagawa Express
- Japan Post / EMS
- DHL
- FedEx
- UPS

## Customer tracking page

Create a WordPress page and insert:

```
[himedoll_tracking]
```

Logged-in customers will see tracking data for their latest ten orders.

## Test checklist

1. Place a test order and confirm 18+ validation blocks unchecked checkout.
2. Confirm discreet packaging and delivery preferences appear in the admin order.
3. Add a carrier and tracking number to the order.
4. Verify the customer receives an order note and sees the tracking link in My Account.
5. Test the tracking shortcode while logged in as the customer.
