<div class="flex flex-col bg-white p-8 rounded-xl shadow-sm divide-y divide-gray-200">
    <h2 class="text-xl font-bold mb-4">Items in your bag</h2>
    <div class="flex gap-4 py-4">
        <img src="{{ asset('images/macbook-pro.jpeg') }}" alt="MacBook Pro" class="w-24 rounded-xl">
        <div class="flex flex-col gap-2 w-full">
            <span class="flex justify-between font-bold w-full">
                <span class="inline-block">MacBook Pro 16" M3 Max 1TB</span>
                <span class="inline-block font-normal text-gray-500">Qty 1</span>
            </span>
            <span class="text-gray-500">$3,999.00</span>
        </div>
    </div>
    <div class="flex gap-4 py-4">
        <img src="{{ asset('images/iphone.jpg') }}" alt="iPhone" class="w-24 rounded-xl">
        <div class="flex flex-col gap-2 w-full">
            <span class="flex justify-between font-bold w-full">
                <span class="inline-block">iPhone 15 Pro Max</span>
                <span class="inline-block font-normal text-gray-500">Qty 1</span>
            </span>
            <span class="text-gray-500">$1,199.00</span>
        </div>
    </div>
    <div class="flex items-center justify-between py-4">
        <span>Your bag total is</span>
        <span class="font-bold">$5,198.00</span>
    </div>
    <div class="flex items-center justify-between pt-4">
        <span>Delivery</span>
        <span>FREE</span>
    </div>
</div>
