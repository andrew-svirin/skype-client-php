<?php

namespace AndriySvirin\SkypeBot\exceptions;

class ClientOauthMicrosoftRedirectLoginException extends \Exception
{
   const MESSAGE = 'Unable to login to Oauth Microsoft Redirect [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }
}