<?php

namespace AndrewSvirin\SkypeClient\models;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Property for class @see Session that holds data retrieved from OAuth Microsoft Redirect.
 */
class OAuthMicrosoftRedirect
{
   /**
    * @var string
    */
   private $nap;

   /**
    * @var string
    */
   private $anon;

   /**
    * @var string
    */
   private $t;

   /**
    * @var Cookie[]
    */
   private $cookies = [];

   /**
    * @return string
    */
   public function getNAP(): string
   {
      return $this->nap;
   }

   /**
    * @param string $value
    */
   public function setNAP(string $value)
   {
      $this->nap = $value;
   }

   /**
    * @return string
    */
   public function getANON(): string
   {
      return $this->anon;
   }

   /**
    * @param string $value
    */
   public function setANON(string $value)
   {
      $this->anon = $value;
   }

   /**
    * @return string
    */
   public function getT(): string
   {
      return $this->t;
   }

   /**
    * @param string $value
    */
   public function setT(string $value)
   {
      $this->t = $value;
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