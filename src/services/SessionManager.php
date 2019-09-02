<?php

namespace AndriySvirin\SkypeBot\services;

use AndriySvirin\SkypeBot\exceptions\SessionDirCreateException;
use AndriySvirin\SkypeBot\exceptions\SessionFileLoadException;
use AndriySvirin\SkypeBot\exceptions\AccountCacheFileSaveException;
use AndriySvirin\SkypeBot\exceptions\SessionFileRemoveException;
use AndriySvirin\SkypeBot\models\Account;
use AndriySvirin\SkypeBot\models\Session;
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
    * Fields for cached data.
    */
   const FIELD_SKYPE_TOKEN = 'skypeToken';
   const FIELD_REGISTRATION_TOKEN = 'registrationToken';
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
      $sessionData = [
         self::FIELD_SKYPE_TOKEN => $session->getSkypeToken(),
         self::FIELD_REGISTRATION_TOKEN => $session->getRegistrationToken(),
         self::FIELD_EXPIRY => $session->getExpiry()->format('U'),
      ];
      $sessionFilePath = $this->buildAccountSessionFilePath($session->getAccount());
      if (false === file_put_contents($sessionFilePath, serialize($sessionData)))
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
         if (false === ($sessionContent = file_get_contents($sessionFilePath)))
         {
            throw new SessionFileLoadException($sessionFilePath);
         }
         $sessionData = unserialize($sessionContent);
         $session->setSkypeToken((string)$sessionData[self::FIELD_SKYPE_TOKEN]);
         $session->setRegistrationToken((string)$sessionData[self::FIELD_REGISTRATION_TOKEN]);
         $session->setExpiry(DateTime::createFromFormat('U', (int)$sessionData[self::FIELD_EXPIRY]));
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
