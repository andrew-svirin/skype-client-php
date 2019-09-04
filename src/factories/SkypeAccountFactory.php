<?php

namespace AndrewSvirin\SkypeClient\factories;

use AndrewSvirin\SkypeClient\models\RegistrationToken;
use AndrewSvirin\SkypeClient\models\SkypeAccount;

/**
 * Class SkypeAccountFactory manages conversion SkypeAccount to data and reverse.
 */
class SkypeAccountFactory
{

   const FIELD_USERNAME = 'username';
   const FIELD_FULL_NAME = 'fullName';

   /**
    * @param array $data
    * @return SkypeAccount
    */
   public static function buildSkypeAccountFromData(array $data): SkypeAccount
   {
      $result = new SkypeAccount($data[self::FIELD_USERNAME], $data[self::FIELD_FULL_NAME]);
      return $result;
   }

   /**
    * @param SkypeAccount $skypeAccount
    * @return array
    */
   public static function buildDataFromSkypeAccount(SkypeAccount $skypeAccount): array
   {
      $result = [
         self::FIELD_USERNAME => $skypeAccount->getUsername(),
         self::FIELD_FULL_NAME => $skypeAccount->getFullName(),
      ];
      return $result;
   }
}