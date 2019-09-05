<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class SessionDirCreateException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class SessionDirCreateException extends \Exception
{
   const MESSAGE = 'Unable to create the directory %s.';

   public function __construct($dir)
   {
      parent::__construct(sprintf(self::MESSAGE, $dir));
   }

}