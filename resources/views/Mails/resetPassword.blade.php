@component('mail::message')

Hello <b>{{$email}}</b>,<br>
We got a request to reset your password, kindly click on the link below.
@component('mail::button', ['url' => $link])
Reset Password
@endcomponent
You can copy the link below to reset your password if the button above is disabled.
<br><br>

<b>{{$link}}</b><br>

If you didn't initiate this request , please ignore.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
