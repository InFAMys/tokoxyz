@php
    $toasts = collect(session()->all())
        ->reject(fn ($v, $k) => in_array($k, ['_token', '_previous', '_current', 'url', 'errors']) || !is_string($v))
        ->map(fn ($v) => ['msg' => $v, 'type' => 'success'])
        ->values();

    $errs = $errors->any()
        ? collect($errors->all())->map(fn ($m) => ['msg' => $m, 'type' => 'error'])
        : collect();

    $all = $toasts->concat($errs);
@endphp

@if ($all->isNotEmpty())
    <div class="toast-container position-fixed start-50 translate-middle-x p-3"
        style="top: 4.5rem; z-index: 2000">
        @foreach ($all as $t)
            <div class="toast {{ $t['type'] }} show align-items-center text-white border-0 mb-2"
                role="alert" data-bs-delay="3500">
                <div class="d-flex align-items-center">
                    <div class="toast-body">{{ $t['msg'] }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 ms-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-progress"></div>
            </div>
        @endforeach
    </div>
@endif
