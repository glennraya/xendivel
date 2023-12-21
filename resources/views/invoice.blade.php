<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xendivel Invoice Template</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,900;0,9..40,1000;1,9..40,800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite('resources/css/invoice.css')

</head>
<body class="antialiased flex flex-col h-screen text-[10px] font-sans text-gray-700 tracking-tight">
    {{-- Header: This contains the company logo, name,
         address and other contact information. --}}
    <div class="w-full bg-gradient-to-t from-slate-200 via-white">
        <div class="container flex justify-between w-full mx-auto p-8">
            {{-- Merchant Info --}}
            <div class="flex flex-col justify-between w-4/12">
                {{-- Example merchant logo: You should replace this with the
                     logo of the merchant's company or business. --}}
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85"/><path d="m7 16.5 5-3"/><path d="M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3"/><path d="m17 16.5 4.74-2.85"/><path d="M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15"/><path d="m12 8 4.74-2.85"/><path d="M12 13.5V8"/></svg>
                </div>

                {{-- Invoice # --}}
                <span class="font-light">Invoice #: {{ $invoice_data['invoice_number'] }}</span>

                {{-- Company name, address --}}
                <div class="flex flex-col gap-y-4 mt-3">
                    <span class="flex flex-col font-bold">
                        <span class="text-gray-400">Merchant</span>
                        <span class="text-base">{{ $invoice_data['merchant']['name'] }}</span>
                    </span>

                    {{-- Address --}}
                    <p>{{ $invoice_data['merchant']['address'] }}</p>

                    {{-- Contacts --}}
                    <div class="flex flex-col">
                        <p>Phone: {{ $invoice_data['merchant']['phone'] }}</p>
                        <p>Email: {{ $invoice_data['merchant']['email'] }}</p>
                    </div>
                </div>
            </div>


            {{-- Customer Info --}}
            <div class="flex flex-col justify-between w-4/12">
                {{-- Invoice date --}}
                <div class="flex flex-col">
                    <span class="font-light text-right">Date: {{ now()->format('M. d, Y \a\t g:ia') }}</span>
                </div>

                {{-- Customer details --}}
                <div class="flex flex-col gap-y-4 text-right mt-3">
                    <span class="flex flex-col font-bold">
                        <span class="text-gray-400">Customer</span>
                        <span class="text-base">{{ $invoice_data['customer']['name'] }}</span>
                    </span>

                    {{-- Address --}}
                    <p>{{ $invoice_data['customer']['address'] }}</p>

                    {{-- Contacts --}}
                    <div class="flex flex-col">
                        <p>Phone: {{ $invoice_data['customer']['phone'] }}</p>
                        <p>Email: {{ $invoice_data['customer']['email'] }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Invoice Details: This is where you'll typically place the details
         about the transaction such as the items, quantity, amount, etc.  --}}
    <div class="container mx-auto p-8">
        <table class="border-collapse w-full">
            <thead>
                <tr class="text-left">
                    <th class="pb-2">Description</th>
                    <th class="pb-2">Qty</th>
                    <th class="pb-2 text-right">Unit Price</th>
                    <th class="px-0 pb-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php
                    $total_price = 0;
                @endphp
                @foreach ($invoice_data['items'] as $item)
                    @php
                        $total_price += $item['price'] * $item['quantity'];
                    @endphp
                    <tr>
                        <td class="py-1">{{ $item['item']}}</td>
                        <td class="py-1">{{ $item['quantity'] }}</td>
                        <td class="py-1 text-right">${{ number_format($item['price'], 2) }}</td>
                        <td class="py-1 text-right">
                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="container flex justify-end mx-auto p-8">
        <div class="flex flex-col">
            <div class="flex flex-col w-full mb-4">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-bold">${{ number_format($total_price, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax Rate</span>
                    <span class="font-bold">12%</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax Amount</span>
                    @php
                        $tax_amount = $total_price * $invoice_data['tax_rate'];
                    @endphp
                    <span class="font-bold">${{ number_format($tax_amount, 2) }}</span>
                </div>
            </div>

            <div class="flex w-auto bg-black font-medium px-4 py-2 text-white justify-between items-center rounded-lg">
                <span class="uppercase text-gray-400 font-bold mr-10">Total</span>
                <span class="text-base font-bold">${{ number_format($total_price + $tax_amount, 2) }}</span>
            </div>
            <div class="flex items-center">
                {{-- You can customize the icons here depending on what the
                        payment method your customer used. --}}

                {{-- Visa icon --}}
                @if ($invoice_data['card_type'] === 'VISA')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" id="visa-credit-card" class="w-10" fill="currentColor"><g data-name="Visa credit card"><path fill="#273991" d="m12.77 32.28-1.69-8.17a2 2 0 0 0-2.2-1.5H1.06L1 23c6.08 1.46 10.1 5 11.77 9.28Zm5.68-9.49L13.51 35.4 13 33.5a19.77 19.77 0 0 0-7-7.66l4.51 15.41h5.33l7.94-18.46Zm7.38 0-3.15 18.5h5l3.16-18.5Zm33.09 0H55a2.5 2.5 0 0 0-2.64 1.54l-7.45 16.94h5.29s.86-2.28 1.06-2.78h6.45c.15.64.61 2.76.61 2.76H63Zm-6.21 11.92c.42-1.06 2-5.17 2-5.17s.42-1.06.67-1.76l.35 1.59 1.16 5.34ZM41 30.2c-1.75-.85-2.83-1.43-2.82-2.29s.91-1.59 2.88-1.59a9.08 9.08 0 0 1 3.77.71l.46.21.68-4a12.89 12.89 0 0 0-4.51-.77c-5 0-8.49 2.5-8.52 6.1 0 2.65 2.5 4.14 4.41 5S40 35.06 40 35.87c0 1.24-1.57 1.81-3 1.81a10.58 10.58 0 0 1-4.74-1l-.65-.3-.71 4.14a15.87 15.87 0 0 0 5.62 1c5.29 0 8.73-2.48 8.77-6.32-.01-2.08-1.35-3.68-4.29-5Z"></path><path fill="#f99f1b" d="M11.08 24.11a2 2 0 0 0-2.2-1.5H1.06L1 23c6.08 1.47 10.1 5 11.77 9.29Z"></path></g></svg>
                @endif

                {{-- Mastercard icon --}}
                @if ($invoice_data['card_type'] === 'MASTERCARD')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="mastercard" class="w-10" fill="currentColor"><path fill="#FF5F00" d="M15.245 17.831h-6.49V6.168h6.49v11.663z"></path><path fill="#EB001B" d="M9.167 12A7.404 7.404 0 0 1 12 6.169 7.417 7.417 0 0 0 0 12a7.417 7.417 0 0 0 11.999 5.831A7.406 7.406 0 0 1 9.167 12z"></path><path fill="#F79E1B" d="M24 12a7.417 7.417 0 0 1-12 5.831c1.725-1.358 2.833-3.465 2.833-5.831S13.725 7.527 12 6.169A7.417 7.417 0 0 1 24 12z"></path></svg>
                @endif

                {{-- Masked card number --}}
                <span class="ml-auto">{{ $invoice_data['masked_card_number'] }}</span>
            </div>
        </div>
    </div>


    {{-- Footer, thank you note. --}}
    @if (isset($invoice_data['footer_note']) && $invoice_data['footer_note'] !== '')
        <div class="container flex justify-between mx-auto mt-auto p-8">
            <div class="flex flex-col py-4 w-1/2">
                <span class="font-bold">Dear customer,</span>
                <p>{{ $invoice_data['footer_note']}}</p>
            </div>

            <div class="flex flex-col py-4 mt-auto">
                <span class="font-bold">Tax ID/VAT Number: {{ $invoice_data['tax_id'] }}</span>
            </div>
        </div>
    @endif

</body>
</html>
