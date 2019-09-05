<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class ClientOauthMicrosoftRedirectLoginException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class ClientOauthMicrosoftRedirectLoginException extends \Exception
{
   const MESSAGE = 'Unable to login to Oauth Microsoft Redirect [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }

}