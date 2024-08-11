<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $this->validate($request, [
            'value' => 'required|numeric|min:0.01',
        ]);
    
        $value = $request->input('value');
    
        try {
            $approvalUrl = $this->paypalService->createOrder($value);
            return redirect()->away($approvalUrl);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    

    public function success()
    {
        return view('inc.success');
    }

    public function cancel()
    {
        return view('inc.cancel');
    }
}
