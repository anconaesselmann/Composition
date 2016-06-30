<?php
/**
 *
 */
namespace aae\message {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\message
	 */
	class Email {
		public function send($senderAddress, $senderName, $receiverAddress, $subject, $bodyHmtl, $bodyPlainText = "") {
            $subject   = $subject;
            $eMessage  = $bodyHmtl;

            $headers   = array();
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=ISO-8859-1";
            $headers[] = "From: $senderName <$senderAddress>";
            $headers[] = "Subject: {$subject}";
            $headers[] = "X-Mailer: PHP/".phpversion();

            $mailSent  = mail($receiverAddress, $subject, $eMessage, implode("\r\n", $headers));

            return $mailSent;
        }
	}
}