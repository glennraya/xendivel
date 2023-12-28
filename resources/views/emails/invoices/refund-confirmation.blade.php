<x-mail::message>
# Greetings!

@if ($message === null)
We've processed your refund, and you should see the amount in your account within 3-5 business days. Our apologies for any inconvenience. If you need further assistance, please contact our support team.
@else
{{ $message }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
