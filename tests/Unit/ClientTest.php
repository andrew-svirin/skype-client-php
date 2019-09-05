<?php

namespace AndrewSvirin\tests\Unit;

use AndrewSvirin\SkypeClient\SkypeClient;
use AndrewSvirin\SkypeClient\Models\Account;
use AndrewSvirin\SkypeClient\Services\SessionManager;
use AndrewSvirin\SkypeClient\Utils\EnvUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
final class ClientTest extends TestCase
{

   /**
    * Algo to encode/decode session.
    */
   const ENCRYPT_ALGO = 'AES-128-ECB';

   private $dataDir = __DIR__ . '/../_data';
   private $cacheDir = __DIR__ . '/../_cache';

   /**
    * @var SkypeClient
    */
   private $client;

   /**
    * {@inheritdoc}
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    */
   public function setUp()
   {
      $sessionManager = new SessionManager(
         $this->cacheDir . '/sessions',
         EnvUtil::getSecret(),
         true,
         $this->cacheDir . '/debug_sessions'
      );
      $this->client = new SkypeClient($sessionManager);
   }

   /**
    * @dataProvider accountProvider
    * @param string $username
    * @param string $password
    * @return \AndrewSvirin\SkypeClient\Models\Session
    * @throws \AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function testLogin(string $username, string $password)
   {
      $account = new Account($username, $password);
      $session = $this->client->login($account);
      $this->assertNotEmpty($session);
      return $session;
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    */
   public function testMyProperties()
   {
      $account = $this->getAccount('user_1');
      $session = $this->testLogin($account->getUsername(), $account->getPassword());
      $this->assertNotEmpty($this->client->loadMyProperties($session));
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    */
   public function testMyInvites()
   {
      $account = $this->getAccount('user_1');
      $session = $this->testLogin($account->getUsername(), $account->getPassword());
      $this->assertArrayHasKey('invite_list', $this->client->loadMyInvites($session));
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    */
   public function testSendMessage()
   {
      $account1 = $this->getAccount('user_1');
      $session1 = $this->testLogin($account1->getUsername(), $account1->getPassword());
      $account2 = $this->getAccount('user_2');
      $session2 = $this->testLogin($account2->getUsername(), $account2->getPassword());
      $this->client->sendMessage($session2, $session1->getAccount()->getConversation(), uniqid('Ping-'));
      sleep(2);
      $this->client->sendMessage($session1, $session2->getAccount()->getConversation(), uniqid('Pong-'));
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function testLoadMessages()
   {
      $account1 = $this->getAccount('user_1');
      $session1 = $this->testLogin($account1->getUsername(), $account1->getPassword());
      $account2 = $this->getAccount('user_2');
      $session2 = $this->testLogin($account2->getUsername(), $account2->getPassword());
      $this->assertArrayHasKey('messages', $this->client->loadMessages($session1, $session2->getAccount()->getConversation()));
      sleep(2);
      $this->assertArrayHasKey('messages', $this->client->loadMessages($session2, $session1->getAccount()->getConversation()));
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function testCreateGroup()
   {
      $account1 = $this->getAccount('user_1');
      $session1 = $this->testLogin($account1->getUsername(), $account1->getPassword());
      $account2 = $this->getAccount('user_2');
      $session2 = $this->testLogin($account2->getUsername(), $account2->getPassword());
      $members = [
         $session2->getAccount()->getConversation(),
      ];
      $this->client->createGroup($session1, uniqid('Group-'), $members);
      $this->client->createGroup($session2, uniqid('Group-'));
   }

   /**
    * Skype Accounts credentials data provider.
    * @return array
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    */
   public function accountProvider(): array
   {
      $secret = EnvUtil::getSecret();
      $this->generateDataProvider($secret);
      $accountsEnc = json_decode(file_get_contents($this->credentialsFilePath()), true);
      $accounts = [];
      foreach ($accountsEnc as $name => $account)
      {
         $accounts[$name] = [
            'username' => $this->decrypt($account['username'], $secret),
            'password' => $this->decrypt($account['password'], $secret),
         ];
      }
      return $accounts;
   }

   private function credentialsFilePath()
   {
      return $this->dataDir . '/credentials.json';
   }

   /**
    * Generate data provider.
    * Extract from env debugging variables and populate credentials data file.
    * @param string $secret
    */
   private function generateDataProvider(string $secret)
   {
      if (is_file($this->credentialsFilePath()))
      {
         return;
      }
      $user1 = EnvUtil::getUser1();
      $user2 = EnvUtil::getUser2();
      $accounts = [
         'user_1' => [
            'username' => $user1[0],
            'password' => $user1[1],
         ],
         'user_2' => [
            'username' => $user2[0],
            'password' => $user2[1],
         ],
      ];
      $accountsEnc = [];
      foreach ($accounts as $name => $account)
      {
         $accountsEnc[$name] = [
            'username' => $this->encrypt($account['username'], $secret),
            'password' => $this->encrypt($account['password'], $secret),
         ];
      }
      file_put_contents($this->dataDir . '/credentials.json', json_encode($accountsEnc, JSON_PRETTY_PRINT));
   }

   /**
    * Skype Account credentials for user.
    * @param string $user
    * @return Account
    * @throws \AndrewSvirin\SkypeClient\Exceptions\ClientException
    */
   public function getAccount(string $user): Account
   {
      $accountProvider = $this->accountProvider();
      $credentials = $accountProvider[$user];
      return new Account($credentials['username'], $credentials['password']);
   }

   /**
    * Encrypt text.
    * @param string $text
    * @param string $secret
    * @return string
    */
   private function encrypt(string $text, string $secret): string
   {
      return base64_encode(openssl_encrypt($text, self::ENCRYPT_ALGO, $secret));
   }

   /**
    * Decrypt text.
    * @param string $text
    * @param string $secret
    * @return string
    */
   private function decrypt(string $text, string $secret)
   {
      return openssl_decrypt(base64_decode($text), self::ENCRYPT_ALGO, $secret);
   }

}