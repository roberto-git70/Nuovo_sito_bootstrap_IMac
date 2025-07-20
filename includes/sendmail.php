<?php
$recaptcha_secretkey = "6LfF4zYeAAAAAHXzq-bRki1ME__LnfQMbeBrDlqw"; // Chiave segreta reCAPTCHA

function verifyReCaptcha($recaptchaResponse, $userIP, $secretKey) {
    $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");
    return strstr($request, "true");
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";
$status = "false";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica reCAPTCHA
    if (isset($_POST['g-recaptcha-response'])) {
        $userIP = $_SERVER["REMOTE_ADDR"];
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        if (!verifyReCaptcha($recaptchaResponse, $userIP, $recaptcha_secretkey)) {
            echo json_encode(['message' => '<strong>Errore!</strong> Problema con il Captcha.', 'status' => $status]);
            exit;
        }
    } else {
        echo json_encode(['message' => '<strong>Errore!</strong> reCAPTCHA mancante.', 'status' => $status]);
        exit;
    }

    if (!empty($_POST['form_name']) && !empty($_POST['form_email']) && !empty($_POST['form_subject'])) {
        require_once('phpmailer/class.phpmailer.php');
        require_once('phpmailer/class.smtp.php');

        $mail = new PHPMailer();

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isSMTP();
        $mail->Host = 'mail.soluzioniwebdesign.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'roberto.riccardi@soluzioniwebdesign.com';
        $mail->Password = 'anacleto64599';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $name     = $_POST['form_name'];
        $email    = $_POST['form_email'];
        $subject  = $_POST['form_subject'] ?: 'Nuovo Messaggio | Modulo di Contatto';
        $phone    = $_POST['form_phone'] ?? '';
        $azienda  = $_POST['form_company'] ?? '';
        $settore  = $_POST['form_sector'] ?? '';
        $citta    = $_POST['form_city'] ?? '';
        $messageC = $_POST['form_message'] ?? '';
        $botcheck = $_POST['form_botcheck'] ?? '';

        $toemail = 'roberto.riccardi@soluzioniwebdesign.com';
        $toname  = 'Roberto Riccardi';

        if ($botcheck == '') {
            $mail->SetFrom($toemail, $toname);
            $mail->AddReplyTo($email, $name);
            $mail->AddAddress($toemail, $toname);
            $mail->Subject = $subject;
            $mail->isHTML(true);

            // Corpo email con interlinea maggiore e impaginazione migliorata
            $body = '<div style="line-height: 1.8; font-size: 15px;">';
            $body .= $name     ? "<strong>Nome:</strong> $name<br><br>" : '';
            $body .= $email    ? "<strong>Email:</strong> $email<br><br>" : '';
            $body .= $phone    ? "<strong>Telefono:</strong> $phone<br><br>" : '';
            $body .= $azienda  ? "<strong>Azienda:</strong> $azienda<br><br>" : '';
            $body .= $settore  ? "<strong>Settore:</strong> $settore<br><br>" : '';
            $body .= $citta    ? "<strong>Città:</strong> $citta<br><br>" : '';
            $body .= $messageC ? "<strong>Messaggio:</strong><br>$messageC<br><br>" : '';
            $body .= $_SERVER['HTTP_REFERER'] ? "<hr><em>Inviato da:</em> <a href='{$_SERVER['HTTP_REFERER']}'>{$_SERVER['HTTP_REFERER']}</a>" : '';
            $body .= '</div>';

            $mail->Body = $body;
            $mail->AltBody = strip_tags(str_replace("<br>", "\n", $body));

            try {
                if ($mail->Send()) {
                    $message = 'Messaggio <strong>inviato con successo!</strong> Grazie per averci contattato.';
                    $status = "true";
                } else {
                    $message = 'Errore nell\'invio: ' . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                $message = 'Eccezione PHPMailer: ' . $e->getMessage();
            }
        } else {
            $message = 'Bot <strong>rilevato</strong>.';
        }
    } else {
        $message = 'Compila <strong>tutti i campi obbligatori</strong>.';
    }
} else {
    $message = 'Metodo di invio non valido.';
}

echo json_encode(['message' => $message, 'status' => $status]);
?>
