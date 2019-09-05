<?php

namespace AndrewSvirin\SkypeClient\Services;

use AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException;
use AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException;
use AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException;
use AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException;
use AndrewSvirin\SkypeClient\Factories\SessionFactory;
use AndrewSvirin\SkypeClient\Models\Account;
use AndrewSvirin\SkypeClient\Models\Session;
use DateTime;

/**
 * Class helps to manage cached data for @see Session.
 * Cached data are stored in files for fast access.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class SessionManager
{

   /**
    * Algo to recognize cache name.
    */
   const CACHE_ALGO = 'sha512';

   /**
    * Algo to encode/decode session.
    */
   const ENCRYPT_ALGO = 'AES-128-ECB';

   /**
    * Store caches for sessions.
    *
    * @var string
    */
   private $sessionDir;

   /**
    * Session encrypting key.
    *
    * @var string
    */
   private $secret;

   /**
    * Debug mode.
    *
    * @var bool
    */
   private $isDebug;

   /**
    * Debug session storage.
    *
    * @var string
    */
   private $debugSessionsDir;

   public function __construct($sessionDir, $secret, bool $isDebug = false, string $debugSessionsDir = null)
   {
      $this->sessionDir = $sessionDir;
      $this->secret = $secret;
      $this->isDebug = $isDebug;
      $this->debugSessionsDir = $debugSessionsDir;
   }

   /**
    * Save session data for next fast access.
    * Set expiration time.
    * @param Session $session
    * @param DateTime|null $now
    * @throws AccountCacheFileSaveException
    * @throws SessionDirCreateException
    */
   public function saveSession(Session $session, DateTime $now = null)
   {
      if (null === $now)
      {
         $now = DateTime::createFromFormat('U', time());
      }
      $now->modify('+6 hours');
      $session->setExpiry($now);
      $this->saveSessionData($session->getAccount(), SessionFactory::buildDataFromSession($session));
   }

   /**
    * Try to retrieve from storage cached data and populate @see Session.
    * If cached data are absent or expired, then skip population.
    * @param Account $account
    * @return Session
    * @throws SessionFileLoadException
    */
   public function loadAccountSession(Account $account)
   {
      if (($data = $this->loadSessionData($account)))
      {
         $session = SessionFactory::buildSessionFromData($account, $data);
      }
      else
      {
         $session = new Session($account);
      }
      return $session;
   }

   /**
    * Destroy account session and stored cache.
    * @param Session $session
    * @throws SessionFileRemoveException
    */
   public function removeSession(Session $session)
   {
      $session->reset();
      $this->removeSessionFile($session->getAccount());
   }

   /**
    * Generate file path for cached data for @see Session
    * @param Account $account
    * @return string
    */
   private function buildAccountSessionFileName(Account $account)
   {
      $accountHash = hash(self::CACHE_ALGO, $account->getUsername());
      $result = sprintf('session_%s', $accountHash);
      return $result;
   }

   /**
    * Remove account session file.
    * @param Account $account
    * @throws SessionFileRemoveException
    */
   private function removeSessionFile(Account $account)
   {
      $sessionFileName = $this->buildAccountSessionFileName($account);
      $sessionFilePath = $this->sessionDir . '/' . $sessionFileName;
      if (false === unlink($sessionFilePath))
      {
         throw new SessionFileRemoveException($sessionFilePath);
      }
      if ($this->isDebug)
      {
         $debugSessionFilePath = $this->debugSessionsDir . '/' . $sessionFileName;
         if (false === unlink($debugSessionFilePath))
         {
            throw new SessionFileRemoveException($debugSessionFilePath);
         }
      }
   }

   /**
    * @throws SessionDirCreateException
    */
   private function prepareSessionDir()
   {
      if (!file_exists($this->sessionDir) && !mkdir($this->sessionDir))
      {
         throw new SessionDirCreateException($this->sessionDir);
      }
   }

   /**
    * @throws SessionDirCreateException
    */
   private function prepareDebugSessionDir()
   {
      if (!file_exists($this->debugSessionsDir) && !mkdir($this->debugSessionsDir))
      {
         throw new SessionDirCreateException($this->debugSessionsDir);
      }
   }

   /**
    * Encrypt data by secret from the Env to the file.
    * @param Account $account
    * @param array $data
    * @throws AccountCacheFileSaveException
    * @throws SessionDirCreateException
    */
   private function saveSessionData(Account $account, array $data)
   {
      $this->prepareSessionDir();
      $sessionFileName = $this->buildAccountSessionFileName($account);
      $sessionFilePath = $this->sessionDir . '/' . $sessionFileName;
      $dataJson = json_encode($data, JSON_PRETTY_PRINT);
      $encryptedData = base64_encode(openssl_encrypt($dataJson, self::ENCRYPT_ALGO, $this->secret));
      if (false === file_put_contents($sessionFilePath, $encryptedData))
      {
         throw new AccountCacheFileSaveException($sessionFilePath);
      }
      if ($this->isDebug)
      {
         $this->prepareDebugSessionDir();
         $debugSessionFilePath = $this->debugSessionsDir . '/' . $sessionFileName;
         if (false === file_put_contents($debugSessionFilePath, $dataJson))
         {
            throw new AccountCacheFileSaveException($sessionFilePath);
         }
      }
   }

   /**
    * Decrypt encrypted text from the file by secret from the Env.
    * @param Account $account
    * @return array|null
    * @throws SessionFileLoadException
    */
   private function loadSessionData(Account $account): ?array
   {
      $sessionFileName = $this->buildAccountSessionFileName($account);
      $sessionFilePath = $this->sessionDir . '/' . $sessionFileName;
      if (!file_exists($sessionFilePath))
      {
         return null;
      }
      if (!($encryptedData = file_get_contents($sessionFilePath)))
      {
         throw new SessionFileLoadException($sessionFilePath);
      }
      $dataJson = openssl_decrypt(base64_decode($encryptedData), self::ENCRYPT_ALGO, $this->secret);
      $data = json_decode($dataJson, true);
      return $data;
   }
}
