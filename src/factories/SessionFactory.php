<?php

namespace AndrewSvirin\SkypeClient\factories;

use AndrewSvirin\SkypeClient\models\Account;
use AndrewSvirin\SkypeClient\models\Session;
use DateTime;

/**
 * Class SessionFactory manages conversion Session to data and reverse.
 */
class SessionFactory
{
   /**
    * Fields for cached data.
    */
   const FIELD_SKYPE_ACCOUNT = 'account';
   const FIELD_SKYPE_TOKEN = 'skypeToken';
   const FIELD_REGISTRATION_TOKEN = 'registrationToken';
   const FIELD_EXPIRY = 'expiry';


   /**
    * Produce Session from data array.
    * @param Account $account
    * @param array $data
    * @return Session
    */
   public static function buildSessionFromData(Account $account, array $data): Session
   {
      $result = new Session($account);
      $account->setSkypeAccount(SkypeAccountFactory::buildSkypeAccountFromData($data[self::FIELD_SKYPE_ACCOUNT]));
      $result->setSkypeToken(SkypeTokenFactory::buildSkypeTokenFromData($data[self::FIELD_SKYPE_TOKEN]));
      $result->setRegistrationToken(RegistrationTokenFactory::buildRegistrationTokenFromData($data[self::FIELD_REGISTRATION_TOKEN]));
      $result->setExpiry(DateTime::createFromFormat('U', (int)$data[self::FIELD_EXPIRY]));
      return $result;
   }

   /**
    * @param Session $session
    * @return array
    */
   public static function buildDataFromSession(Session $session): array
   {
      $result = [
         self::FIELD_SKYPE_ACCOUNT => SkypeAccountFactory::buildDataFromSkypeAccount($session->getAccount()->getSkypeAccount()),
         self::FIELD_SKYPE_TOKEN => SkypeTokenFactory::buildDataFromSkypeToken($session->getSkypeToken()),
         self::FIELD_REGISTRATION_TOKEN => RegistrationTokenFactory::buildDataFromRegistrationToken($session->getRegistrationToken()),
         self::FIELD_EXPIRY => $session->getExpiry()->format('U'),
      ];
      return $result;
   }
}