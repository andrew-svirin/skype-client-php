<?php

namespace AndrewSvirin\SkypeClient\models;

/**
 * Property for class @see Session that holds data retrieved from Skype Token.
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