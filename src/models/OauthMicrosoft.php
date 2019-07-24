<?php

namespace AndriySvirin\SkypeBot\models;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Decorator for class @see Session that holds data retrieved from Oauth Microsoft.
 */
class OauthMicrosoft
{
   /**
    * @var string
    */
   private $loginUrl;

   /**
    * @var string
    */
   private $ppft;

   /**
    * @var string
    */
   private $ppsx;

   /**
    * @var Cookie[]
    */
   private $cookies = [];

   /**
    * @return string
    */
   public function getLoginUrl()
   {
      return $this->loginUrl;
   }

   /**
    * @param string $loginUrl
    */
   public function setLoginUrl($loginUrl)
   {
      $this->loginUrl = $loginUrl;
   }

   /**
    * @return string
    */
   public function getPPFT()
   {
      return $this->ppft;
   }

   /**
    * @param string $ppft
    */
   public function setPPFT($ppft)
   {
      $this->ppft = $ppft;
   }

   /**
    * @return string
    */
   public function getPPSX()
   {
      return $this->ppsx;
   }

   /**
    * @param string $ppsx
    */
   public function setPPSX($ppsx)
   {
      $this->ppsx = $ppsx;
   }

   /**
    * @return array
    */
   public function getCookies()
   {
      return $this->cookies;
   }

   /**
    * Add Cookie.
    * @param Cookie $cookie
    */
   public function addCookies(Cookie $cookie)
   {
      $this->cookies[] = $cookie;
   }
}