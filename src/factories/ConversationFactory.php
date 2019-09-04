<?php

namespace AndrewSvirin\SkypeClient\factories;

use AndrewSvirin\SkypeClient\models\Conversation;

/**
 * Class ConversationFactory manages conversion Conversation to data and reverse.
 */
class ConversationFactory
{

   const FIELD_NAME = 'name';
   const FIELD_LABEL = 'label';

   /**
    * @param array $data
    * @return Conversation
    */
   public static function buildConversationFromData(array $data): Conversation
   {
      $result = new Conversation($data[self::FIELD_NAME], $data[self::FIELD_LABEL]);
      return $result;
   }

   /**
    * @param Conversation $conversation
    * @return array
    */
   public static function buildDataFromConversation(Conversation $conversation): array
   {
      $result = [
         self::FIELD_NAME => $conversation->getName(),
         self::FIELD_LABEL => $conversation->getLabel(),
      ];
      return $result;
   }

}