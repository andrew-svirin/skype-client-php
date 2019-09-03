<?php

namespace AndrewSvirin\SkypeClient\models;

use DateTime;

/**
 * Decorator for class @see Account that holds data related for cache.
 */
class Session
{

   /**
    * Account.
    *
    * @var Account
    */
   private $account;

   /**
    * DateTime until the cache is valid.
    *
    * @var DateTime
    */
   private $expiry;

   /**
    * @var SkypeToken
    */
   private $skypeToken;

   /**
    * @var RegistrationToken
    */
   private $registrationToken;

   /**
    * @var OAuthMicrosoft
    */
   private $oAuthMicrosoft;

   /**
    * @var OAuthMicrosoftRedirect
    */
   private $oAuthMicrosoftRedirect;

   /**
    * @var OAuthSkype
    */
   private $oAuthSkype;

   public function __construct(Account $account)
   {
      $this->account = $account;
   }

   /**
    * @return Account
    */
   public function getAccount()
   {
      return $this->account;
   }

   /**
    * @return DateTime
    */
   public function getExpiry()
   {
      return $this->expiry;
   }

   /**
    * @param DateTime $expiry
    */
   public function setExpiry(DateTime $expiry)
   {
      $this->expiry = $expiry;
   }

   /**
    * Check if @see Session is New.
    * @return bool
    */
   public function isNew()
   {
      $result = null === $this->expiry;
      return $result;
   }

   /**
    * Check if @see Session is expired.
    * @param DateTime $now
    * @return bool
    */
   public function isExpired(DateTime $now = null)
   {
      if (null === $now)
      {
         $now = DateTime::createFromFormat('U', time());
      }
      $result = $now > $this->expiry;
      return $result;
   }

   /**
    * Reset @see Session attributes.
    */
   public function reset()
   {
      $this->expiry = null;
      $this->skypeToken = null;
      $this->registrationToken = null;
   }

   public function getSkypeToken(): ?SkypeToken
   {
      return $this->skypeToken;
   }

   public function setSkypeToken(SkypeToken $skypeToken)
   {
      $this->skypeToken = $skypeToken;
   }

   /**
    * @return RegistrationToken
    */
   public function getRegistrationToken(): ?RegistrationToken
   {
      return $this->registrationToken;
   }

   /**
    * @param RegistrationToken $registrationToken
    */
   public function setRegistrationToken(RegistrationToken $registrationToken)
   {
      $this->registrationToken = $registrationToken;
   }

   /**
    * @return OAuthMicrosoft
    */
   public function getOAuthMicrosoft()
   {
      return $this->oAuthMicrosoft;
   }

   /**
    * @param OAuthMicrosoft $oAuthMicrosoft
    */
   public function setOAuthMicrosoft(OAuthMicrosoft $oAuthMicrosoft)
   {
      $this->oAuthMicrosoft = $oAuthMicrosoft;
   }

   /**
    * @return OAuthMicrosoftRedirect
    */
   public function getOAuthMicrosoftRedirect()
   {
      return $this->oAuthMicrosoftRedirect;
   }

   /**
    * @param OAuthMicrosoftRedirect $oAuthMicrosoftRedirect
    */
   public function setOAuthMicrosoftRedirect(OAuthMicrosoftRedirect $oAuthMicrosoftRedirect)
   {
      $this->oAuthMicrosoftRedirect = $oAuthMicrosoftRedirect;
   }

   /**
    * @return OAuthSkype
    */
   public function getOAuthSkype()
   {
      return $this->oAuthSkype;
   }

   /**
    * @param OAuthSkype $oAuthSkype
    */
   public function setOAuthSkype(OAuthSkype $oAuthSkype)
   {
      $this->oAuthSkype = $oAuthSkype;
   }

}