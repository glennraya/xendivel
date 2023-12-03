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
        <div class="flex w-auto bg-black font-medium px-4 py-2 text-white justify-between items-center rounded-lg">
            <span class="uppercase text-gray-400 font-bold mr-10">Total</span>
            <span class="text-base font-bold">${{ number_format($total_price, 2) }}</span>
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
