<?php

namespace AndrewSvirin\SkypeClient\factories;

use AndrewSvirin\SkypeClient\models\SkypeToken;

/**
 * Class SessionFactory manages conversion SkypeToken to data and reverse.
 */
class SkypeTokenFactory
{

   const FIELD_SKYPE_TOKEN = 'skypeToken';

   /**
    * @param array $data
    * @return SkypeToken
    */
   public static function buildSkypeTokenFromData(array $data): SkypeToken
   {
      $result = new SkypeToken();
      $result->setSkypeToken($data[self::FIELD_SKYPE_TOKEN]);
      return $result;
   }

   /**
    * @param SkypeToken $skypeToken
    * @return array
    */
   public static function buildDataFromSkypeToken(SkypeToken $skypeToken): array
   {
      $result = [
         self::FIELD_SKYPE_TOKEN => $skypeToken->getSkypeToken(),
      ];
      return $result;
   }
}