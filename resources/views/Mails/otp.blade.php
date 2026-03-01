@component('mail::message')
Dear {{$email}},

Kindly use the OTP below to verify your Account.

@component('mail::panel')
# <center>{{$otp}}</center>
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
