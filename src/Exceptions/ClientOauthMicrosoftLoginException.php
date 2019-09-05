<?php

namespace AndrewSvirin\SkypeClient\Exceptions;

/**
 * Class ClientOauthMicrosoftLoginException
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
class ClientOauthMicrosoftLoginException extends \Exception
{
   const MESSAGE = 'Unable to login to Oauth Microsoft [%s]';

   public function __construct($reason)
   {
      parent::__construct(sprintf(self::MESSAGE, $reason));
   }

}