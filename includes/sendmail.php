<?php

$recaptcha_secretkey = "6LfF4zYeAAAAAHXzq-bRki1ME__LnfQMbeBrDlqw"; // Sostituisci con la tua chiave segreta

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
        $secretKey = $recaptcha_secretkey;

        if (!verifyReCaptcha($recaptchaResponse, $userIP, $secretKey)) {
            $message = '<strong>Errore!</strong> C\'è stato un problema con il Captcha.';
            $status = "false";
            echo json_encode(['message' => $message, 'status' => $status]);
            exit;
        }
    } else {
        $message = '<strong>Errore!</strong> reCAPTCHA non trovato.';
        $status = "false";
        echo json_encode(['message' => $message, 'status' => $status]);
        exit;
    }

    if ($_POST['form_name'] != '' && $_POST['form_email'] != '' && $_POST['form_subject'] != '') {
        require_once('phpmailer/class.phpmailer.php');
        require_once('phpmailer/class.smtp.php');

        $mail = new PHPMailer();

        // ✅ Aggiunto supporto UTF-8
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isSMTP();
        $mail->Host = 'mail.soluzioniwebdesign.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'roberto.riccardi@soluzioniwebdesign.com';
        $mail->Password = 'anacleto64599';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $name = $_POST['form_name'];
        $email = $_POST['form_email'];
        $subject = $_POST['form_subject'];
        $phone = $_POST['form_phone'];
        $message = $_POST['form_message'];

        $subject = $subject ?: 'Nuovo Messaggio | Modulo di Contatto';
        $botcheck = $_POST['form_botcheck'];
        $toemail = 'roberto.riccardi@soluzioniwebdesign.com';
        $toname = 'roberto.riccardi@soluzioniwebdesign.com';

        if ($botcheck == '') {
            $mail->SetFrom($toemail, $toname);
            $mail->AddReplyTo($email, $name);
            $mail->AddAddress($toemail, $toname);
            $mail->Subject = $subject;

            $name = $name ? "Nome: $name<br><br>" : '';
            $email = $email ? "Email: $email<br><br>" : '';
            $phone = $phone ? "Telefono: $phone<br><br>" : '';
            $message = $message ? "Messaggio: $message<br><br>" : '';

            $referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>Questa mail è stata inviata da: ' . $_SERVER['HTTP_REFERER'] : '';
            $body = "$name $email $phone $message $referrer";

            // ✅ Aggiunto anche isHTML (opzionale ma consigliato)
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            try {
                $sendEmail = $mail->Send();
                if ($sendEmail) {
                    $message = 'Il tuo messaggio <strong>è stato inviato con successo!</strong> Ti risponderemo il prima possibile.';
                    $status = "true";
                } else {
                    $message = 'L\'email <strong>non è stata inviata</strong>. Motivo: ' . $mail->ErrorInfo;
                    $status = "false";
                }
            } catch (Exception $e) {
                $message = 'Errore durante l\'invio. Dettagli: ' . $e->getMessage();
                $status = "false";
            }
        } else {
            $message = 'Bot <strong>rilevato</strong>.!';
            $status = "false";
        }
    } else {
        $message = 'Per favore <strong>compila tutti i campi</strong> e riprova.';
        $status = "false";
    }
} else {
    $message = 'Errore imprevisto. Riprova più tardi.';
    $status = "false";
}

echo json_encode(['message' => $message, 'status' => $status]);
?>
