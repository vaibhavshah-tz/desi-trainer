<!DOCTYPE html>
<html>

<head>
    <title>Desi-Trainer</title>
</head>

<body>
    <p>Please verify the OTP and reset the password.</p>
    <p>OTP is {{ $user['reset_password_token'] }}</p>
    <p>OTP is expired in {{ config('constants.EMAIL_OTP_EXPIRED_MINUTES') }} minutes.</p>
    <p>Thank you</p>
</body>

</html>