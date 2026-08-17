<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $from = $this->parseDate($request->query('from'), now()->startOfMonth());
        $to = $this->parseDate($request->query('to'), now()->endOfDay());

        $query = $this->salesQuery($from, $to);
        $sales = $query->get();
        $ids = $sales->pluck('id_checkout');

        $summary = $this->summary($sales);
        $topProducts = $this->topProducts($ids);
        $categories = $this->categories($ids);

        return view('owner.laporan.index', compact('from', 'to', 'sales', 'summary', 'topProducts', 'categories'));
    }

    public static function monthlySummary(): array
    {
        $from = now()->startOfMonth();
        $to = now()->endOfDay();

        return [
            'revenueThisMonth' => (float) self::salesQuery($from, $to)->sum('total_amount'),
            'ordersThisMonth' => self::salesQuery($from, $to)->count(),
            'recentSales' => self::salesQuery($from, $to)->latest('paid_at')->limit(5)->get(),
        ];
    }

    protected static function salesQuery(Carbon $from, Carbon $to)
    {
        return Checkout::query()
            ->with(['items'])
            ->whereIn('status', ['paid', 'processed', 'shipping', 'delivered', 'completed'])
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to]);
    }

    protected function summary($sales): array
    {
        $count = $sales->count();
        $revenue = (float) $sales->sum('total_amount');
        $preorderOrders = $sales->filter(fn ($s) => $s->items->contains('is_preorder', true))->count();

        return [
            'revenue' => $revenue,
            'orders' => $count,
            'avgOrder' => $count > 0 ? round($revenue / $count, 2) : 0.0,
            'itemsSold' => (int) $sales->flatMap->items->sum('jumlah_barang'),
            'preorderOrders' => $preorderOrders,
            'discountsGiven' => (float) $sales->sum('diskon_nominal'),
        ];
    }

    protected function topProducts($ids): array
    {
        return DB::table('detail_pesanan')
            ->whereIn('id_checkout', $ids)
            ->selectRaw('nama_barang, SUM(jumlah_barang) as qty, SUM(subtotal) as revenue')
            ->groupBy('nama_barang')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->toArray();
    }

    protected function categories($ids): array
    {
        return DB::table('detail_pesanan as d')
            ->join('barangs as b', 'b.id_barang', '=', 'd.id_barang')
            ->join('kategoris as k', 'k.id_kategori', '=', 'b.id_kategori')
            ->whereIn('d.id_checkout', $ids)
            ->selectRaw('k.nama_kategori, SUM(d.subtotal) as revenue, SUM(d.jumlah_barang) as qty')
            ->groupBy('k.nama_kategori')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }

    protected function parseDate($value, Carbon $default): Carbon
    {
        try {
            return $value ? Carbon::parse($value) : $default;
        } catch (\Throwable) {
            return $default;
        }
    }
}
