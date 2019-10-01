# SKYPE CLIENT (PHP) [![Build Status](https://travis-ci.org/andrew-svirin/skype-client-php.svg?branch=master)](https://travis-ci.org/andrew-svirin/skype-client-php)
Provides interface for communicate with Skype server using Skype Account.
Can be used to some skype integrations. In Symfony 5 exists notifier module that can be extended by this library.

# Troubleshooting
Before usage please login by web-browser first, because account can be blocked by the Skype server.

### Installation for PHP 7.2+
```bash
$ composer require andrew-svirin/skype-client-php
```

### License
andrew-svirin/skype-client-php is licensed under the MIT License, see the LICENSE file for details

### Example
Include
```php
 use AndrewSvirin\SkypeClient\SkypeClient;
 use AndrewSvirin\SkypeClient\Services\SessionManager;
```
Initialize Client:
```php
      $sessionManager = new SessionManager(
         __PROTECTED_DIR__ . '/sessions',
         EnvUtil::getSecret()
      );
      $client = new SkypeClient($sessionManager);
```
Login and create session:
```php
      $account = new Account($username, $password);
      $session = $client->login($account);
```
Send message:
```php
      $conversation = new Conversation(__SKYPE_ID__, __SKYPE_LABEL__);
      $client->sendMessage($session, $conversation, 'Hi');
```
More usage methods you can find in `tests/Unit/ClientTest.php` file.
