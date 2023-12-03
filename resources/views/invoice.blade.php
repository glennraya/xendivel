<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xendivel Invoice Template</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,900;0,9..40,1000;1,9..40,800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite('resources/css/main.css')

</head>
<body class="antialiased flex flex-col h-screen text-[10px] font-sans text-gray-700 tracking-tight">
    {{-- Header: This contains your company logo, name,
         address and other contact information. --}}
    <div class="w-full bg-gradient-to-t from-slate-200 via-white">
        <div class="container flex justify-between w-full mx-auto p-8">
            {{-- Merchant Info --}}
            @include('vendor.xendivel.views.invoice-partials.merchant')

            {{-- Customer Info --}}
            @include('vendor.xendivel.views.invoice-partials.customer')
        </div>
    </div>

    {{-- Invoice Details: This is where you'll typically place the details
         about the transaction such as the items, quantity, amount, etc.  --}}
    @include('vendor.xendivel.views.invoice-partials.items')

    {{-- Footer, thank you note. --}}
    @include('vendor.xendivel.views.invoice-partials.footer')
</body>
</html>
