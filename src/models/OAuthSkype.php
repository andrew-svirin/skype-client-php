<?php

namespace AndrewSvirin\SkypeClient\models;

/**
 * Property for class @see Session that holds data retrieved from OAuth Skype.
 */
class OAuthSkype
{
   /**
    * @var string
    */
   private $t;

   /**
    * @return string
    */
   public function getT(): string
   {
      return $this->t;
   }

   /**
    * @param string $value
    */
   public function setT(string $value)
   {
      $this->t = $value;
   }

}