<x-mail::message>
# Greetings!

<p>
@if ($message === null)
Thank you for your recent purchase from {{ config('app.name') }}. We have attached your invoice on this e-mail.
@else
{{ $message }}
@endif
</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
