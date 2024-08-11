<?php

namespace App\Http\Controllers\Paypal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PayPalService;

class PaypalController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function createOrder(Request $request)
    {
        dd($request);
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->input('amount');

        try {
            $approvalUrl = $this->paypalService->createOrder($amount);
            return redirect()->away($approvalUrl);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function success()
    {
        return view('paypal.success');
    }

    public function cancel()
    {
        return view('paypal.cancel');
    }
}
