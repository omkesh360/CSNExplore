<?php
$userFilePath = W3SPEEDSTER_PATH . '/data/config.php';
$resetFilePath = W3SPEEDSTER_PATH . '/data/reset_tokens.php';
$email = "";
$new_password = "";
$token = "";
$token_err = "";
$password_err = "";
$success_msg = "";
include($userFilePath);
if (!isset($config) || !is_array($config)) {
    $configData = [];
    $token_err = "Error loading user configuration.";
} else {
    $configData = $config;
}
$users = isset($configData['users']) ? $configData['users'] : [];
$smtpConfig = isset($configData['smtp']) ? $configData['smtp'] : [];
$tokens = [];
if (file_exists($resetFilePath)) {
    $tokenContent = file_get_contents($resetFilePath);
    if (!empty($tokenContent)) {
        $tokens = json_decode($tokenContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $tokens = [];
            $token_err = "Error loading reset tokens. Please contact administrator.";
        }
    }
}
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $emailFound = false;
    
    foreach ($tokens as $key => $token_data) {
        if ($token_data['token'] === $token) {
            $email = $token_data['email'];
            $emailFound = true;
            break;
        }
    }
    
    if (!$emailFound) {
        $token_err = "Invalid token or token has expired.";
    }
} else {
    $token_err = "Reset token is either missing or has expired.";
}
render_page:
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($token_err)) {
    if (empty(trim($_POST["new_password"]))) {
        $password_err = "Please enter a new password.";
    } else {
        $new_password = trim($_POST["new_password"]);
    } 
    if (empty($password_err)) {
        if (!empty($email)) {
            if (array_key_exists($email, $users)) {
                $configData['users'][$email]['password'] = md5($new_password);
            } else {
                $found = false;
                foreach ($users as $userKey => $userData) {
                    if (isset($userData['email']) && $userData['email'] === $email) {
                        $configData['users'][$userKey]['password'] = md5($new_password);
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $token_err = "User email not found in the system.";
                    goto render_page; 
                }
            }
            file_put_contents($userFilePath, '<?php $config = ' . var_export($configData, true) . ';');
            foreach ($tokens as $key => $token_data) {
                if ($token_data['email'] === $email) {
                    unset($tokens[$key]);
                    break;
                }
            }
            if (!empty($tokens)) {
                file_put_contents($resetFilePath, json_encode(array_values($tokens), JSON_PRETTY_PRINT));
            } else {
                if (file_exists($resetFilePath)) {
                    unlink($resetFilePath);
                }
            }
            
            $success_msg = "Your password has been reset successfully. You can now log in.";
            header("Location: ?admin=1&page=w3_speedster&tab=cache");
            exit();
        } else {
            $token_err = "Invalid token or email not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W3speedster Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600&display=swap" rel="stylesheet">
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

        .reset-form {
            max-width: 570px;
            padding: 50px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px 20px #0001;
            width: calc(100% - 20px);
        }

        .reset-password {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            background-image: linear-gradient(90deg, rgba(42, 172, 201, 1) 30%, rgba(42, 172, 201, 1) 30%, rgba(15, 133, 216, 1) 100%);
            min-height: 100vh;
        }

        .reset-password img.banner-image {
            position: absolute;
            max-width: 50vw;
            left: 0vw;
            top: -10vw;
            filter: brightness(0) opacity(0.03);
            z-index: 0;
        }

        .reset-form img.logo-img {
            max-height: 60px;
            transform: translateX(-20px);
            margin-bottom: 10px;
        }

        .reset-form form {
            position: relative;
        }

        p.erro {
            background: #ffe4e4;
            border-radius: 5px;
            font-size: 90%;
        }

        .reset-form form label {
            cursor: pointer;
            user-select: none;
        }

        .reset-form form input:focus {
            box-shadow: none;
        }

        img.banner-image.bottom {
            top: unset;
            bottom: 0;
            left: 90vw;
            max-width: 10vw;
            filter: brightness(0) opacity(0.05) invert(1);
        }

        @media(max-width:575px) {
            .reset-form {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-password">
        <img class="banner-image" src="<?php echo W3SPEEDSTER_URL ?>assets/images/bg_logo.webp" alt="W3speedup logo banner">
        <img class="banner-image bottom" src="<?php echo W3SPEEDSTER_URL ?>assets/images/bg_logo.webp" alt="W3speedup logo banner2">
        <div class="reset-form">
            <img class="logo-img mb-4" src="<?php echo W3SPEEDSTER_URL ?>assets/images/site_logo.webp" alt="W3speedup logo banner">
            <h3 class="mt-2">Reset Password</h3>
            <form action="" method="POST">
                <div class="form-group mb-3 <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label for="new_password">New Password</label>
                    <input id="new_password" type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>" required>
                    <span class="help-block text-danger"><?php echo $password_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-4 mb-3" <?php echo (!empty($token_err)) ? 'disabled' : null ?>>Reset Password</button>
                <?php if (!empty($success_msg)) : ?>
                    <div class="alert alert-success">
                        <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($token_err)) : ?>
                    <div class="alert alert-danger">
                        <?php echo $token_err; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
