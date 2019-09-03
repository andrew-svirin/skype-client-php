<?php

namespace AndrewSvirin\SkypeClient\exceptions;

class SessionFileLoadException extends \Exception
{
   const MESSAGE = 'Unable to load the file %s.';

   public function __construct($path)
   {
      parent::__construct(sprintf(self::MESSAGE, $path));
   }
}