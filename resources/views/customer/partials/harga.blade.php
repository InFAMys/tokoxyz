@php
    $prices = $item->ukurans->pluck('harga_ukuran')->filter()->map(fn ($p) => (float) $p);
@endphp
@if ($prices->isNotEmpty())
    @php
        $min = $prices->min();
        $max = $prices->max();
    @endphp
    @if ($min == $max)
        Rp {{ number_format($min, 0, ',', '.') }}
    @else
        <span class="d-flex flex-wrap column-gap-1">
            <span>Rp {{ number_format($min, 0, ',', '.') }}</span>
            <span class="text-muted">-</span>
            <span>Rp {{ number_format($max, 0, ',', '.') }}</span>
        </span>
    @endif
@else
    Rp {{ number_format($item->harga, 0, ',', '.') }}
@endif
