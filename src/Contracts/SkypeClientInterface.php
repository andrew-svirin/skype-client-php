<?php

namespace AndrewSvirin\SkypeClient\Contracts;

use AndrewSvirin\SkypeClient\Exceptions\AccountCacheFileSaveException;
use AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException;
use AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException;
use AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException;
use AndrewSvirin\SkypeClient\Exceptions\SessionDirCreateException;
use AndrewSvirin\SkypeClient\Exceptions\SessionException;
use AndrewSvirin\SkypeClient\Exceptions\SessionFileLoadException;
use AndrewSvirin\SkypeClient\Exceptions\SessionFileRemoveException;
use AndrewSvirin\SkypeClient\Models\Account;
use AndrewSvirin\SkypeClient\Models\Conversation;
use AndrewSvirin\SkypeClient\Models\Session;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class SkypeClient implements methods to interact with Skype Server.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
interface SkypeClientInterface
{

   /**
    * Login process consists from 2 parts: login to Microsoft & login to Skype.
    * This method create/restore session and setup expire date for session and save to session storage.
    * @param Account $account
    * @param DateTime|null $now
    * @return Session
    * @throws ClientOauthMicrosoftLoginException
    * @throws ClientOauthMicrosoftRedirectLoginException
    * @throws ClientOauthSkypeLoginException
    * @throws SessionException
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    * @throws AccountCacheFileSaveException
    * @throws SessionDirCreateException
    * @throws SessionFileLoadException
    * @throws SessionFileRemoveException
    */
   function login(Account $account, DateTime $now = null): Session;

   /**
    * Load current user properties.
    * @param Session $session
    * @return array
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    */
   function loadMyProperties(Session $session): array;

   /**
    * Load current user profile.
    * @param Session $session
    * @return array
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    */
   function loadMyProfile(Session $session): array;

   /**
    * Load current user invites list.
    * @param Session $session
    * @return array
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    */
   function loadMyInvites(Session $session): array;

   /**
    * Send message from skype Account in the Session to Skype Account or Skype Group in the arguments.
    * @param Session $session
    * @param Conversation $conversation
    * @param string $content
    * @return array
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    */
   function sendMessage(Session $session, Conversation $conversation, $content): array;

   /**
    * Load messages for Account from Conversation to Another Account or in the Group.
    * @param Session $session
    * @param Conversation $conversation
    * @param int $size Amount of messages.
    * @return array
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    */
   function loadMessages(Session $session, Conversation $conversation, $size = 100): array;

   /**
    * Create Group with Admin from Session and Members from arguments.
    * Responses group name.
    * @param Session $session
    * @param string $topic
    * @param Conversation[] $members
    * @return Conversation
    * @throws ClientExceptionInterface
    * @throws RedirectionExceptionInterface
    * @throws ServerExceptionInterface
    * @throws TransportExceptionInterface
    */
   function createGroup(Session $session, string $topic, array $members = []): Conversation;

   /**
    * Load Group details.
    * @param Session $session
    * @param Conversation $conversation
    * @return array
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   function loadGroup(Session $session, Conversation $conversation): array;

}