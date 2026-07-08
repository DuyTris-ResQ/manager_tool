<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('license');

        if ($request->search) {
            $query->where('order_code', 'like', "%{$request->search}%")
                  ->orWhere('transaction_code', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }
}
