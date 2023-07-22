@component('mail::message')
<img src="https://mappeddev.fra1.digitaloceanspaces.com/images/1688554964.jpg" alt="" width="100" height="100" style="margin-left: auto;margin-right: auto;display: block; width: 50%;">
<h1 style="font-weight: bold;text-align:center">Verify Your Email</h1>

Hi {{ $user->name }},
<br>
Welcome to Default Admin.
<br>
Please enter the OTP below to verify your email.
<br>
<h1 style="font-weight: bold">{{ $otp }}</h1>

If you did not sign up for Default Admin, please ignore this email or feel free to contact us at defaultadmin@henceforth.com.

Regards,<br>
Henceforth Solutions
@endcomponent
