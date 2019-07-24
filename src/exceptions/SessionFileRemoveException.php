<?php

namespace AndriySvirin\SkypeBot\exceptions;

class SessionFileRemoveException extends \Exception
{
   const MESSAGE = 'Unable to remove the file %s.';

   public function __construct($path)
   {
      parent::__construct(sprintf(self::MESSAGE, $path));
   }
}