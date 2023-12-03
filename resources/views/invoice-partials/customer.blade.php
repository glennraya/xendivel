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
