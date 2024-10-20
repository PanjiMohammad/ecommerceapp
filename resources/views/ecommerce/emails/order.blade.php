{{-- <!DOCTYPE html>
<html>

<head>
    <title>Konfirmasi Pesanan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        table {
            border-collapse: collapse !important;
        }
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        /* MOBILE STYLES */
        @media screen and (max-width:600px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
            .mobile-hide {
                display: none !important;
            }
            .mobile-center {
                text-align: center !important;
            }
        }
        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
    <!-- HIDDEN PREHEADER TEXT -->
    <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">Thank you for your purchase! Your order details are below.</div>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- HERO -->
        <tr>
            <td style="background-color: #FFA73B; align-items: center;  padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td style="vertical-align: top; background-color: #ffffff; align-items: center;  padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <h1 style="font-size: 48px; font-weight: 400; margin: 2;">Terima kasih atas pembelian Anda!</h1>
                            <img src="https://img.icons8.com/?size=100&id=114213&format=png&color=000000" width="125" height="120" style="display: block; border: 0px;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- COPY BLOCK -->
        <tr>
            <td style="padding: 0px 10px 0px 10px; background-color: #f4f4f4; align-items: center;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <!-- COPY -->
                    <tr>
                        <td style="background-color: #ffffff; align-items: flex-start; padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">Hi {{ $order->customer->name }},</p>
                            <p style="margin: 0;">Terima kasih atas pembelian Anda! Kami sangat senang dapat mengirimkan pesanan <span style="text-transform: uppercase; font-weight: 40;">#{{ $orderDetail->tracking_number }}</span> kepada Anda. Di bawah ini adalah rincian pembelian Anda : </p>
                        </td>
                    </tr>
                    <!-- ORDER DETAILS -->
                    <tr>
                        <td style="background-color: #ffffff; align-items: flex-start;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding: 20px 30px 0px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        <h2 style="font-size: 24px; font-weight: 700; margin: 0;">Detail Pesanan</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 30px 30px 30px; align-items: flex-start;">
                                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="20%" style="padding: 10px 0;  align-items: flex-start;">
                                                    <img src="{{ $message->embed(public_path('/products/' . $orderDetail->product->image)) }}" alt="{{ $orderDetail->product->name }}" style="width: 80px; height: 80px; object-fit: cover; display: block; border: 1px solid #dddddd; border-radius: 5px; object-fit: contain;">
                                                </td>
                                                <td width="55%" style="align-items: flex-start; padding: 10px 0; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400;">
                                                    {{ $orderDetail->product->name }}<br>
                                                    Kuantiti : {{ $orderDetail->qty }} item
                                                </td> 
                                                <td width="25%" style="align-items: flex-start; padding: 10px 0; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400;">
                                                    Rp {{ number_format($orderDetail->price, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0px 30px 30px 30px; align-items: flex-start;">
                                        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="75%" style="align-items: flex-start; padding: 10px 0; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700;">Total</td>
                                                <td width="25%" style="align-items: flex-start; padding: 10px 0; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700;">Rp {{ number_format(($orderDetail->price * $orderDetail->qty) + (($orderDetail->price * $orderDetail->qty) * 0.10) + $orderDetail->shipping_cost, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- CLOSING -->
                    <tr>
                        <td style="padding: 0px 30px 40px 30px; align-items: flex-start; background-color: #ffffff; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0px 0px 30px 0px;">Kami harap Anda menikmati pembelian Anda! Jika Anda memiliki pertanyaan, jangan ragu untuk <a href="https://api.whatsapp.com/send?phone=6287889165715&amp;text=Halo%20,%20Saya%20butuh%20bantuan%20" target="_blank" style="color: #FFA73B;">hubungi kami</a>.</p>
                            <p style="margin: 0;">Cheers,<br>Panji Mohammad</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- FOOTER -->
        <tr>
            <td style="background-color: #f4f4f4; align-items: center; padding: 30px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td style="background-color: #f4f4f4; align-items: center; padding: 30px 30px 30px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;">
                            <p style="margin: 0;">Copyright ©2024 All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 10px 0;
            background-color: #4CAF50; /* Green header */
            border-radius: 8px 8px 0 0;
            color: white;
        }

        .header img {
            align-items: center;
            max-width: 120px;
            object-fit: contain;
        }

        .content {
            padding: 20px;
        }

        .content h1 {
            font-size: 24px;
            color: #333;
        }

        .content p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .order-info {
            background-color: #fef3c7; /* Soft yellow */
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #333;
        }
        .order-info strong {
            color: #333;
        }
        .product-list {
            margin-top: 20px;
        }
        .product-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 8px;
        }

        .product-item img {
            max-width: 80px;
            border-radius: 4px;
            margin-right: 15px;
            object-fit: contain;
        }

        .product-item h4 {
            font-size: 16px;
            color: #333;
            margin: 0;
        }

        .product-item p {
            font-size: 14px;
            color: #777;
            margin: 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            padding: 10px 0;
            background-color: #4CAF50;
            border-radius: 0 0 8px 8px;
            color: white;
        }

        .footer a {
            color: #ffeb3b; /* Bright yellow */
            text-decoration: none;
        }

        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
            .header h1 {
                font-size: 22px;
            }
            .content h1 {
                font-size: 20px;
            }
            .product-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .product-item img {
                margin-bottom: 10px;
                object-fit: contain;
                margin-right: 15px;
                max-width: 80px;
                border-radius: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://img.icons8.com/?size=100&id=114213&format=png&color=000000" alt="Shipment Logo"/>
            {{-- <img src="https://via.placeholder.com/120" alt="Company Logo"> --}}
            <h2>Terima Kasih Sudah Berbelanja!</h2>
        </div>
        
        <div class="content">
            <h1>Pesanan Anda Telah Dikirim!</h1>
            <p>Halo {{ $order->customer_name }},</p>
            <p>Pesanan Anda telah diproses dan sedang dalam perjalanan! Berikut adalah rincian pengiriman Anda:</p>
            
            <div class="order-info">
                <p><strong>Nomor Resi : </strong> {{ '#' . $orderDetail->tracking_number }}</p>
                <p><strong>Tanggal Pengiriman : </strong> {{ \Carbon\Carbon::parse($orderDetail->shippin_date)->locale('id')->translatedFormat('l, d F Y H:i') }}</p>
                <p><strong>Alamat Pengiriman : </strong> {{ $order->customer_address . ', ' . $order->customer->district->name . ', Kelurahan ' . $order->customer->district->city->name . ', Kecamatan ' . $order->customer->district->city->province->name . ', ' . $order->customer->district->city->postal_code . ', Indonesia' }}</p>
                <p><strong>Kurir Pengiriman : </strong> {{ $orderDetail->shipping_service }}</p>
            </div>

            <div class="product-list">
                <div class="product-item">
                    {{-- <img src="https://via.placeholder.com/80" alt="Product Image"> --}}
                    <img src="{{ $message->embed(public_path('/products/' . $orderDetail->product->image)) }}" alt="{{ $orderDetail->product->name }}">

                    <div style="margin-left: 7px;">
                        <h4 style="margin-bottom: 6px;">{{ $orderDetail->product->name }}</h4>
                        <p style="margin-bottom: 6px;">Jumlah : {{ $orderDetail->qty }} item</p>
                        <p>Harga : {{ 'Rp ' . number_format($orderDetail->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <p>Kami harap Anda menikmati pembelian Anda! Jika Anda memiliki pertanyaan, jangan ragu untuk <a href="https://api.whatsapp.com/send?phone=6287889165715&amp;text=Halo%20,%20Saya%20butuh%20bantuan%20" target="_blank" style="color: #FFA73B;">hubungi kami</a>.</p>
            <p style="margin: 0;">Cheers,<br>Panji Mohammad</p>
        </div>

        <div class="footer">
            <p>&copy; 2024 All rights reserved. | <a href="#">Kebijakan Privasi</a></p>
        </div>
    </div>
</body>
</html>