<?php

namespace AndrewSvirin\SkypeClient\exceptions;

class SessionException extends \Exception
{
   const MESSAGE = 'Unable to login to Skype [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }
}