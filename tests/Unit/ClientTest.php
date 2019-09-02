<?php

namespace AndriySvirin\tests\Unit;

use AndriySvirin\SkypeBot\Client;
use AndriySvirin\SkypeBot\models\Account;
use AndriySvirin\SkypeBot\services\SessionManager;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{

   private $dataDir = __DIR__ . '/../_data';
   private $cacheDir = __DIR__ . '/../_cache';

   /**
    * @var Account
    */
   private $account;

   /**
    * @var Client
    */
   private $client;

   /**
    * {@inheritdoc}
    */
   public function setUp()
   {
      $credentials = unserialize(file_get_contents($this->dataDir . '/credentials.ser'));
      $username = $credentials['user']['username'];
      $password = base64_decode($credentials['user']['password']);
      $this->account = new Account($username, $password);
      $sessionManager = new SessionManager($this->cacheDir . '/skype-client-php');
      $this->client = new Client($sessionManager);
   }

   /**
    * @return \AndriySvirin\SkypeBot\models\Session
    * @throws \AndriySvirin\SkypeBot\exceptions\AccountCacheFileSaveException
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientOauthSkypeLoginException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionDirCreateException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionFileLoadException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function testLogin()
   {
      $session = $this->client->login($this->account);
      $this->assertNotEmpty($session);
      return $session;
   }

   /**
    * @throws \AndriySvirin\SkypeBot\exceptions\AccountCacheFileSaveException
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientOauthMicrosoftLoginException
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientOauthMicrosoftRedirectLoginException
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientOauthSkypeLoginException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionDirCreateException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionFileLoadException
    * @throws \AndriySvirin\SkypeBot\exceptions\SessionFileRemoveException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws \AndriySvirin\SkypeBot\exceptions\ClientException
    */
   public function testMyProperties()
   {
      $session = $this->testLogin();
      $this->assertNotEmpty($this->client->loadMyProperties($session));
   }

}