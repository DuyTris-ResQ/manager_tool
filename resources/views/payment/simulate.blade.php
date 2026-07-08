<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Checkout Simulator</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #e8fdf5 0%, #e0f7fa 40%, #f0f9ff 100%);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-700 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white shadow-2xl rounded-3xl p-8 border border-gray-100 flex flex-col items-center space-y-6">
        
        <!-- Header -->
        <div class="text-center">
            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-bold uppercase tracking-wider">Checkout Sandbox</span>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight mt-2">Simulate Payment Portal</h2>
            <p class="text-xs text-gray-500 mt-1">Order Code: <span class="font-bold text-gray-700 font-mono">#{{ $order->order_code }}</span></p>
        </div>

        <!-- Bill Details Card -->
        <div class="w-full bg-gray-50 rounded-2xl p-5 border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Amount</p>
                <p class="text-2xl font-black text-gray-950 mt-1">{{ number_format($order->amount) }} VND</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</p>
                <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 uppercase tracking-wider animate-pulse">{{ $order->status }}</span>
            </div>
        </div>

        <!-- QR Code Simulation -->
        <div class="flex flex-col items-center p-6 bg-white border border-gray-100 rounded-2xl shadow-sm space-y-3">
            @if(!empty($bank_account))
                <!-- Real VietQR Code -->
                <div class="w-56 h-56 bg-white border border-emerald-500 rounded-2xl p-2 flex items-center justify-center shadow-md">
                    <img src="https://img.vietqr.io/image/{{ $bank_name }}-{{ $bank_account }}-compact.png?amount={{ $order->amount }}&addInfo={{ $order->order_code }}&accountName={{ urlencode($bank_holder) }}" alt="VietQR" class="max-w-full max-h-full rounded-xl">
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-gray-800">{{ $bank_name }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $bank_account }}</p>
                    <p class="text-xs text-gray-500 font-medium uppercase mt-0.5">{{ $bank_holder }}</p>
                </div>
            @else
                <!-- Mock QR Visual -->
                <div class="w-48 h-48 bg-gray-150 border-4 border-emerald-500 rounded-2xl flex items-center justify-center relative overflow-hidden shadow-inner bg-slate-50">
                    <!-- Simple styling to represent a QR grid pattern -->
                    <div class="grid grid-cols-6 gap-2 w-40 h-40 opacity-70">
                        <div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div>
                        <div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div>
                        <div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-300 rounded"></div>
                        <div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div>
                        <div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-300 rounded"></div><div class="bg-slate-900 rounded"></div>
                        <div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div><div class="bg-slate-900 rounded"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="p-2.5 bg-white text-emerald-600 font-bold rounded-xl text-xs shadow-md border border-emerald-100 uppercase tracking-widest">PayOS</span>
                    </div>
                </div>
            @endif
            <p class="text-xs text-gray-400 font-medium">Scan to pay with Bank App (Simulated)</p>
        </div>

        <!-- Success Notification Box (Initially hidden) -->
        <div id="payment-success-box" class="w-full p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center space-x-3 shadow-sm hidden">
            <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-sm font-bold">Payment Verified Successfully!</p>
                <p class="text-xs text-emerald-600 mt-0.5">The license key has been extended by 30 days and marked as active.</p>
            </div>
        </div>

        <!-- Action Form -->
        <div id="actions-panel" class="w-full flex flex-col space-y-2">
            <button onclick="triggerWebhook()" class="w-full py-4 text-sm font-bold rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white hover:from-emerald-600 hover:to-teal-600 shadow-lg shadow-emerald-100 hover:shadow-emerald-200 transition-all active:scale-[0.99] flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Simulate Payment Success</span>
            </button>
            <p class="text-[10px] text-gray-400 text-center">Clicking this calls POST `/api/payment/webhook` with valid PayOS transaction data.</p>
        </div>

        <div id="done-panel" class="w-full hidden">
            <a href="{{ route('admin.dashboard') }}" class="w-full py-4 text-sm font-bold rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors flex items-center justify-center">
                Return to Admin Panel
            </a>
        </div>
    </div>

    <script>
        function triggerWebhook() {
            const payload = {
                data: {
                    orderCode: "{{ $order->order_code }}",
                    amount: {{ $order->amount }},
                    status: "success",
                    reference: "TX-" + Math.floor(Math.random() * 100000000),
                    transaction_code: "MOCK-PAYOS-" + Math.floor(Math.random() * 90000)
                }
            };

            fetch('/api/payment/webhook', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('payment-success-box').classList.remove('hidden');
                    document.getElementById('actions-panel').classList.add('hidden');
                    document.getElementById('done-panel').classList.remove('hidden');
                } else {
                    alert('Error simulating payment: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Connection failed to simulation endpoint.');
            });
        }
    </script>
</body>
</html>
