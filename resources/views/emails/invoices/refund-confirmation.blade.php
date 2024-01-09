<x-mail::message>
# Greetings!

@if ($message === null)
We've processed your refund. Our apologies for any inconvenience. If you need further assistance, please contact our support team.
@else
{{ $message }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
