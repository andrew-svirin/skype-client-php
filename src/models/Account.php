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

   /**
    * Skype Account.
    *
    * @var SkypeAccount
    */
   private $skypeAccount;

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

   /**
    * @param SkypeAccount $skypeAccount
    */
   public function setSkypeAccount(SkypeAccount $skypeAccount)
   {
      $this->skypeAccount = $skypeAccount;
   }

   /**
    * @return SkypeAccount|null
    */
   public function getSkypeAccount(): ?SkypeAccount
   {
      return $this->skypeAccount;
   }

}