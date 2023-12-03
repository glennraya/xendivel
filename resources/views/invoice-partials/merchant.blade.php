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
            <span class="text-base">{{ $invoice_data['merchant_name'] }}</span>
        </span>

        {{-- Address --}}
        <p>{{ $invoice_data['merchant_address'] }}</p>

        {{-- Contacts --}}
        <div class="flex flex-col">
            <p>Phone: {{ $invoice_data['merchant_phone'] }}</p>
            <p>Email: {{ $invoice_data['merchant_email'] }}</p>
        </div>
    </div>
</div>
