<?php
require_once(W3SPEEDSTER_PATH . '/data/config.php');
if (!isset($config['users'], $config['smtp']) || !is_array($config)) {
    die('Configuration file is invalid or corrupted.');
}
$users = $config['users'];
$smtpConfig = $config['smtp'];
$resetFilePath = W3SPEEDSTER_PATH . '/data/reset_tokens.php';
$email = "";
$email_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($email_err)) {
        if (array_key_exists($email, $users)) {
            $token = bin2hex(random_bytes(50));
            $reset_link = str_replace('W3speedster/', '', W3SPEEDSTER_URL) . "?w3_reset_password=1&page=w3_speedster&token=" . $token;
            $token_data = [
                'email' => $email,
                'token' => $token,
                'created_at' => time()
            ];

            $tokens = file_exists($resetFilePath) ? json_decode(file_get_contents($resetFilePath), true) : [];
            $tokens[$email] = $token_data;

            if (file_put_contents($resetFilePath, json_encode($tokens, JSON_PRETTY_PRINT)) === false) {
                $email_err = "Something went wrong!";
            } else {
                if (
                    file_exists(W3SPEEDSTER_PATH . '/PHPMailer.php') &&
                    file_exists(W3SPEEDSTER_PATH . '/SMTP.php') &&
                    isset($smtpConfig['status']) && $smtpConfig['status']
                ) {
                    require 'PHPMailer.php';
                    require 'SMTP.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    try {
                        $mail->isSMTP();
                        $mail->Host = $smtpConfig['smtp_host'];
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtpConfig['smtp_username'];
                        $mail->Password = $smtpConfig['smtp_password'];
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = $smtpConfig['smtp_port'];
                        $mail->setFrom($smtpConfig['smtp_from'], $smtpConfig['smtp_name']);
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body = "Click the link to reset your password: <a href='$reset_link'>$reset_link</a>";
                        $mail->send();
                        $success_msg = "A password reset link has been sent to your email.";
                    } catch (Exception $e) {
                        $email_err = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    // Fallback PHP mail
                    $subject = "Password Reset Request";
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    $message = "<p>Click the link to reset your password: <a href='$reset_link'>$reset_link</a></p>";
                    if (mail($email, $subject, $message, $headers)) {
                        $success_msg = "A password reset link has been sent to your email.";
                    } else {
                        $email_err = "Email could not be sent.";
                    }
                }
            }
        } else {
            $email_err = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W3speedster Password Reset</title>
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

        .reset-form {
            max-width: 570px;
            padding: 50px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px 20px #0001;
            width: calc(100% - 20px);
        }

        .forgot-password {
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

        .forgot-password img.banner-image {
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
    <div class="forgot-password">
        <img class="banner-image" src="<?php echo W3SPEEDSTER_URL ?>assets/images/bg_logo.webp" alt="W3speedup logo banner">
        <img class="banner-image bottom" src="<?php echo W3SPEEDSTER_URL ?>assets/images/bg_logo.webp" alt="W3speedup logo banner2">
        <div class="reset-form">
            <img class="logo-img mb-4" src="<?php echo W3SPEEDSTER_URL ?>assets/images/site_logo.webp" alt="W3speedup logo banner">
            <h3 class="mt-2">Forgot Password</h3>
            <p>Please enter your email to reset your password.</p>
            <form action="" method="POST">
                <div class="form-group mb-3 <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span class="help-block text-danger"><?php echo $email_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-4 mb-3">Submit</button>
            </form>
            <?php if (!empty($success_msg)) : ?>
                <div class="alert alert-success">
                    <?= $success_msg; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>