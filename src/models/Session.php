<?php

namespace AndriySvirin\SkypeBot\models;

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
    * TODO: Skype Token needs for...
    *
    * @var string
    */
   private $skypeToken;

   /**
    * TODO: Registration Token needs for...
    *
    * @var string
    */
   private $registrationToken;

   /**
    * DateTime until the cache is valid.
    *
    * @var DateTime
    */
   private $expiry;

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

   /**
    * @var Skype
    */
   private $skype;

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
    * @return string
    */
   public function getSkypeToken()
   {
      return $this->skypeToken;
   }

   /**
    * @param string $skypeToken
    */
   public function setSkypeToken($skypeToken)
   {
      $this->skypeToken = $skypeToken;
   }

   /**
    * @return string
    */
   public function getRegistrationToken()
   {
      return $this->registrationToken;
   }

   /**
    * @param string $registrationToken
    */
   public function setRegistrationToken($registrationToken)
   {
      $this->registrationToken = $registrationToken;
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
    * Check if @see Session is expired.
    * @param DateTime $now
    * @return bool
    */
   public function isExpired(DateTime $now)
   {
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

   /**
    * Lazy loading @see Skype instance by demand.
    * @return Skype
    */
   public function getSkype()
   {
      if (null === $this->skype)
      {
         $this->skype = new Skype();
      }
      return $this->skype;
   }

}