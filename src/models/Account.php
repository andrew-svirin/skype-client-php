<?php

namespace AndrewSvirin\SkypeClient\models;

/**
 * Common account class that holds basic information.
 */
class Account
{

   /**
    * Username.
    *
    * @var string
    */
   private $username;

   /**
    * Password.
    *
    * @var string
    */
   private $password;

   public function __construct(string $username, string $password)
   {
      $this->username = $username;
      $this->password = $password;
   }

   /**
    * @return string
    */
   public function getUsername(): string
   {
      return $this->username;
   }

   /**
    * @return string
    */
   public function getPassword(): string
   {
      return $this->password;
   }

}