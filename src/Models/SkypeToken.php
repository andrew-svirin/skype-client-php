<?php

namespace AndrewSvirin\SkypeClient\Models;

/**
 * Property for class @see Session that holds data retrieved from Skype Token.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class SkypeToken
{
   /**
    * @var string
    */
   private $skypeToken;

   /**
    * @return string
    */
   public function getSkypeToken(): string
   {
      return $this->skypeToken;
   }

   /**
    * @param string $value
    */
   public function setSkypeToken(string $value)
   {
      $this->skypeToken = $value;
   }

}