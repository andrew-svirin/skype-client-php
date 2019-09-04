<?php

namespace AndrewSvirin\SkypeClient\models;

/**
 * Skype account for Skype interactions with account.
 */
class SkypeAccount
{

   /**
    * Username.
    *
    * @var string
    */
   private $username;

   /**
    * Full Name.
    *
    * @var string
    */
   private $fullName;

   public function __construct(string $username, string $fullName)
   {
      $this->username = $username;
      $this->fullName = $fullName;
   }

   /**
    * @return string
    */
   public function getUsername(): string
   {
      return $this->username;
   }

   /**
    * First Name and Last Name.
    * @return string
    */
   public function getFullName(): string
   {
      return $this->fullName;
   }

}