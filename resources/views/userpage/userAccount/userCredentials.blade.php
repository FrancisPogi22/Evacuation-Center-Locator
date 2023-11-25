<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html: charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        * {
            text-decoration: none;
        }

        body {
            background: #ecf0f1;
        }

        #btn-login {
            color: white;
            background: #3b82f6;
            padding: 14px;
            font-size: 14px;
            border-radius: 5px;
        }

        #last-hr {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <p>
        These are the login details for your admin account. You can modify your password after successfully logging in.
    </p>
    <hr>

    <p>Email Address: {{ $userCredentials['email'] }}</p>

    @if ($userCredentials['organization'] == 'CDRRMO')
        <p>Organization: Cabuyao Disaster Risk Reduction and Management Office ({{ $userCredentials['organization'] }})
        </p>
    @else
        <p>Organization: City Social Welfare and Developement ({{ $userCredentials['organization'] }})</p>
    @endif

    <p>Position: {{ $userCredentials['position'] }}</p>

    <p>Password: <font color="red">{{ $userCredentials['password'] }}</font>
    </p>

    <center>
        <a href="{{ route('/') }}" id="btn-login">Login With Credentials</a>
    </center>

    <hr id="last-hr">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
</body>

</html>
