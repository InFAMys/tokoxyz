# Plan: Working Search Bars (live-as-you-type, debounced)

## Scope
Make the 5 dead search bars work with debounced live-as-you-type filtering.
Server-side filter via AJAX returns only the `<tbody>` rows fragment; JS swaps it in.
No page reload. No-JS fallback (form GET) still works.

Search bar locations:
- Pegawai: k_barang, k_brand, k_kategori
- Owner: k_diskon, k_pegawai

Out of scope: k_ukuran / k_stok (no search bar); `$barangs` print bug in k_barang.

## Mechanism
- Input wrapped in `<form method="GET">` (no-JS fallback: type + Enter = full reload filter).
- JS `input` listener, debounced at **800ms**, `fetch` same listing URL `?q=` with
  `X-Requested-With: XMLHttpRequest` header ("ajax branch" keyed on `$request->ajax()`).
- A sequence counter guards against stale responses landing out of order.
- Each entity's `<tbody>` is extracted into its own partial view
  (`_*_rows.blade.php`), reused for both full page and AJAX render.

## Search columns
| Entity | Columns |
|---|---|
| barang | kode_barang, nama_barang, brand name (whereHas), kategori name (whereHas) |
| brand  | nama_brand |
| kategori | nama_kategori |
| pegawai | nama_pegawai, username_pegawai |
| diskon | nama_diskon, kode_diskon |

## Files
### 5 partials (new) — tbody moved out of each list view, has `id` + empty state
- pegawai/kelola/_barang_rows.blade.php
- pegawai/kelola/_brand_rows.blade.php
- pegawai/kelola/_kategori_rows.blade.php
- owner/kelola/_pegawai_rows.blade.php
- owner/kelola/_diskon_rows.blade.php

Empty state uses `@forelse ... @empty <tr><td colspan="N" ...>No results</td></tr> @endforelse`. colspans: barang 9, diskon 7, pegawai 4, brand 4, kategori (per columns).

### 5 controllers — filter q, AJAX branch renders the partial
```php
public function listBarang(Request $request)
{
    $q = trim($request->query('q', ''));

    $barang = Barang::query()
        ->when($q, fn ($query) => $query
            ->where('nama_barang', 'like', "%$q%")
            ->orWhere('kode_barang', 'like', "%$q%")
            ->orWhereHas('brand', fn ($b) => $b->where('nama_brand', 'like', "%$q%"))
            ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%$q%")))
        ->get();

    if ($request->ajax()) {
        return view('pegawai.kelola._barang_rows', compact('barang', 'barangs'))->render();
    }

    return view('pegawai.kelola.k_barang', compact('barang', 'barangs'));
}
```
`$barangs` gets the same `when($q)` filter so both vars stay consistent.

### 5 list views — replace inline tbody with `@include`, upgrade input
```html
<form method="GET" class="ms-auto search-wrapper" style="width: 230px">
    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
    <input type="text" name="q" value="{{ request('q') }}"
        class="form-control form-control-pink live-search" data-tbody="rows-barang"
        placeholder="Search..." />
</form>
```

### resources/js/script.js — one generic debounced live search
```js
document.querySelectorAll(".live-search").forEach((input) => {
    const debounce = +input.dataset.debounce || 800;
    const target = document.getElementById(input.dataset.tbody);
    const baseUrl = new URL(input.closest("form")?.action || location.href);
    let timer, seq = 0;

    const run = () => {
        baseUrl.searchParams.set("q", input.value.trim());
        const current = ++seq;
        clearTimeout(timer);
        fetch(baseUrl, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then((r) => r.text())
            .then((html) => {
                if (current !== seq) return; // ignore stale response
                const rows = new DOMParser()
                    .parseFromString(html, "text/html")
                    .querySelector("tbody");
                if (target && rows) target.innerHTML = rows.innerHTML;
            });
    };
    input.addEventListener("input", () => { clearTimeout(timer); timer = setTimeout(run, debounce); });
});
```

## Notes
- `fetch` does NOT auto-set X-Requested-With — header must be set explicitly for `$request->ajax()`.
- Input escaped by Blade `{{ }}`. `%` / `_` in query act as like-wildcards (acceptable; optional `addcslashes`).
- Debounce tweakable per input via `data-debounce`.

## Verify
- type query -> rows filter after ~800ms, no page jump; empty q -> all rows; no-JS Enter still filters.
- `npm run build` (or `npm run dev` / `composer run dev`).
- `php artisan view:cache`, `vendor/bin/pint --format agent`, `php artisan test --compact`.
