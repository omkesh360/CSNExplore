<?php
include(W3SPEEDSTER_PATH . '/data/config.php');
$config = W3SPEEDSTER_CONFIG;

$loginLogFilePath = W3SPEEDSTER_PATH . '/data/login_activity.json';

if (!session_id()) {
    session_start();
}

$email = $password = "";
$email_err = $password_err = $login_err = "";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header('Location: ?admin=1&page=w3_speedster');
    exit;
}

function logLoginActivity($email, $loginLogFilePath)
{
    $log_data = [
        'username' => $email,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'datetime' => date('Y-m-d H:i:s')
    ];

    if (file_exists($loginLogFilePath)) {
        $current_data = json_decode(file_get_contents($loginLogFilePath), true);
    } else {
        $current_data = [];
    }

    $current_data[] = $log_data;

    @file_put_contents($loginLogFilePath, json_encode($current_data, JSON_PRETTY_PRINT));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        if (isset($config['users'][$email]) && $config['users'][$email]['password'] === md5($password)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $email;
            $_SESSION["name"] = $config['users'][$email]['name'];

            setcookie("w3user", $email, time() + (86400 * 30), "/");

            logLoginActivity($email, $loginLogFilePath);

            $current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (empty($_GET['admin'])) {
                $new_url = $current_url . (strpos($current_url, '?') === false ? '?' : '&') . "admin=1&page=w3_speedster";
                header("Location: " . $new_url);
                exit;
            }

            header("Location: " . $current_url);
            exit;
        } else {
            $login_err = "Invalid email or password.";
        }
    }
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W3speedster Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            font-size: medium;
        }

        .login-form {
            max-width: 570px;
            padding: 50px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px 20px #0001;
            width: calc(100% - 20px);
        }

        .user-login {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            background-image: linear-gradient(90deg, rgba(42, 172, 201, 1) 30%, rgba(42, 172, 201, 1) 30%, rgba(15, 133, 216, 1) 100%);
        }

        .user-login img.banner-image {
            position: absolute;
            max-width: 50vw;
            left: 0vw;
            top: -10vw;
            filter: brightness(0) opacity(0.03);
            z-index: 0;
        }


        .login-form img.logo-img {
            max-height: 60px;
            transform: translateX(-20px);
            margin-bottom: 10px;
        }

        .login-form form {
            position: relative;
        }

        p.erro {
            background: #ffe4e4;
            border-radius: 5px;
            font-size: 90%;
        }

        .login-form form label {
            cursor: pointer;
            user-select: none;
        }

        .login-form form input:focus {
            box-shadow: none;
        }

        .login-form form input#remeber_me {
            width: 16px;
            height: 16px;
        }

        img.banner-image.bottom {
            top: unset;
            bottom: 0;
            left: 90vw;
            max-width: 10vw;
            filter: brightness(0) opacity(0.05) invert(1);
        }

        @media(max-width:575px) {
            .login-form {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="user-login">
        <img class="banner-image" src="<?php echo W3SPEEDSTER_URL ?>assets/images/bg_logo.webp" alt="W3speedup logo banner">
        <img class="banner-image bottom" src="<?php echo W3SPEEDSTER_URL ?>assets/images/bg_logo.webp" alt="W3speedup logo banner2">
        <div class="login-form">
            <img class="logo-img mb-4" src="<?php echo W3SPEEDSTER_URL ?>assets/images/site_logo.webp" alt="W3speedup logo banner">
            <h3 class="mt-2">Login</h3>
            <p class="mb-4">Boost Your Website. Enhance Your Speed. Experience Clarity.</p>
            <?php if (!empty($login_err)) : ?> <p class="erro py-2 px-3 text-danger"><i class="bi bi-exclamation-triangle"></i> <?php echo $login_err; ?></p> <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group mb-3">
                    <label for="user_name">Email Address</label>
                    <input id="user_name" type="email" name="email" class="form-control" value="" placeholder="admin@example.com" required>
                    <small class="text-danger"><?php if (!empty($email_err)) echo $email_err; ?></small>
                </div>
                <div class="form-group mb-3">
                    <label for="user_pass">Password</label>
                    <div style="position: relative;">
                        <input id="user_pass" type="password" name="password" class="form-control" placeholder="Admin@1234" required>
                        <button title="view-pass" type="button" id="togglePass"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <i class="bi bi-eye"></i>
                        </button>
                        <small class="text-danger"><?php if (!empty($password_err)) echo $password_err; ?></small>
                    </div>
                </div>
                <div class="form-group d-flex align-items-center gap-1">
                    <input id="remeber_me" type="checkbox" name="checkbox">
                    <label for="remeber_me">&nbsp;Remember Me</label>

                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4 mb-3">Login</button>
                <small class="text-center d-block">Having trouble? <a href="https://w3speedster.com/" target="_blank"><strong>Need Help</strong></a> or <a href="?w3_forgot_password=1&page=w3_speedster"><strong>Forgot Password</strong></a></small>
            </form>
        </div>

    </div>
    <script>
        document.getElementById("togglePass").addEventListener("click", function() {
            let passwordInput = document.getElementById("user_pass");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                this.innerHTML = "<i class='bi bi-eye-slash'></i>";
            } else {
                passwordInput.type = "password";
                this.innerHTML = "<i class='bi bi-eye'></i>";
            }
        });
    </script>
</body>

</html>
