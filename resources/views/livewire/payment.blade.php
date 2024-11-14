<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment - {{ $order->order_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Order Payment</h1>
            <div class="mb-4">
                <p class="text-gray-600">Order Number: {{ $order->order_number }}</p>
                <p class="text-gray-600">Total Amount: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
            <button id="pay-button" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Pay Now
            </button>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = '/payment/success/' + '{{ $order->order_number }}';
                },
                onPending: function(result) {
                    window.location.href = '/payment/pending/' + '{{ $order->order_number }}';
                },
                onError: function(result) {
                    window.location.href = '/payment/error/' + '{{ $order->order_number }}';
                },
                onClose: function() {
                    window.location.href = '/payment/cancel/' + '{{ $order->order_number }}';
                }
            });
        };
    </script>
</body>

</html>
