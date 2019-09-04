<?php

namespace AndrewSvirin\tests\Unit;

use AndrewSvirin\SkypeClient\SkypeClient;
use AndrewSvirin\SkypeClient\models\Account;
use AndrewSvirin\SkypeClient\services\SessionManager;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{

   private $dataDir = __DIR__ . '/../_data';
   private $cacheDir = __DIR__ . '/../_cache';

   /**
    * @var SkypeClient
    */
   private $client;

   /**
    * {@inheritdoc}
    */
   public function setUp()
   {
      $sessionManager = new SessionManager($this->cacheDir . '/skype-client-php');
      $this->client = new SkypeClient($sessionManager);
   }

   /**
    * @dataProvider accountProvider
    * @param string $username
    * @param string $password
    * @return \AndrewSvirin\SkypeClient\models\Session
    * @throws \AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException
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
    * @throws \AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientException
    */
   public function testMyProperties()
   {
      $account = $this->getAccount('user_1');
      $session = $this->testLogin($account->getUsername(), $account->getPassword());
      $this->assertNotEmpty($this->client->loadMyProperties($session));
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientException
    */
   public function testMyInvites()
   {
      $account = $this->getAccount('user_1');
      $session = $this->testLogin($account->getUsername(), $account->getPassword());
      $this->assertArrayHasKey('invite_list', $this->client->loadMyInvites($session));
   }

   /**
    * @throws \AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientException
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
    * @throws \AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException
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
    * @throws \AndrewSvirin\SkypeClient\exceptions\AccountCacheFileSaveException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\ClientOauthSkypeLoginException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionDirCreateException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileLoadException
    * @throws \AndrewSvirin\SkypeClient\exceptions\SessionFileRemoveException
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
    */
   public function accountProvider()
   {
      $credentials = json_decode(file_get_contents($this->dataDir . '/credentials.json'), true);
      $accounts = [];
      foreach ($credentials as $user => $credential)
      {
         $accounts[$user] = [
            'username' => base64_decode($credential['username']),
            'password' => base64_decode($credential['password']),
         ];
      }
      return $accounts;
   }

   /**
    * Skype Account credentials for user.
    * @param string $user
    * @return Account
    */
   public function getAccount(string $user): Account
   {
      $accountProvider = $this->accountProvider();
      $credentials = $accountProvider[$user];
      return new Account($credentials['username'], $credentials['password']);
   }

}