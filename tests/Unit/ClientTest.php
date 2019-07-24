<?php

namespace AndriySvirin\tests\Unit;

use AndriySvirin\SkypeBot\Client;
use AndriySvirin\SkypeBot\models\Account;
use AndriySvirin\SkypeBot\models\Session;
use AndriySvirin\SkypeBot\services\SessionManager;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{

   private $dataDir = __DIR__ . '/../_data';
   private $cacheDir = __DIR__ . '/../_cache';

   public function testLogin()
   {
      $credentials = unserialize(file_get_contents($this->dataDir . '/credentials.ser'));
      $username = $credentials['user']['username'];
      $password = base64_decode($credentials['user']['password']);
      $account = new Account($username, $password);
      $session = new Session($account);
      $sessionManager = new SessionManager($this->cacheDir . '/skype-bot-php');
      $sessionManager->loadSession($session);
      $client = new Client($this->cacheDir . '/skype-bot-php');
      $client->loginAccount($session);

      $this->assertTrue(true);
   }

}