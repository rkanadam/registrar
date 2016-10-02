<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, user-scalable=yes">

    <script src="../bower_components/webcomponentsjs/webcomponents-lite.js"></script>
    <link rel="import" href="../bower_components/paper-card/paper-card.html">
    <link rel="import" href="../bower_components/google-signin/google-signin.html">

    <style type="text/css">
        body {
            margin: 0;
            font-family: 'Roboto'
            font-size: 12px;
        }
    </style>
</head>
<body>

<paper-card heading="Sign in" alt="Emmental">
    <div class="card-content">
        Sairam, Please login with
    </div>
    <div class="card-actions">
        <google-signin client-id="477636750840-9kmsln38ji1ramlf56uk12ktud92gas6.apps.googleusercontent.com"
                       scopes="email profile"
                       on-google-signed-out="_onLogout"
                       on-google-signin-success="_onLogin" with-backdrop
                       always-on-top></google-signin>
    </div>
</paper-card>
</body>
</html>