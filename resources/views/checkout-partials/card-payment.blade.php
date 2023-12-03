<form action="/checkout-example" method="POST" id="payment-form" class="grid grid-cols-6 gap-4 bg-white shadow-sm rounded-xl p-6">
    @csrf
    {{-- Amount to pay: This element was hidden --}}
    <div class="gap-x-4 col-span-6">
        <div class="flex flex-col w-full">
            <div class="flex gap-4 items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-blue-500">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                </svg>

                <span class="text-sm">You can enter a pre-defined <span class="font-bold">'failure charge amount'</span> to simulate failed charges. <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions" class="text-blue-500 border-b border-blue-500" target="_tab">Failed charge scenarios</a></span>
            </div>

            <label for="amount-to-pay" class="text-sm uppercase font-bold text-gray-500">Amount to pay</label>
            <div class="flex flex-col">
                <div class="flex">
                    <input type="text" id="amount-to-pay" name="amount" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="PHP" value="5198">
                </div>
                <span class="text-xs text-gray-500 mt-1"><strong>Note:</strong> The "amount to pay" field, doesn't need to be included in the checkout UI. This is shown here so you could easily test different amount values and failure scenarios.</span>
            </div>
        </div>
    </div>

    {{-- Card number --}}
    <div class="flex gap-x-4 col-span-3">
        <div class="flex flex-col w-full">
            <label for="card-number" class="text-sm uppercase font-bold text-gray-500">Card number</label>
            <div class="flex flex-col">
                <div class="flex">
                    <input type="text" id="card-number" name="card-number" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="4XXXXXXXXXXX1091" value="5200000000001005">
                </div>
            </div>
        </div>
    </div>

    {{-- Expiry Date --}}
    <div class="flex gap-x-4 col-span-2">
        <div class="flex flex-col ">
            <label for="card-exp-month" class="text-sm uppercase font-bold text-gray-500">Expiry Date</label>
            <div class="flex gap-x-4 bg-gray-100 rounded-xl">
                <div class="flex w-3/4">
                    <input type="text" id="card-exp-month" name="card-exp-month" class="w-full bg-gray-100 p-3 rounded-xl outline-none text-center focus:ring focus:ring-blue-400" placeholder="MM" value="12">
                </div>
                <div class="flex">
                    <input type="text" id="card-exp-year" name="card-exp-year" class="w-full bg-gray-100 p-3 rounded-xl outline-none text-center focus:ring focus:ring-blue-400" placeholder="YYYY" value="2030">
                </div>
            </div>
        </div>
    </div>

    {{-- CVV --}}
    <div class="flex gap-x-4 col-span-1">
        <div class="flex flex-col">
            <label for="card-cvn" class="text-sm uppercase font-bold text-gray-500">CVV</label>
            <div class="flex gap-x-4">
                <div class="flex">
                    <input type="text" id="card-cvn" name="card-cvn" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="CVV" value="123">
                </div>
            </div>
        </div>
    </div>

    {{-- Button for generating the tokenized value of card details. --}}
    <button id="charge-card-btn" type="button" class="submit col-span-6 bg-gray-900 text-white rounded-xl p-4 text-sm uppercase font-bold disabled:hover:bg-gray-900 disabled:opacity-75 hover:bg-gray-600">
        <span id="pay-label">Charge Card</span>
        <span id="processing" class="hidden">Processing...</span>
    </button>
</form>
