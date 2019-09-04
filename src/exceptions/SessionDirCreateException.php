<?php

namespace AndrewSvirin\SkypeClient\exceptions;

class SessionDirCreateException extends \Exception
{
   const MESSAGE = 'Unable to create the directory %s.';

   public function __construct($dir)
   {
      parent::__construct(sprintf(self::MESSAGE, $dir));
   }

}