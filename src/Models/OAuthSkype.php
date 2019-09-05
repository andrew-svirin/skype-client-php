<?php

namespace AndrewSvirin\SkypeClient\Models;

/**
 * Property for class @see Session that holds data retrieved from OAuth Skype.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class OAuthSkype
{
   /**
    * @var string
    */
   private $token;

   /**
    * @return string
    */
   public function getToken(): string
   {
      return $this->token;
   }

   /**
    * @param string $value
    */
   public function setToken(string $value)
   {
      $this->token = $value;
   }

}