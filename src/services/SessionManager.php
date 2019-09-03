<?php

namespace AndrewSvirin\SkypeClient\services;

use AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException;
use AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException;
use AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException;
use AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException;
use AndrewSvirin\SkypeClient\factories\SessionFactory;
use AndrewSvirin\SkypeClient\models\Account;
use AndrewSvirin\SkypeClient\models\Session;
use DateTime;

/**
 * Class helps to manage cached data for @see Session.
 * Cached data are stored in files for fast access.
 */
class SessionManager
{

   /**
    * Algo to recognize cache name.
    */
   const CACHE_ALGO = 'sha512';

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
    * Save session data for next fast access.
    * Set expiration time.
    * @param Session $session
    * @param DateTime|null $now
    * @throws AccountCacheFileSaveException
    */
   public function saveSession(Session $session, DateTime $now = null)
   {
      if (null === $now)
      {
         $now = DateTime::createFromFormat('U', time());
      }
      $now->modify('+6 hours');
      $session->setExpiry($now);
      $sessionFilePath = $this->buildAccountSessionFilePath($session->getAccount());
      if (false === file_put_contents($sessionFilePath, json_encode(SessionFactory::buildDataFromSession($session), JSON_PRETTY_PRINT)))
      {
         throw new AccountCacheFileSaveException($sessionFilePath);
      }
   }

   /**
    * Try to retrieve from storage cached data and populate @see Session.
    * If cached data are absent or expired, then skip population.
    * @param Account $account
    * @return Session
    * @throws SessionDirCreateException
    * @throws SessionFileLoadException
    */
   public function loadAccountSession(Account $account)
   {
      $session = new Session($account);
      if (!file_exists($this->cacheDir) && !mkdir($this->cacheDir))
      {
         throw new SessionDirCreateException($this->cacheDir);
      }
      $sessionFilePath = $this->buildAccountSessionFilePath($account);
      // Load session data from file.
      if (file_exists($sessionFilePath))
      {
         if (!($sessionContent = file_get_contents($sessionFilePath)) || !($data = json_decode($sessionContent, true)))
         {
            throw new SessionFileLoadException($sessionFilePath);
         }
         $session = SessionFactory::buildSessionFromData($account, $data);
      }
      return $session;
   }

   /**
    * Destroy account session and stored cache.
    * @param Session $session
    * @throws SessionFileRemoveException
    */
   public function destroyAccountSession(Session $session)
   {
      $session->reset();
      $this->removeAccountSessionFilePath($session->getAccount());
   }

   /**
    * Generate file path for cached data for @see Session
    * @param Account $account
    * @return string
    */
   private function buildAccountSessionFilePath(Account $account)
   {
      $accountHash = hash(self::CACHE_ALGO, $account->getUsername());
      $result = sprintf('%s/session_%s', $this->cacheDir, $accountHash);
      return $result;
   }

   /**
    * Remove account session file.
    * @param Account $account
    * @throws SessionFileRemoveException
    */
   private function removeAccountSessionFilePath(Account $account)
   {
      $sessionFilePath = $this->buildAccountSessionFilePath($account);
      if (false === unlink($sessionFilePath))
      {
         throw new SessionFileRemoveException($sessionFilePath);
      }
   }
}
