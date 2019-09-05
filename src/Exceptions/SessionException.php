<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class SessionException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class SessionException extends \Exception
{
   const MESSAGE = 'Unable to login to Skype [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }

}