<?php

namespace AndriySvirin\SkypeBot\exceptions;

class AccountCacheFileSaveException extends \Exception
{
   const MESSAGE = 'Unable to save the file %s.';

   public function __construct($path)
   {
      parent::__construct(sprintf(self::MESSAGE, $path));
   }
}