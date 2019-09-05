<?php

namespace AndrewSvirin\SkypeClient\Factories;

use AndrewSvirin\SkypeClient\Models\SkypeToken;

/**
 * Class SessionFactory manages conversion SkypeToken to data and reverse.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
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