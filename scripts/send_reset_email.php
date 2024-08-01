<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Verifique se o email existe no banco de dados
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_token = bin2hex(random_bytes(16));
        $reset_token_expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Salve o token e a data de expiração no banco de dados
        $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_token_expiration = ? WHERE email = ?');
        $stmt->execute([$reset_token, $reset_token_expiration, $email]);

        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';
            $mail->Password = 'your-email-password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Destinatários
            $mail->setFrom('your-email@gmail.com', 'TheGate');
            $mail->addAddress($email);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = 'Click on the link to reset your password: <a href="http://localhost/fifi/scripts/reset_password.php?token=' . $reset_token . '">Reset Password</a>';
            
            $mail->send();
            echo 'A password reset link has been sent to your email address.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Email address not found.';
    }
}
?>
