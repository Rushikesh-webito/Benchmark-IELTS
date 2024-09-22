<!DOCTYPE html>
<html>
<head>
    <title>Set Your Password</title>
</head>
<body>
    <h1>Hello, {{ $user->email }}!</h1>
    <p>Thank you for registering. Click the link below to set your password:</p>
    <a href="{{ $link }}">Set Password</a>
    <p>If you did not request this, please ignore this email.</p>
</body>
</html>
