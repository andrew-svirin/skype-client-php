<?php

namespace AndriySvirin\SkypeBot\models;

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

   public function __construct($username, $password)
   {
      $this->username = $username;
      $this->password = $password;
   }

   /**
    * @return string
    */
   public function getUsername()
   {
      return $this->username;
   }

   /**
    * @return string
    */
   public function getPassword()
   {
      return $this->password;
   }

}