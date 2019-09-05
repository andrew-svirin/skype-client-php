<?php

namespace AndrewSvirin\SkypeClient\Factories;

use AndrewSvirin\SkypeClient\Models\RegistrationToken;

/**
 * Class SessionFactory manages conversion RegistrationToken to data and reverse.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class RegistrationTokenFactory
{

   const FIELD_REGISTRATION_TOKEN = 'registrationToken';
   const FIELD_MESSENGER_URL = 'response';
   const FIELD_RESPONSE = 'response';

   /**
    * @param array $data
    * @return RegistrationToken
    */
   public static function buildRegistrationTokenFromData(array $data): RegistrationToken
   {
      $result = new RegistrationToken();
      $result->setRegistrationToken($data[self::FIELD_REGISTRATION_TOKEN]);
      $result->setMessengerUrl($data[self::FIELD_MESSENGER_URL]);
      return $result;
   }

   /**
    * @param RegistrationToken $registrationToken
    * @return array
    */
   public static function buildDataFromRegistrationToken(RegistrationToken $registrationToken): array
   {
      $result = [
         self::FIELD_REGISTRATION_TOKEN => $registrationToken->getRegistrationToken(),
         self::FIELD_RESPONSE => $registrationToken->getResponse(),
         self::FIELD_MESSENGER_URL => $registrationToken->getMessengerUrl(),
      ];
      return $result;
   }

}