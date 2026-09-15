<?php

namespace App\Console\Commands;

use App\Models\Checkout;
use App\Services\CheckoutStatusService;
use Illuminate\Console\Command;

class ReconcileOrders extends Command
{
    protected $signature = 'orders:reconcile';

    protected $description = 'Reconcile order statuses against Midtrans and the shipping carrier (auto-refund after 3 days)';

    public function handle(CheckoutStatusService $status): int
    {
        Checkout::query()
            ->whereIn('status', ['pending', 'paid', 'processed', 'shipping', 'delivered'])
            ->with('items')
            ->chunkById(100, function ($checkouts) use ($status) {
                foreach ($checkouts as $checkout) {
                    $status->reconcile($checkout);
                }
            });

        Checkout::query()
            ->whereNotNull('refund_failed_at')
            ->whereIn('status', ['refunded', 'partially_refunded'])
            ->update(['refund_failed_at' => null]);

        return self::SUCCESS;
    }
}
