<?php

namespace AndriySvirin\SkypeBot\services;

use AndriySvirin\SkypeBot\exceptions\SessionDirCreateException;
use AndriySvirin\SkypeBot\exceptions\SessionFileLoadException;
use AndriySvirin\SkypeBot\exceptions\AccountCacheFileSaveException;
use AndriySvirin\SkypeBot\exceptions\SessionFileRemoveException;
use AndriySvirin\SkypeBot\models\Session;

/**
 * Class helps to manage cached data for @see Session.
 * Cached data are stored in files for fast access.
 */
class SessionManager
{

   /**
    * Algo to recognize cache name.
    */
   const CACHE_ALGO = 'sha1';

   /**
    * Cache interval for expiration goals.
    */
   const CACHE_INTERVAL = 21600;

   /**
    * Fields for cached data.
    */
   const FIELD_SKYPE_TOKEN = 'skypeToken';
   const FIELD_REGISTRATION_TOKEN = 'skypeToken';
   const FIELD_EXPIRY = 'expiry';

   /**
    * Store caches for accounts.
    *
    * @var string
    */
   private $cacheDir;

   public function __construct($cacheDir)
   {
      $this->cacheDir = $cacheDir;
   }

   /**
    * Save cached data for next fast access.
    * Data expiration is regulated by parameter $interval.
    * @param Session $session
    * @param null|int $interval
    * @param null|int $now Current time fot testing goals.
    * @throws AccountCacheFileSaveException
    */
   public function saveSession(Session $session, $interval = null, $now = null)
   {
      if (null === $now)
      {
         $now = time();
      }
      if (null === $interval)
      {
         $interval = self::CACHE_INTERVAL;
      }
      $session->setExpiry($now + $interval);
      $sessionData = [
         self::FIELD_SKYPE_TOKEN => $session->getSkypeToken(),
         self::FIELD_REGISTRATION_TOKEN => $session->getRegistrationToken(),
         self::FIELD_EXPIRY => $session->getExpiry(),
      ];
      $sessionFilePath = $this->genAccountCacheFilePath($session);
      if(false === file_put_contents($sessionFilePath, serialize($sessionData))){
         throw new AccountCacheFileSaveException($sessionFilePath);
      }
   }

   /**
    * Try to retrieve from storage cached data and populate @see Session.
    * If cached data are absent or expired, then skip population.
    * @param Session $session
    * @param null|int $now Current time fot testing goals.
    * @throws SessionDirCreateException
    * @throws SessionFileLoadException
    * @throws SessionFileRemoveException
    */
   public function loadSession(Session $session, $now = null)
   {
      if (!file_exists($this->cacheDir))
      {
         if (!mkdir($this->cacheDir))
         {
            throw new SessionDirCreateException($this->cacheDir);
         }
      }
      $sessionFilePath = $this->genAccountCacheFilePath($session);
      if (file_exists($sessionFilePath))
      {
         if(false === ($sessionContent = file_get_contents($sessionFilePath))){
            throw new SessionFileLoadException($sessionFilePath);
         }
         $sessionData = unserialize($sessionContent);
            $session->setSkypeToken((string)$sessionData[self::FIELD_SKYPE_TOKEN]);
            $session->setRegistrationToken((string)$sessionData[self::FIELD_REGISTRATION_TOKEN]);
            $session->setExpiry((int)$sessionData[self::FIELD_EXPIRY]);
         if($session->isExpired($now)){
            $session->reset();
            if(false === unlink($sessionFilePath)){
               throw new SessionFileRemoveException($sessionFilePath);
            }
         }
      }
   }

   /**
    * Generate file path for cached data for @see Session
    * @param Session $session
    * @return string
    */
   private function genAccountCacheFilePath(Session $session)
   {
      $accountHash = hash(self::CACHE_ALGO, $session->getAccount()->getUsername());
      $result = sprintf('%s/session_%s', $this->cacheDir, $accountHash);
      return $result;
   }
}
