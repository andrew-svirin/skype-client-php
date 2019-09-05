<?php

namespace AndrewSvirin\SkypeClient\Models;

/**
 * Property for class @see Session that holds data retrieved from Registration Token.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class RegistrationToken
{
   /**
    * @var string
    */
   private $registrationToken;

   /**
    * @var array
    */
   private $response;

   /**
    * Uses for Client communication with Skype Server.
    * @var string
    */
   private $messengerUrl;

   /**
    * @return string
    */
   public function getRegistrationToken(): string
   {
      return $this->registrationToken;
   }

   /**
    * @param string $value
    */
   public function setRegistrationToken(string $value)
   {
      $this->registrationToken = $value;
   }

   /**
    * @return array
    */
   public function getResponse(): array
   {
      return $this->response;
   }

   /**
    * @param array $value
    */
   public function setResponse(array $value): void
   {
      $this->response = $value;
   }

   /**
    * @return string
    */
   public function getMessengerUrl(): string
   {
      return $this->messengerUrl;
   }

   /**
    * @param string $value
    */
   public function setMessengerUrl(string $value): void
   {
      $this->messengerUrl = $value;
   }

}