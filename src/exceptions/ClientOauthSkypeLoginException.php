<?php

namespace AndriySvirin\SkypeBot\exceptions;

class ClientOauthSkypeLoginException extends \Exception
{
   const MESSAGE = 'Unable to login to Oauth Skype [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }
}