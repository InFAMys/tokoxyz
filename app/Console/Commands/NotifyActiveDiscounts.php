<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Diskon;
use App\Notifications\DiscountAvailable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notify:discounts')]
#[Description('Notify all customers about currently active, unnotified discounts.')]
class NotifyActiveDiscounts extends Command
{
    public static function notifyActive(Diskon $diskon): void
    {
        if (self::isActive($diskon)) {
            Customer::all()->each(fn (Customer $c) => $c->notify(new DiscountAvailable($diskon)));
            $diskon->forceFill(['notified_at' => now()])->save();
        }
    }

    protected static function isActive(Diskon $diskon): bool
    {
        return $diskon->status_diskon === 'aktif'
            && $diskon->mulai_diskon <= now()
            && $diskon->akhir_diskon >= now()
            && $diskon->notified_at === null;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = 0;

        Diskon::where('status_diskon', 'aktif')
            ->where('mulai_diskon', '<=', now())
            ->where('akhir_diskon', '>=', now())
            ->whereNull('notified_at')
            ->each(function (Diskon $diskon) use (&$count) {
                self::notifyActive($diskon);
                $count++;
            });

        $this->info("Notified {$count} active discount(s).");

        return self::SUCCESS;
    }
}
