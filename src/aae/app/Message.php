<?php
/**
 *
 */
namespace aae\app {
    use \aae\db\FunctionAPI as FAPI;
    /**
     * @author Axel Ancona Esselmann
     * @package aae\app
     */
    class Message {
        private $_storageAPI, $_emailSender;

        public function __construct(\aae\db\FunctionAPI $storageAPI, \aae\message\Email $email) {
            $this->_storageAPI  = $storageAPI;
            $this->_emailSender = $email;
        }

        public function insertMessage($user, $connectionId, $subject, $body, $sent = false) {
            $messageId = (int)$this->_storageAPI->insertMessage(
                $user->getEmail(),
                $connectionId,
                $subject,
                $body,
                $sent
            );
            if ($messageId < 1) throw new \aae\db\StorageAPIException("Message was not inserted.", 1113141047);
            return $messageId;
        }

        public function sendEmail(
            $user,
            $connectionId,
            $subject,
            $body,
            $mailServiceSenderEmail
        ) {
            $messageId = $this->insertMessage(
                $user,
                $connectionId,
                $subject,
                $body,
                true
            );

            $senderAddress = $mailServiceSenderEmail;
            $senderName    = $this->_storageAPI->getOwnDisplayNameForConnection(
                $user->getEmail(),
                $connectionId
            );

            $receiverAddress = $this->getEmailAddressFromConnection(
                $user,
                $connectionId
            );

            $success = $this->_emailSender->send(
                $senderAddress,
                $senderName,
                $receiverAddress,
                sprintf($subject, $senderName),
                $body
            );
            if (!$success) throw new \aae\message\MessageException("Message was not sent.", 1113141147);
            return true;
        }

        public function getEmailAddressFromConnection($user, $connectionId) {
            $email = $this->_storageAPI->getEmailFromConnection($user->getEmail(), $connectionId);
            if (is_string($email) && strlen($email) < 6) throw new \aae\message\MessageException("Email could not be retrieved.", 11131414);
            return $email;
        }

        public function getMessage($user, $messageId) {
            $this->_storageAPI->setFetchMode(FAPI::FETCH_ASS_ARRAY | FAPI::FETCH_ONE_ROW);
            $message = $this->_storageAPI->getMessage($user->getEmail(), $messageId);
            $this->_storageAPI->setFetchMode(FAPI::RESET);
            return $message;
        }
    }
}