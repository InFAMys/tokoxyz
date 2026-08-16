<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

it('returns 422 json for empty kode when Accept json', function () {
    $customer = Customer::query()->first();
    Auth::guard('customer')->login($customer);

    $this->withHeader('Accept', 'application/json')
        ->post(route('checkout.diskon'), ['kode_diskon' => ''])
        ->assertStatus(422)
        ->assertJsonPath('errors.kode_diskon.0', 'The kode diskon field is required.');
});

it('returns 302 redirect for empty kode without Accept', function () {
    $customer = Customer::query()->first();
    Auth::guard('customer')->login($customer);

    $this->post(route('checkout.diskon'), ['kode_diskon' => ''])
        ->assertStatus(302);
});
