<?php
/**
 * Fichier : config/mail.php
 * Rôle : Envoi d'e-mails via PHPMailer et SMTP Gmail.
 */

require_once __DIR__ . '/../libs/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer.php';
require_once __DIR__ . '/../libs/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerMailLocaFiara($destinataire, $nomDestinataire, $sujet, $messageHtml) {
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yowaneluc50@gmail.com';     
        $mail->Password   = 'ugvt bujd ixku zbmy';             
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Expéditeur et Destinataire
        $mail->setFrom('yowaneluc50@gmail.com', 'LocaFiara Madagascar'); // ⚠️ Mets ton adresse Gmail ici
        $mail->addAddress($destinataire, $nomDestinataire);

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $messageHtml;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $messageHtml));

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}