@php
    $tStatus = strtolower((string) ($tracking['status'] ?? ''));
    $tBadge = $tStatus === 'delivered' ? 'text-bg-success'
        : ($tStatus === 'transit' || $tStatus === 'intransit' || $tStatus === 'in_transit' ? 'text-bg-primary'
        : 'text-bg-warning');
    $tHistories = $tracking['histories'] ?? [];
@endphp
@if (! empty($tHistories))
    <div class="summary-box mb-3">
        <div class="form-label-pink mb-3"><i class="fa-solid fa-truck-fast"></i> Lacak Pengiriman</div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="small text-muted">Kurir</div>
                <div class="fw-bold">{{ $checkout->shipping_courier }}</div>
            </div>
            <div class="text-end">
                <div class="small text-muted">No. Resi</div>
                <div class="fw-bold">{{ $checkout->no_resi }}</div>
            </div>
        </div>
        @if ($tStatus)
            <div class="mb-3">
                <span class="badge rounded-pill {{ $tBadge }}">{{ $tracking['status'] }}</span>
            </div>
        @endif
        <div class="vstack" style="--bs-gap-y:0">
            @foreach ($tHistories as $event)
                @php
                    $est = strtolower((string) ($event['status'] ?? ''));
                    $isDelivered = $est === 'delivered';
                    $isOut = $est === 'out_for_delivery' || $est === 'outfordelivery' || $est === 'delivery';
                    $icon = $isDelivered ? 'fa-circle-check' : ($isOut ? 'fa-truck-fast' : 'fa-circle');
                    $dotColor = $isDelivered ? 'text-success' : ($isOut ? 'text-info' : 'text-pink');
                    $tDate = \Carbon\Carbon::parse($event['date'] ?? null);
                @endphp
                <div class="d-flex">
                    <div class="position-relative flex-shrink-0 d-flex justify-content-center"
                        style="width:1.4rem">
                        <i class="fa-solid {{ $icon }} {{ $dotColor }}"
                            style="font-size:1.1rem; line-height:1; position:relative; z-index:1"></i>
                        @if (! $loop->last)
                            <span style="position:absolute; top:1.1rem; bottom:0; left:50%; width:2px; transform:translateX(-50%); background:#f0d9de"></span>
                        @endif
                    </div>
                    <div class="flex-grow-1 pb-3">
                        <div class="small fw-semibold">{{ $event['status'] ?? '' }}</div>
                        <div class="small">{{ $event['message'] ?? '' }}</div>
                        <div class="small text-muted">{{ $tDate ? $tDate->format('d M Y H:i') : '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
