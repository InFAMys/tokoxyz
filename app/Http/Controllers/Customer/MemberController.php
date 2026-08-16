<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Membership;
use App\Services\MidtransApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class MemberController extends Controller
{
    public const FEE = 25000;

    public function __construct(
        protected MidtransApi $midtrans,
    ) {}

    public function show(): View
    {
        $customer = $this->customer();

        if ($customer->member === 'false') {
            foreach ($customer->memberships()->where('status', 'pending')->get() as $membership) {
                $this->reconcile($membership);
            }

            $customer->refresh();
        }

        return view('customer.membership.index', compact('customer'));
    }

    public function subscribe(): View|RedirectResponse
    {
        if ($this->customer()->member === 'true') {
            return redirect()->route('membership.index')->with('status', 'Kamu sudah menjadi member.');
        }

        return view('customer.membership.pay');
    }

    public function token(): JsonResponse
    {
        $customer = $this->customer();

        if ($customer->member === 'true') {
            return response()->json(['message' => 'Kamu sudah menjadi member.'], 422);
        }

        $membership = $customer->memberships()
            ->where('status', 'pending')
            ->latest('id_membership')
            ->first();

        $created = false;

        if (! $membership) {
            $membership = Membership::create([
                'id_cst' => $customer->id_cst,
                'order_id' => $this->orderId(),
                'nominal' => self::FEE,
                'status' => 'pending',
            ]);
            $created = true;
        }

        try {
            $token = $this->midtrans->createSnapToken([
                'transaction_details' => [
                    'order_id' => $membership->order_id,
                    'gross_amount' => self::FEE,
                ],
                'customer_details' => [
                    'first_name' => $customer->nama,
                    'email' => $customer->email,
                    'phone' => $customer->no_telp,
                ],
                'item_details' => [[
                    'id' => 'MEMBERSHIP',
                    'price' => self::FEE,
                    'quantity' => 1,
                    'name' => 'Keanggotaan Member (Diskon 10%)',
                ]],
            ]);
        } catch (Throwable $e) {
            if ($created) {
                $membership->delete();
            }

            return response()->json(['message' => 'Gagal membuat pembayaran: '.$e->getMessage()], 422);
        }

        return response()->json(['token' => $token]);
    }

    public function reconcile(Membership $membership): void
    {
        if ($membership->status !== 'pending') {
            return;
        }

        try {
            $data = $this->midtrans->transactionStatus($membership->order_id);
        } catch (Throwable) {
            return;
        }

        $status = strval($data['transaction_status'] ?? '');
        $map = [
            'capture' => 'paid',
            'settlement' => 'paid',
            'expire' => 'expired',
            'cancel' => 'expired',
            'deny' => 'deny',
        ];

        $newStatus = $map[$status] ?? null;

        if ($newStatus === null) {
            if (strval($data['status_code'] ?? '') === '404') {
                $membership->update(['status' => 'expired']);
            }

            return;
        }

        if ($newStatus === 'paid') {
            $membership->paid_at = $membership->paid_at ?? now();
            $customer = $membership->customer;
            $customer->member = 'true';
            $customer->member_since = $customer->member_since ?? now();
            $customer->save();
        }

        $membership->update(['status' => $newStatus]);
    }

    protected function orderId(): string
    {
        return 'MEM-'.strtoupper(bin2hex(random_bytes(5)));
    }

    protected function customer(): Customer
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return $customer;
    }
}
