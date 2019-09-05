<?php

namespace AndrewSvirin\SkypeClient\Utils;

use AndrewSvirin\SkypeClient\Exceptions\ClientException;

/**
 * Manage environment variables.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class EnvUtil
{

   /**
    * Get secret.
    * @return string
    * @throws ClientException
    */
   public static function getSecret(): string
   {
      if (!($secret = getenv('SECRET')))
      {
         throw new ClientException('Should be specified SECRET env var.');
      }
      return $secret;
   }

   /**
    * User 1 details. Optional.
    * @return array
    */
   public static function getUser1(): ?array
   {
      return is_string(getenv('DEBUG_USER_1')) ? explode(':', getenv('DEBUG_USER_1'), 2) : null;
   }

   /**
    * User 2 details. Optional.
    * @return array
    */
   public static function getUser2(): ?array
   {
      return is_string(getenv('DEBUG_USER_2')) ? explode(':', getenv('DEBUG_USER_2'), 2) : null;
   }

}