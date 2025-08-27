<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            margin: -30px -30px 30px;
            border-radius: 5px 5px 0 0;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .order-items {
            margin: 20px 0;
        }
        .item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #007bff;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.pending {
            background-color: #ffc107;
        }
        .status.paid {
            background-color: #28a745;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your purchase!</p>
        </div>

        <h2>Hello {{ $user->name }},</h2>
        
        <p>We're excited to confirm that we've received your order. Here are the details:</p>

        <div class="order-info">
            <h3>Order Details</h3>
            <p><strong>Order Number:</strong> #{{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Status:</strong> <span class="status {{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
        </div>

        <div class="order-items">
            <h3>Items Ordered</h3>
            @foreach($orderItems as $item)
                <div class="item">
                    <strong>{{ $item->product->name }}</strong><br>
                    <span>Quantity: {{ $item->quantity }}</span> | 
                    <span>Price: ${{ number_format($item->price, 2) }}</span> | 
                    <span>Subtotal: ${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="total">
            <p>Total Amount: ${{ number_format($order->getTotalAmount(), 2) }}</p>
        </div>

        <p>We'll send you another email when your order ships. If you have any questions, please don't hesitate to contact us.</p>

        <div class="footer">
            <p>Thank you for choosing Padellers!</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>