# Transaction Flow — Choosing Barang → Checkout via Midtrans

End-to-end shopping flow on the **customer** side: browse → add to cart →
checkout → Midtrans. Three PHP namespaces involved, each on its own guard.

## Route Map (relevant)

| Method | Route | Controller@action | Role |
|---|---|---|---|
| GET | `barang/{id}` (`barang.detail`) | CustomerController@detailBarang | barang detail |
| POST | `keranjang` (`keranjang.store`) | KeranjangController@store | add to cart |
| GET | `keranjang` (`keranjang.index`) | KeranjangController@index | list cart |
| POST | `keranjang/checkout` (`keranjang.checkout`) | KeranjangController@checkoutSelected | select items |
| GET | `checkout` (`checkout.create`) | CheckoutController@create | build checkout page |
| POST | `checkout/rate` (`checkout.rate`) | CheckoutController@rate | fetch ongkir |
| POST | `checkout/diskon` (`checkout.diskon`) | CheckoutController@diskon | validate discount |
| POST | `checkout` (`checkout.store`) | CheckoutController@store | create order + Snap |
| GET | `checkout/{id}` (`checkout.show`) | CheckoutController@show | payment/"Bayar Sekarang" |
| POST | `checkout/notification` (`checkout.notification`) | CheckoutController@notification | Midtrans webhook |
| GET | `checkout/riwayat` (`checkout.history`) | CheckoutController@history | order history |

## Step-by-Step

### 1. Browse & choose barang
Customer guards `guest:customer`/`auth:customer`. Products listed on
`welcome`, `barang.search`, `barang.detail`. Detail page shows price range +
`Pilih Ukuran` select (ukuran price overrides base `barang.harga`). "Habis"
gray badge overlays a card when `stokReady() < 1`.

### 2. Add to cart — `keranjang.store`
- Form on `detail.blade.php` posts `id_barang`, optional `id_ukuran`, `jumlah_barang`.
- `KeranjangController@store`:
  1. Loads barang (status `Ditampilkan`) + resolves ukuran (`selectedUkuran`).
  2. Merges qty with existing same `(id_barang, id_ukuran)` cart row.
  3. `ensureStockIsAvailable` — ukuran `stok_ukuran` else barang `stok`; overstock → validation error.
  4. Inserts/updates the `keranjangs` row, redirects to cart.

### 3. Select items for checkout — `keranjang.checkout`
- Cart page: per-item checkbox `.keranjang-check`, sidebar summary is a form.
  JS injects checked values as hidden `id_keranjang[]` on submit (see
  `resources/js/script.js`), since the checkboxes live outside the form.
- `checkoutSelected` validates `id_keranjang` array (min 1), scopes ids to the
  customer's own cart, stores them in session key **`checkout_ids`**, redirects to checkout.
- `CheckoutController::cart()` narrows the cart to those ids (when set); still
  filters out trashed/undisplayed items via `canUseCartItem`.

### 4. Checkout page — `checkout.create`
Loads selected items, computes `subtotal`, `beratTotal`, addresses.
- Choosing an address triggers **`checkout.rate`** → Klikresi live rates
  (`rate()`), `normalizeRates()` picks the configured courier (J&T), returns
  options (service, cost, etd). Shipping cost shown.
- Entering a discount code triggers **`checkout.diskon`** → server validates
  code against `diskon` (aktif, date window) and pre-computes nominal.

### 5. Place order — `checkout.store`
- Server re-validates **everything**. `verifyShippingCost` re-queries Klikresi
  and rejects if the submitted cost doesn't match; `verifyDiscount` recomputes
  the discount off subtotal.
- `total_amount = max(0, subtotal - diskon + ongkir)`.
- Inside `DB::transaction`:
  - Creates `Checkout` (order_id `ORD-xxxx`, snapshot name/email/telp/address).
    `status = 'paid'` when `total_amount == 0` (free order), else `'pending'`.
  - Creates one `CheckoutItem` per cart row (name, ukuran, unit price, qty, subtotal, berat).
  - Free order → `decrementStockForItems($items)` immediately.
- Deletes only the **checked-out** cart rows; forgets `checkout_ids`.
- Paid order → `MidtransApi::createSnapToken($snapPayload())` → stores
  `snap_token` on the checkout, redirects to `checkout.show`.
- Any throw → `DB::rollBack()`, back with `kode_diskon` error.

### 6. Pay — `checkout.show` (Bayar Sekarang)
- `show()` reconciles pending orders with Midtrans; auto-expires after 24h.
- If `pending` + `snap_token`, renders "Bayar Sekarang" button carrying
  `snap-token`, Midtrans `client_key`, and production flag; Midtrans.js Snap
  opens the payment popup when clicked. Free orders show status directly.
- Snap payload (`snapPayload`): `transaction_details` (order_id, gross_amount),
  `customer_details`, `item_details` = each barang as an item + `SHIPPING_JNT`
  line + negative `DISCOUNT` line.

### 7. Payoff — webhook + reconcile
- **`checkout.notification`**: verifies `signature_key` via
  `verifySignature` (sha512 of order_id + status_code + gross_amount + server key),
  matches `gross_amount` to `total_amount`, then `applyMidtransStatus`.
- **`applyMidtransStatus`** maps Midtrans codes: `capture`/`settlement`→`paid`,
  `expire`→`expired`, `cancel`→`cancelled`, `deny`, `refund`, `partial_refund`.
  - On first `paid`: sets `paid_at` + `payment_type`, **decrements stock** only
    if it wasn't already paid (`$wasPaid` guard) — idempotent, no double-deduct.
  - `reconcileMidtrans` (used by `history`/`show`) queries
    `api.sandbox.midtrans.com/v2/{order_id}/status` and applies the same map,
    covering missed webhooks.

## Stock Decrement Rule
Stock is reduced **only on the transition to paid**, and only once:
- Free order (`total_amount == 0`) → decremented inside `store`.
- Paid order → decremented in `applyMidtransStatus` when webhook/reconcile
  marks `paid` and it wasn't already paid. Per-item: ukuran row uses
  `stok_ukuran`, else barang `stok`; both floored at 0.

## Key Config
- `config/services.php`: `midtrans.{server_key,client_key,is_production}`,
  `klikresi.{origin_id,courier}`, `courier`.
- Midtrans hosts: Snap = `app.sandbox.midtrans.com`, status API =
  `api.sandbox.midtrans.com` (SDK-less, plain HTTP with server-key basic auth).
