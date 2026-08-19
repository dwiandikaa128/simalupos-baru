<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class MembershipController extends Controller
{
    public function show($token)
    {
        $customer = Customer::where('unique_token', $token)->firstOrFail();
        $mutations = $customer->mutations()->with('order')->limit(30)->get();

        return view('public.membership', compact('customer', 'mutations'));
    }
}
