<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function localMailConfig()
{
      static $config = null;

      if ($config === null) {
            $path = __DIR__ . '/config/mail.php';
            $config = file_exists($path) ? require $path : [];
      }

      return $config;
}

function mailConfig($key, $default = '')
{
      $value = getenv($key);
      if ($value !== false && $value !== '') {
            return $value;
      }

      $config = localMailConfig();
      $map = [
            'MAIL_HOST' => 'host',
            'MAIL_PORT' => 'port',
            'MAIL_USERNAME' => 'username',
            'MAIL_PASSWORD' => 'password',
            'MAIL_FROM' => 'from_email',
            'MAIL_FROM_NAME' => 'from_name',
      ];
      $configKey = $map[$key] ?? strtolower(str_replace('MAIL_', '', $key));

      return isset($config[$configKey]) && $config[$configKey] !== '' ? $config[$configKey] : $default;
}

function envoyerMail($to, $nomEnvoyeur, $colis, $dateReception)
{
      $smtpUser = mailConfig('MAIL_USERNAME', 'njaratianarabiaharison@gmail.com');
      $smtpPass = mailConfig('MAIL_PASSWORD');
      $fromEmail = mailConfig('MAIL_FROM', $smtpUser);
      $fromName = mailConfig('MAIL_FROM_NAME', 'Gestion Colis Coopérative');

      if ($smtpPass === '') {
            return [
                  'success' => false,
                  'message' => 'Mot de passe SMTP manquant. Configurez MAIL_PASSWORD avec un mot de passe d’application Gmail.'
            ];
      }

      $mail = new PHPMailer(true);

      try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = mailConfig('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) mailConfig('MAIL_PORT', '587');

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to, $nomEnvoyeur);

            $mail->isHTML(true);
            $mail->Subject = "Notification de réception de colis";
            $safeName = htmlspecialchars($nomEnvoyeur, ENT_QUOTES, 'UTF-8');
            $safeColis = htmlspecialchars($colis, ENT_QUOTES, 'UTF-8');
            $safeDate = htmlspecialchars($dateReception, ENT_QUOTES, 'UTF-8');

            $mail->Body = "
                  <h2>Bonjour {$safeName},</h2>
                  <p>Nous vous informons que votre colis a été reçu avec succès.</p>
                  <p><strong>Colis :</strong> {$safeColis}</p>
                  <p><strong>Date de réception :</strong> {$safeDate}</p>
                  <p>Merci pour votre confiance.</p>
                  <p><em>Coopérative de gestion des colis</em></p>
            ";
            $mail->AltBody = "Bonjour {$nomEnvoyeur}, votre colis '{$colis}' a été reçu le {$dateReception}.";

            $mail->send();
            return ['success' => true, 'message' => "Email envoyé à {$to}."];
      } catch (Exception $e) {
            error_log('Erreur envoi email: ' . $mail->ErrorInfo);
            return ['success' => false, 'message' => "Email non envoyé : {$mail->ErrorInfo}"];
      }
}
