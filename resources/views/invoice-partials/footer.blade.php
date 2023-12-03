@if (isset($invoice_data['footer_note']) && $invoice_data['footer_note'] !== '')
    <div class="container flex mx-auto mt-auto p-8">
        <div class="flex flex-col py-4">
            <span class="font-bold">Dear customer,</span>
            <p class="">{{ $invoice_data['footer_note']}}</p>
        </div>
    </div>
@endif
