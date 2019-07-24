<?php

namespace AndriySvirin\tests\Unit;

use AndriySvirin\SkypeBot\Client;
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
      $client = new Client($username, $password, $this->cacheDir . '/skype-bot-php');
//      $credentials = file_get_contents($this->dataDir . '/response.mt942');
//      $adapter = new Client();
//      $payments = $adapter->normalize($str);
      $this->assertTrue(true);
   }

}