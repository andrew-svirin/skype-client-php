<?php

namespace AndrewSvirin\SkypeClient\models;

/**
 * Skype conversation can be used for account or group interactions with another account.
 */
class Conversation
{

   /**
    * Username.
    *
    * @var string
    */
   private $name;

   /**
    * Full Name.
    *
    * @var string
    */
   private $label;

   public function __construct(string $name, string $label)
   {
      $this->name = $name;
      $this->label = $label;
   }

   /**
    * @return string
    */
   public function getName(): string
   {
      return $this->name;
   }

   /**
    * First Name and Last Name.
    * @return string
    */
   public function getLabel(): string
   {
      return $this->label;
   }

   /**
    * @return int
    */
   public function getMode(): int
   {
      $mode = strstr($this->name, 'thread.skype') ? 19 : 8;
      return $mode;
   }

}