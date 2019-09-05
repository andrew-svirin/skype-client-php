<?php

namespace AndrewSvirin\SkypeClient\Models;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Property for class @see Session that holds data retrieved from OAuth Microsoft.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class OAuthMicrosoft
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
   public function getLoginUrl(): string
   {
      return $this->loginUrl;
   }

   /**
    * @param string $value
    */
   public function setLoginUrl(string $value)
   {
      $this->loginUrl = $value;
   }

   /**
    * @return string
    */
   public function getPPFT(): string
   {
      return $this->ppft;
   }

   /**
    * @param string $value
    */
   public function setPPFT(string $value)
   {
      $this->ppft = $value;
   }

   /**
    * @return string
    */
   public function getPPSX(): string
   {
      return $this->ppsx;
   }

   /**
    * @param string $value
    */
   public function setPPSX(string $value)
   {
      $this->ppsx = $value;
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