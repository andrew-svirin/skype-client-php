<?php

namespace AndriySvirin\SkypeBot\exceptions;

class ClientOauthMicrosoftLoginException extends \Exception
{
   const MESSAGE = 'Unable to login to Oauth Microsoft [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }
}