<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class SessionFileRemoveException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class SessionFileRemoveException extends \Exception
{
   const MESSAGE = 'Unable to remove the file %s.';

   public function __construct($path)
   {
      parent::__construct(sprintf(self::MESSAGE, $path));
   }

}