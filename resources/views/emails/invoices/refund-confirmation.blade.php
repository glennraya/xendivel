<x-mail::message>
# Greetings!

@if ($message === null)
We would like to inform you that we had processed the refund to your account.
@else
{{ $message }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
