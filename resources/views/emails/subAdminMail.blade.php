<!DOCTYPE html>
<html>

<head>
    <title>Desi-Trainer</title>
</head>

<body>
    <p>Hello {{ $user['full_name'] ?? ''}}</p>
    <p>New user is created, Please click on link and login to system {{ env('APP_URL') }}</p>
    <p>Username : {{ $user->email ?? ''}}</p>
    <p>Password : {{ $user->plainPassword ?? ''}}</p>

    <p>Thank you</p>
</body>

</html>