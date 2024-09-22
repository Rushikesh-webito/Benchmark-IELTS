<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Benchmark</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: whitesmoke;
            /* Set to match your logo primary color */
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .header img {
            max-width: 150px;
        }

        .content {
            padding: 20px;
        }

        .content h1 {
            color: #0D47A1;
            /* Primary color */
        }

        .content p {
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            background-color: #0D47A1;
            /* Primary color */
            color: #fff;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            text-align: center;
            text-align: center;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f4f4f4;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://edubenchmark.com/images/benchmark-blue.png" alt="Benchmark Logo">
        </div>
        <div class="content">
            <h1>Welcome to Benchmark!</h1>
            <p>Hi {{ $user->name }},</p>
            <p>We are thrilled to have you onboard with Benchmark for your IELTS practice tests. Your account is now ready
                for you to start exploring.</p>
            <p>Please click the button below to create your password and access your account:</p>
            <a href="{{ $url }}" class="button" style="color: green;">Create Password</a>
            <p>Good luck with your preparation!</p>
        </div>
        <div class="footer">
            <p>If you have any questions, feel free to reach out to us at <a
                    href="mailto:info@edubenchmark.com">info@edubenchmark.com</a>.</p>
            <p>Thank you,<br>Benchmark PTE Team</p>
        </div>
    </div>
</body>

</html>