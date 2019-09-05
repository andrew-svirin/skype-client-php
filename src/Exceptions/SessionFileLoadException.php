<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class SessionFileLoadException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class SessionFileLoadException extends \Exception
{
   const MESSAGE = 'Unable to load the file %s.';

   public function __construct($path)
   {
      parent::__construct(sprintf(self::MESSAGE, $path));
   }

}