<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class ClientOauthSkypeLoginException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class ClientOauthSkypeLoginException extends \Exception
{
   const MESSAGE = 'Unable to login to Oauth Skype [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }

}