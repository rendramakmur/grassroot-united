<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Confirmation</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Registration Confirmation</h1>
        <p>
            Thank you for registering! Here are your registration details and please click Activation URL to activate your Grassroot United FC account:
        </p>
        <p>
            Email: <strong>{{ $userInformation->ui_email }}</strong><br>
            Registered at: <strong>{{ $userInformation->ui_created_at->format('Y-m-d H:i:s') }}</strong><br>
            Activation Code: {{ $userInformation->ui_activation_code }}<br>
            Activation URL: {{ $activationUrl }}
        </p>
        <p>
            If you have any questions or concerns, please don't hesitate to contact us.
        </p>
        <p>
            Regards,
        </p>
        <p>
            Grassroot United FC Team
        </p>
    </div>
</body>
</html>
