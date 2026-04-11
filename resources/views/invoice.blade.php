@php
    $merchant = $invoice_data['merchant'] ?? [];
    $customer = $invoice_data['customer'] ?? [];
    $items = $invoice_data['items'] ?? [];
    $tax_rate = (float) ($invoice_data['tax_rate'] ?? 0);
    $total_price = 0;
    $page_size = $paper_size ?? 'Letter';
    $page_orientation = ($orientation ?? 'portrait') === 'landscape' ? 'landscape' : 'portrait';
    $invoice_timezone = (string) ($invoice_data['timezone'] ?? 'Asia/Manila');
    $card_type = strtoupper((string) ($invoice_data['card_type'] ?? ''));
    $card_label = $card_type !== '' ? $card_type : 'CARD';
    $invoice_items = [];

    foreach ($items as $item) {
        $price = (float) ($item['price'] ?? 0);
        $quantity = (float) ($item['quantity'] ?? 0);
        $line_total = $price * $quantity;

        $total_price += $line_total;
        $invoice_items[] = [
            'description' => $item['item'] ?? '',
            'quantity' => $item['quantity'] ?? '',
            'price' => $price,
            'line_total' => $line_total,
        ];
    }

    $tax_amount = $total_price * $tax_rate;
    $grand_total = $total_price + $tax_amount;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Xendivel Invoice Template</title>

    <style>
        @page {
            size: {{ $page_size }} {{ $page_orientation }};
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html {
            color: #374151;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        p {
            margin: 0;
        }

        table {
            border-collapse: collapse;
        }

        td,
        th,
        p,
        span {
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .invoice-page {
            min-height: 100%;
            background: #ffffff;
        }

        .invoice-container {
            width: 704px;
            margin: 0 auto;
        }

        .invoice-header {
            background: #f1f3f7;
        }

        .invoice-header-top {
            height: 176px;
            padding-top: 35px;
        }

        .invoice-header-strip {
            width: 100%;
            height: 104px;
            background: #f1f3f7;
            border-bottom: 1px solid #e3e8ef;
        }

        .invoice-header-details {
            padding-top: 0;
        }

        .invoice-header-table,
        .invoice-footer-table {
            width: 100%;
            table-layout: fixed;
        }

        .invoice-party {
            width: 50%;
            vertical-align: top;
        }

        .invoice-party-right {
            text-align: right;
        }

        .invoice-logo {
            width: 42px;
            height: 42px;
            margin-bottom: 18px;
            line-height: 0;
        }

        .invoice-logo svg {
            display: block;
            width: 42px;
            height: 42px;
        }

        .invoice-number,
        .invoice-date {
            display: block;
            color: #374151;
            font-size: 10px;
            font-weight: 400;
            line-height: 14px;
        }

        .invoice-label {
            display: block;
            margin-top: 12px;
            color: #9ca3af;
            font-size: 10px;
            font-weight: 700;
            line-height: 13px;
        }

        .invoice-party-right .invoice-label {
            margin-top: 72px;
        }

        .invoice-name {
            display: block;
            margin-top: 3px;
            color: #374151;
            font-size: 15px;
            font-weight: 700;
            line-height: 20px;
        }

        .invoice-address {
            width: 270px;
            margin-top: 0;
            color: #374151;
            font-size: 10px;
            font-weight: 400;
            line-height: 15px;
        }

        .invoice-party-right .invoice-address {
            margin-left: 82px;
        }

        .invoice-contact {
            margin-top: 12px;
            color: #374151;
            font-size: 10px;
            font-weight: 400;
            line-height: 15px;
        }

        .invoice-items-section {
            margin-top: 35px;
        }

        .invoice-items {
            width: 100%;
            table-layout: fixed;
            color: #374151;
            font-size: 10px;
        }

        .invoice-items th {
            color: #1f2937;
            font-size: 10px;
            font-weight: 700;
            line-height: 16px;
            padding: 0 0 18px;
            text-align: left;
        }

        .invoice-items td {
            border-bottom: 1px solid #e6e7eb;
            line-height: 14px;
            padding: 5px 0 5px;
            vertical-align: top;
        }

        .invoice-item-row-last td {
            border-bottom: 0;
        }

        .invoice-col-description {
            width: 50%;
            text-align: left;
        }

        .invoice-col-qty {
            width: 10%;
            text-align: left;
        }

        .invoice-col-price,
        .invoice-col-subtotal {
            width: 20%;
            text-align: right;
        }

        .invoice-summary-section {
            margin-top: 70px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .invoice-summary-layout {
            width: 100%;
            table-layout: fixed;
        }

        .invoice-summary-spacer {
            width: 72.5%;
        }

        .invoice-summary-box {
            width: 27.5%;
            vertical-align: top;
        }

        .invoice-summary-lines,
        .invoice-total-table,
        .invoice-payment-table {
            width: 100%;
            table-layout: fixed;
        }

        .invoice-summary-lines td {
            color: #374151;
            font-size: 10px;
            line-height: 15px;
            padding: 0 0 5px;
        }

        .invoice-summary-label {
            text-align: left;
        }

        .invoice-summary-value {
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .invoice-total-table {
            margin-top: 15px;
        }

        .invoice-total-label,
        .invoice-total-value {
            background: #000000;
            color: #ffffff;
            line-height: 16px;
            padding: 12px 15px;
        }

        .invoice-total-label {
            width: 41%;
            border-bottom-left-radius: 8px;
            border-top-left-radius: 8px;
            color: #9ca3af;
            font-size: 10px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
        }

        .invoice-total-value {
            width: 59%;
            border-bottom-right-radius: 8px;
            border-top-right-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .invoice-payment-table {
            margin-top: 14px;
        }

        .invoice-payment-brand {
            width: 48%;
            color: #25378f;
            font-size: 15px;
            font-style: italic;
            font-weight: 700;
            line-height: 15px;
            text-align: left;
            vertical-align: top;
        }

        .invoice-payment-number {
            width: 52%;
            color: #374151;
            font-size: 10px;
            font-weight: 400;
            line-height: 15px;
            text-align: right;
            vertical-align: top;
            white-space: nowrap;
        }

        .invoice-visa-mark {
            display: block;
            width: 46px;
            height: 15px;
            line-height: 0;
        }

        .invoice-visa-mark svg {
            display: block;
            width: 46px;
            height: 15px;
        }

        .invoice-card-brand {
            color: #25378f;
            font-size: 15px;
            font-style: italic;
            font-weight: 700;
            line-height: 15px;
        }

        .invoice-footer {
            margin-top: 218px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .invoice-footer-note {
            width: 52%;
            color: #374151;
            font-size: 10px;
            line-height: 15px;
            vertical-align: top;
        }

        .invoice-footer-tax {
            width: 48%;
            color: #374151;
            font-size: 10px;
            line-height: 15px;
            text-align: right;
            vertical-align: bottom;
        }

        .invoice-strong {
            color: #374151;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main class="invoice-page">
        <section class="invoice-header">
            <div class="invoice-container invoice-header-top">
                <table class="invoice-header-table">
                    <tr>
                        <td class="invoice-party">
                            <div class="invoice-logo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/></svg>
                            </div>

                            <span class="invoice-number">Invoice #: {{ $invoice_data['invoice_number'] ?? '' }}</span>
                            <span class="invoice-label">Merchant</span>
                            <span class="invoice-name">{{ $merchant['name'] ?? '' }}</span>
                        </td>

                        <td class="invoice-party invoice-party-right">
                            <span class="invoice-date">Date: {{ now($invoice_timezone)->format('M. d, Y \a\t g:ia') }}</span>
                            <span class="invoice-label">Customer</span>
                            <span class="invoice-name">{{ $customer['name'] ?? '' }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="invoice-header-strip">
                <div class="invoice-container invoice-header-details">
                    <table class="invoice-header-table">
                        <tr>
                            <td class="invoice-party">
                                <p class="invoice-address">{{ $merchant['address'] ?? '' }}</p>
                                <div class="invoice-contact">
                                    <p>Phone: {{ $merchant['phone'] ?? '' }}</p>
                                    <p>Email: {{ $merchant['email'] ?? '' }}</p>
                                </div>
                            </td>

                            <td class="invoice-party invoice-party-right">
                                <p class="invoice-address">{{ $customer['address'] ?? '' }}</p>
                                <div class="invoice-contact">
                                    <p>Phone: {{ $customer['phone'] ?? '' }}</p>
                                    <p>Email: {{ $customer['email'] ?? '' }}</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

        <section class="invoice-container invoice-items-section">
            <table class="invoice-items">
                <thead>
                    <tr>
                        <th class="invoice-col-description">Description</th>
                        <th class="invoice-col-qty">Qty</th>
                        <th class="invoice-col-price">Unit Price</th>
                        <th class="invoice-col-subtotal">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice_items as $item)
                        <tr class="{{ $loop->last ? 'invoice-item-row invoice-item-row-last' : 'invoice-item-row' }}">
                            <td class="invoice-col-description">{{ $item['description'] }}</td>
                            <td class="invoice-col-qty">{{ $item['quantity'] ?? '' }}</td>
                            <td class="invoice-col-price">${{ number_format($item['price'], 2) }}</td>
                            <td class="invoice-col-subtotal">${{ number_format($item['line_total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="invoice-container invoice-summary-section">
            <table class="invoice-summary-layout">
                <tr>
                    <td class="invoice-summary-spacer"></td>
                    <td class="invoice-summary-box">
                        <table class="invoice-summary-lines">
                            <tr class="invoice-summary-row">
                                <td class="invoice-summary-label">Subtotal</td>
                                <td class="invoice-summary-value">${{ number_format($total_price, 2) }}</td>
                            </tr>
                            <tr class="invoice-summary-row">
                                <td class="invoice-summary-label">Tax Rate</td>
                                <td class="invoice-summary-value">{{ number_format($tax_rate * 100, 0) }}%</td>
                            </tr>
                            <tr class="invoice-summary-row">
                                <td class="invoice-summary-label">Tax Amount</td>
                                <td class="invoice-summary-value">${{ number_format($tax_amount, 2) }}</td>
                            </tr>
                        </table>

                        <table class="invoice-total-table">
                            <tr class="invoice-total-row">
                                <td class="invoice-total-label">Total</td>
                                <td class="invoice-total-value">${{ number_format($grand_total, 2) }}</td>
                            </tr>
                        </table>

                        <table class="invoice-payment-table">
                            <tr class="invoice-payment-row">
                                <td class="invoice-payment-brand">
                                    @if ($card_type === 'VISA')
                                        <span class="invoice-visa-mark">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="46" height="15" viewBox="0 0 64 20" aria-label="VISA" role="img">
                                                <path fill="#f5a623" d="M0 0h14.5l-2.1 4.3H0z"/>
                                                <path fill="#25378f" d="M14.4 1 8.6 19H2.8L0 5.5h5.2l1.4 8.8L9.6 1h4.8Zm8.8 0-3.7 18h-5.2L18 1h5.2Zm18.5.9-.8 4.2c-1.2-.6-2.9-1-4.9-1-2.2 0-3.4.7-3.4 1.8 0 1 .9 1.5 3.3 2.6 3.6 1.6 5.1 3.3 5 5.6-.1 3.8-3.6 5.8-8.6 5.8-2.2 0-4.4-.5-5.7-1.2l.8-4.4c1.4.7 3.3 1.3 5.3 1.3 2.2 0 3.4-.7 3.4-1.9 0-1-.7-1.6-3.1-2.7-3.5-1.6-5.1-3.2-5.1-5.5C27.9 3.3 31.2.6 36.3.6c2 0 3.8.4 5.4 1.3ZM56.8 1 61 19h-5.1l-.6-3h-6.4l-1.4 3H42L51.4 1h5.4Zm-2.1 10.8-1-5.2-2.5 5.2h3.5Z"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="invoice-card-brand">{{ $card_label }}</span>
                                    @endif
                                </td>
                                <td class="invoice-payment-number">{{ $invoice_data['masked_card_number'] ?? '' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>

        @if (isset($invoice_data['footer_note']) && $invoice_data['footer_note'] !== '')
            <footer class="invoice-container invoice-footer">
                <table class="invoice-footer-table">
                    <tr>
                        <td class="invoice-footer-note">
                            <span class="invoice-strong">Dear customer,</span>
                            <p>{{ $invoice_data['footer_note'] }}</p>
                        </td>
                        <td class="invoice-footer-tax">
                            <span class="invoice-strong">Tax ID/VAT Number: {{ $invoice_data['tax_id'] ?? '' }}</span>
                        </td>
                    </tr>
                </table>
            </footer>
        @endif
    </main>
</body>
</html>
