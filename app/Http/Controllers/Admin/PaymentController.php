<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with('license');

        if (!$user->isSuperAdmin()) {
            $query->whereHas('license', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'like', "%{$request->search}%")
                  ->orWhere('transaction_code', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }
}
