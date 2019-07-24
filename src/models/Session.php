<?php

namespace AndriySvirin\SkypeBot\models;

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
    * Timestamp until the cache is valid.
    *
    * @var int
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
    * @return int
    */
   public function getExpiry()
   {
      return $this->expiry;
   }

   /**
    * @param int $expiry
    */
   public function setExpiry($expiry)
   {
      $this->expiry = $expiry;
   }

   /**
    * Check if @see Session is expired.
    * @param null|int $now
    * @return bool
    */
   public function isExpired($now = null)
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
    * Lazy loading @see OAuthMicrosoft instance by demand.
    * @return OAuthMicrosoft
    */
   public function getOAuthMicrosoft()
   {
      if (null === $this->oAuthMicrosoft)
      {
         $this->oAuthMicrosoft = new OAuthMicrosoft();
      }
      return $this->oAuthMicrosoft;
   }

   /**
    * Lazy loading @see OAuthMicrosoftRedirect instance by demand.
    * @return OAuthMicrosoftRedirect
    */
   public function getOAuthMicrosoftRedirect()
   {
      if (null === $this->oAuthMicrosoftRedirect)
      {
         $this->oAuthMicrosoftRedirect = new OAuthMicrosoftRedirect();
      }
      return $this->oAuthMicrosoftRedirect;
   }

   /**
    * Lazy loading @see getOAuthSkype instance by demand.
    * @return OAuthSkype
    */
   public function getOAuthSkype()
   {
      if (null === $this->oAuthSkype)
      {
         $this->oAuthSkype = new OAuthSkype();
      }
      return $this->oAuthSkype;
   }

}