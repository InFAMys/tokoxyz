# Keranjang Functionality Plan

## Context

The `keranjang` migration has not been run yet and already has the structure needed for a customer cart:

- `id_keranjang` primary key
- `id_cst` customer foreign key
- `id_barang` barang foreign key
- nullable `id_ukuran` ukuran foreign key
- `jumlah_barang` quantity with default `1`
- timestamps
- unique row per `id_cst`, `id_barang`, and `id_ukuran`

Products can either use general stock from `barangs.stok` or size-specific stock from `ukurans.stok_ukuran`. The cart feature should support both.

## Goal

Create customer cart functionality so logged-in customers can:

- add barang to keranjang from the customer barang detail page
- choose ukuran when a barang has ukuran options
- set quantity when adding an item
- view their keranjang page
- update item quantity
- remove item from keranjang
- see subtotal and total price

## Files To Add

### `app/Models/Keranjang.php`

Create a model for the `keranjang` table.

Planned fields:

- `$table = 'keranjang'`
- `$primaryKey = 'id_keranjang'`
- `$fillable = ['id_cst', 'id_barang', 'id_ukuran', 'jumlah_barang']`
- cast `jumlah_barang` as integer
- relationships:
  - `customer()` belongs to `Customer`
  - `barang()` belongs to `Barang`
  - `ukuran()` belongs to `Ukuran`, nullable

### `app/Http/Controllers/Customer/KeranjangController.php`

Create a customer controller for cart actions.

Planned methods:

- `index()`
  - load the authenticated customer's cart rows
  - eager load `barang.brand`, `barang.kategori`, and `ukuran`
  - show cart page with totals
- `store(Request $request)`
  - validate selected barang, optional ukuran, and quantity
  - ensure barang status is `Ditampilkan`
  - ensure selected ukuran belongs to selected barang when `id_ukuran` is present
  - validate requested quantity against available stock
  - merge with existing matching row and increment quantity instead of creating duplicates
- `update(Request $request, int $id)`
  - only update cart rows owned by the authenticated customer
  - validate quantity against available stock
- `destroy(int $id)`
  - only delete cart rows owned by the authenticated customer

### `resources/views/customer/keranjang/index.blade.php`

Create the cart page using existing Bootstrap/custom CSS style, not Tailwind.

Planned UI:

- list cart items with product image, name, brand/category, selected ukuran, price, quantity input, subtotal, and delete button
- update quantity with a compact form per row
- show empty cart state with a link back to home/search
- show total price at the bottom

### Optional Form Request Files

If the implementation starts getting noisy, add Form Requests:

- `app/Http/Requests/Customer/StoreKeranjangRequest.php`
- `app/Http/Requests/Customer/UpdateKeranjangRequest.php`

Given the current project style often validates inside controllers, I may keep validation in the controller unless the method becomes too large.

## Files To Modify

### `routes/web.php`

Inside the existing `auth:customer` route group, add routes:

```php
Route::get('keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
Route::put('keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
Route::post('keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
```

I will use `POST` for delete to match the existing project pattern, such as `alamat/{id}` destroy.

### `resources/views/customer/layouts/app.blade.php`

Replace the empty Keranjang link with:

```php
route('keranjang.index')
```

Keep the existing `$activ == 'keranjang'` active state.

### `resources/views/customer/barang/detail.blade.php`

Add an add-to-cart form below the product availability/preorder area.

Planned behavior:

- form submits to `route('keranjang.store')`
- hidden `id_barang`
- if product has ukuran, show a `<select>` for `id_ukuran`
- only show ukuran options with `stok_ukuran > 0`
- quantity input starts at `1`
- max quantity follows selected stock as much as possible server-side; optional client-side helper may be added later if needed
- button disabled or replaced with out-of-stock text if no stock is available

### `app/Models/Customer.php`

Add:

```php
public function keranjangs(): HasMany
```

This matches the existing `alamats()` relationship pattern.

### `app/Models/Barang.php`

Add:

```php
public function keranjangs(): HasMany
```

Also consider changing `ukurans()` to have a `HasMany` return type if touching the model anyway.

### `app/Models/Ukuran.php`

Add:

```php
public function keranjangs(): HasMany
```

Also consider adding explicit relationship return types to match Laravel best practices.

## Stock Rules

When adding or updating cart quantity:

- if `id_ukuran` is filled, stock source is `ukurans.stok_ukuran`
- if `id_ukuran` is empty, stock source is `barangs.stok`
- if barang has ukuran rows, customer must choose an ukuran
- requested quantity must be at least `1`
- requested quantity must not exceed available stock
- when merging an existing row, validate the new total quantity against stock

## Cart Row Merge Rule

If the customer adds the same `id_barang` and same `id_ukuran` again:

- do not create a new row
- increment `jumlah_barang`
- validate the final quantity against stock before saving

This matches the unique key in the migration and keeps the cart clean.

## Authorization / Ownership

Every cart action will use `Auth::guard('customer')->user()` and query through that customer's relationship where possible.

For update/delete, use the authenticated customer's cart query:

```php
$customer->keranjangs()->findOrFail($id)
```

This prevents one customer from changing another customer's cart item.

## View / Styling Approach

- Use existing `customer.layouts.app`
- Set `$activ = 'keranjang'`
- Use existing classes like `main-content`, `page-title`, `card-pink`, `btn-pink`, `btn-pink-outline`, `summary-box`, and Bootstrap grid utilities
- Do not use Tailwind classes
- Escape output with Blade `{{ }}`
- Keep all database queries in the controller, not in Blade

## Validation Messages

Use Indonesian validation/status messages to match existing customer views.

Examples:

- `Barang tidak tersedia.`
- `Pilih ukuran terlebih dahulu.`
- `Jumlah barang melebihi stok tersedia.`
- `Barang berhasil ditambahkan ke keranjang.`
- `Keranjang berhasil diperbarui.`
- `Barang berhasil dihapus dari keranjang.`

## Verification After Implementation

After implementing, run:

```bash
php -l app/Models/Keranjang.php
php -l app/Http/Controllers/Customer/KeranjangController.php
php -l routes/web.php
vendor/bin/pint --dirty --format agent
php artisan route:list --path=keranjang --no-interaction
php artisan test --compact
```

Current known limitation: previous `php artisan test --compact` failed because MySQL was not reachable in this environment. If that is still true, I will report it and leave the command for you to run after the database is available.

## Open Decisions

- Keep delete as `POST` to match existing project routes, or use `DELETE` for stricter REST style.
- Keep validation inside `KeranjangController` to match existing code, or create Form Request classes for cleaner methods.
- Add cart item count in navbar now, or leave that for a later enhancement.

## Initial Recommendation

Implement the core cart first with controller validation, ownership checks, stock validation, and Bootstrap/custom CSS views. Defer navbar cart count until checkout/order functionality exists, because it needs shared view data or a view composer to avoid repeating queries.
