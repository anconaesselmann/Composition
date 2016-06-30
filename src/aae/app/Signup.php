<?php
/**
 *
 */
namespace aae\app {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app
	 */
	class Signup {
        private $_emailSender, $_url, $_senderName, $_senderEmailAddress, $_localizer;

        public function __construct(\aae\message\Email $email, $url, $senderEmailAddress, $senderName, $localizer) {
            $this->_emailSender        = $email;
            $this->_url                = $url;
            $this->_senderEmailAddress = $senderEmailAddress;
            $this->_senderName         = $senderName;
            $this->_localizer          = $localizer;
        }

        public function submit($user, $userName, $userEmail, $userPassword, $lang) {
            $code = $user->createUser($userName, $userPassword, $userEmail);
            if ($code) $success = $this->_sendRegistrationEmail($userEmail, $code, $lang);
            return $success;
        }

        public function confirm($user, $c, $e) {
            $email = urldecode($e);
            return $user->verify($email, $c);
        }

        public function _sendRegistrationEmail(
            $userEmail,
            $code,
            $lang
        ) {
            $linkText = $this->_localizer->localize("conf_email_link_text", "signup.json", $lang);
            $bodyText = $this->_localizer->localize("conf_email_body_text", "signup.json", $lang);
            $subjText = $this->_localizer->localize("conf_email_subj_text", "signup.json", $lang);

            $linkUrl  = $this->_url."/signup/confirm?"
                      . http_build_query(["e" => $userEmail, "c" => $code]);

            $link     = "<a href=\"$linkUrl\">$linkText</a>";

            $body     = sprintf($bodyText, $link);
            $subject  = sprintf($subjText, $this->_senderName);

            $success  = $this->_emailSender->send(
                $this->_senderEmailAddress,
                $this->_senderName,
                $userEmail,
                $subject,
                $body
            );
            if (!$success) throw new \aae\message\MessageException("Message was not sent.", 1113141147);
            return true;
        }
	}
}