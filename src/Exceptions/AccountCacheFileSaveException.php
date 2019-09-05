<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class AccountCacheFileSaveException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class AccountCacheFileSaveException extends \Exception
{
   const MESSAGE = 'Unable to save the file %s.';

   public function __construct($path)
   {
      parent::__construct(sprintf(self::MESSAGE, $path));
   }

}