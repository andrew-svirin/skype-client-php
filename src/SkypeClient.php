<?php

namespace AndrewSvirin\SkypeClient;

use AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftLoginException;
use AndrewSvirin\SkypeClient\Exceptions\ClientOauthMicrosoftRedirectLoginException;
use AndrewSvirin\SkypeClient\Exceptions\ClientOauthSkypeLoginException;
use AndrewSvirin\SkypeClient\Exceptions\SessionException;
use AndrewSvirin\SkypeClient\Models\Account;
use AndrewSvirin\SkypeClient\Models\Conversation;
use AndrewSvirin\SkypeClient\Models\OAuthMicrosoft;
use AndrewSvirin\SkypeClient\Models\OAuthMicrosoftRedirect;
use AndrewSvirin\SkypeClient\Models\OAuthSkype;
use AndrewSvirin\SkypeClient\Models\RegistrationToken;
use AndrewSvirin\SkypeClient\Models\Session;
use AndrewSvirin\SkypeClient\Models\SkypeToken;
use AndrewSvirin\SkypeClient\Services\SessionManager;
use DateTime;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class SkypeClient implements methods to interact with Skype Server.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
final class SkypeClient
{

   /**
    * @var HttpClientInterface
    */
   private $httpClient;

   /**
    * @var SessionManager
    */
   private $sessionManager;

   /**
    * @var int
    */
   private $clientId;

   public function __construct(SessionManager $sessionManager)
   {
      $this->clientId = 578134;
      $this->httpClient = HttpClient::create([
         'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',
            'Referer' => 'https://web.skype.com/',
         ],
      ]);
      $this->sessionManager = $sessionManager;
   }

   /**
    * Login on Oauth Microsoft web page and retries from Microsoft arguments LoginUrl, PPFT, PPSX, cookies.
    * @param Session $session
    * @return OAuthMicrosoft
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws ClientOauthMicrosoftLoginException
    */
   private function loginOAuthMicrosoft(Session $session): OAuthMicrosoft
   {
      $response = $this->request('GET', 'https://login.skype.com/login/oauth/microsoft', [
         'query' => [
            'client_id' => $this->clientId,
            'redirect_uri' => sprintf('https://web.skype.com/username=%s', $session->getAccount()->getUsername()),
         ],
      ]);
      $page = $response->getContent();
      preg_match("/urlPost:'(?<login_url>.+)',/isU", $page, $loginURL);
      preg_match('/name="PPFT" id="(?<id>.+)" value="(?<ppft>.+)"/isU', $page, $ppft);
      preg_match("/t:\'(?<ppsx>.+)\',A/isU", $page, $ppsx);
      $headers = $response->getHeaders();
      if (empty($loginURL['login_url']) || empty($ppft['ppft']) || empty($ppsx['ppsx']) || empty($headers['set-cookie']))
      {
         throw new  ClientOauthMicrosoftLoginException('Missing arguments');
      }
      $oAuthMicrosoft = new OAuthMicrosoft();
      $oAuthMicrosoft->setLoginUrl((string)$loginURL['login_url']);
      $oAuthMicrosoft->setPPFT((string)$ppft['ppft']);
      $oAuthMicrosoft->setPPSX((string)$ppsx['ppsx']);
      foreach ($headers['set-cookie'] as $cookie)
      {
         $oAuthMicrosoft->addCookies(Cookie::fromString($cookie));
      }
      return $oAuthMicrosoft;
   }

   /**
    * Login on Oauth Microsoft Redirect web page and setup Microsoft arguments NAP, ANON, T.
    * This method depends of @see loginOAuthMicrosoft retrieved values.
    * @param Session $session
    * @return OAuthMicrosoftRedirect
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws ClientOauthMicrosoftRedirectLoginException
    */
   private function loginOAuthMicrosoftRedirect(Session $session): OAuthMicrosoftRedirect
   {
      $response = $this->request('POST', $session->getOAuthMicrosoft()->getLoginUrl(), [
         'body' => [
            'loginfmt' => $session->getAccount()->getUsername(),
            'login' => $session->getAccount()->getUsername(),
            'passwd' => $session->getAccount()->getPassword(),
            'type' => 11,
            'PPFT' => $session->getOAuthMicrosoft()->getPPFT(),
            'PPSX' => $session->getOAuthMicrosoft()->getPPSX(),
            'NewUser' => 1,
            'LoginOptions' => 3,
            'FoundMSAs' => '',
            'fspost' => 0,
            'i2' => 1,
            'i16' => '',
            'i17' => 0,
            'i18' => '__DefaultLoginStrings|1,__DefaultLogin_Core|1,',
            'i19' => 556374,
            'i21' => 0,
            'i13' => 0,
         ],
         'headers' => [
            'Cookie' => $session->getOAuthMicrosoft()->getCookies(),
         ],
      ]);
      $page = $response->getContent();
      preg_match('/<input type="hidden" name="NAP" id="NAP" value="(?<nap>.+)">/isU', $page, $NAP);
      preg_match('/<input type="hidden" name="ANON" id="ANON" value="(?<anon>.+)">/isU', $page, $ANON);
      preg_match('/<input type="hidden" name="t" id="t" value="(?<token>.+)">/isU', $page, $t);
      $headers = $response->getHeaders();
      if (empty($NAP['nap']) || empty($ANON['anon']) || empty($t['token']) || empty($headers['set-cookie']))
      {
         throw new  ClientOauthMicrosoftRedirectLoginException('Missing arguments');
      }
      $oAuthMicrosoftRedirect = new OAuthMicrosoftRedirect();
      $oAuthMicrosoftRedirect->setNAP((string)$NAP['nap']);
      $oAuthMicrosoftRedirect->setANON((string)$ANON['anon']);
      $oAuthMicrosoftRedirect->setToken((string)$t['token']);
      foreach ($headers['set-cookie'] as $cookie)
      {
         $oAuthMicrosoftRedirect->addCookies(Cookie::fromString($cookie));
      }
      return $oAuthMicrosoftRedirect;
   }

   /**
    * Login on OAuth Skype and setup Skype argument T.
    * This method depends of @see loginOAuthMicrosoftRedirect retrieved values.
    * @param Session $session
    * @return OAuthSkype
    * @throws ClientOauthSkypeLoginException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   private function loginOauthSkype(Session $session): OAuthSkype
   {
      $response = $this->request('POST', 'https://lw.skype.com/login/oauth/proxy', [
         'query' => [
            'client_id' => $this->clientId,
            'redirect_uri' => 'https://web.skype.com/&site_name=lw.skype.com&wa=wsignin1.0',
         ],
         'body' => [
            'NAP' => $session->getOAuthMicrosoftRedirect()->getNAP(),
            'ANON' => $session->getOAuthMicrosoftRedirect()->getANON(),
            't' => $session->getOAuthMicrosoftRedirect()->getToken(),
         ],
         'headers' => [
            'Cookie' => $session->getOAuthMicrosoftRedirect()->getCookies(),
         ],
      ]);
      $page = $response->getContent();
      preg_match('/<input type="hidden" name="t" value="(?<token>.+)"\/>/isU', $page, $t);
      if (empty($t['token']))
      {
         throw new  ClientOauthSkypeLoginException('Missing arguments');
      }
      $oAuthSkype = new OAuthSkype();
      $oAuthSkype->setToken((string)$t['token']);
      return $oAuthSkype;
   }

   /**
    * Login to the Skype and setup SkypeToken.
    * This method depends of @see loginOauthSkype retrieved values.
    * @param Session $session
    * @return SkypeToken
    * @throws SessionException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   private function loginSkypeToken(Session $session): SkypeToken
   {
      $response = $this->request('POST', 'https://login.skype.com/login/microsoft', [
         'query' => [
            'client_id' => $this->clientId,
            'redirect_uri' => 'https://web.skype.com/',
         ],
         'body' => [
            't' => $session->getOAuthSkype()->getToken(),
            'site_name' => 'lw.skype.com',
            'oauthPartner' => 999,
            'form' => '',
            'client_id' => $this->clientId,
            'redirect_uri' => 'https://web.skype.com/'
         ],
      ]);
      $page = $response->getContent();
      preg_match('/<input type="hidden" name="skypetoken" value="(?<skype_token>.+)"\/>/isU', $page, $skypeToken);
      if (empty($skypeToken['skype_token']))
      {
         throw new  SessionException('Missing skypeToken');
      }
      $result = new SkypeToken();
      $result->setSkypeToken((string)$skypeToken['skype_token']);
      return $result;
   }

   /**
    * Register in the Skype and setup registrationToken.
    * This method depends of @see loginSkype retrieved values.
    * Method detects Messenger URL by redirecting on Clients Server.
    * @param Session $session
    * @return RegistrationToken
    * @throws SessionException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   private function loginRegistrationToken(Session $session): RegistrationToken
   {
      $messengerUrl = 'https://client-s.gateway.messenger.live.com/v1';
      $url = sprintf('%s/users/ME/endpoints', $messengerUrl);
      $response = $this->request('POST', $url, [
         'body' => '{}',
         'authorization_session' => $session,
         'headers' => [
            'Cookie' => $session->getOAuthMicrosoft()->getCookies(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
         ],
      ], $redirectUrl);
      $headers = $response->getHeaders();
      if (empty($headers['set-registrationtoken'][0]))
      {
         throw new SessionException('Missing registrationToken');
      }
      preg_match('/registrationToken=(?<registration_token>.+);/isU', $headers['set-registrationtoken'][0], $registrationToken);
      if (empty($registrationToken['registration_token']))
      {
         throw new SessionException('Missing registrationToken');
      }
      $result = new RegistrationToken();
      $result->setRegistrationToken((string)$registrationToken['registration_token']);
      $result->setResponse(json_decode($response->getContent(), true));
      if (!empty($redirectUrl))
      {
         $messengerUrl = substr($redirectUrl, 0, strpos($redirectUrl, '/users'));
      }
      $result->setMessengerUrl($messengerUrl);
      return $result;
   }

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
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws Exceptions\AccountCacheFileSaveException
    * @throws Exceptions\SessionDirCreateException
    * @throws Exceptions\SessionFileLoadException
    * @throws Exceptions\SessionFileRemoveException
    */
   public function login(Account $account, DateTime $now = null): Session
   {
      $session = $this->sessionManager->loadAccountSession($account);
      if ($session->isNew())
      {
         $oAuthMicrosoft = $this->loginOAuthMicrosoft($session);
         $session->setOAuthMicrosoft($oAuthMicrosoft);
         $oAuthMicrosoftRedirect = $this->loginOAuthMicrosoftRedirect($session);
         $session->setOAuthMicrosoftRedirect($oAuthMicrosoftRedirect);
         $oAuthSkype = $this->loginOauthSkype($session);
         $session->setOAuthSkype($oAuthSkype);
         $skypeToken = $this->loginSkypeToken($session);
         $session->setSkypeToken($skypeToken);
         $registrationToken = $this->loginRegistrationToken($session);
         $session->setRegistrationToken($registrationToken);
         $profile = $this->loadMyProfile($session);
         $conversation = new Conversation($profile['username'], $profile['firstname'] . ' ' . $profile['lastname']);
         $account->setConversation($conversation);
         $this->sessionManager->saveSession($session);
      }
      elseif ($session->isExpired($now))
      {
         $this->sessionManager->removeSession($session);
      }
      return $session;
   }

   /**
    * Load current user properties.
    * @param Session $session
    * @return array
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function loadMyProperties(Session $session): array
   {
      $url = sprintf('%s/users/ME/properties', $session->getRegistrationToken()->getMessengerUrl());
      $response = $this->request('GET', $url, [
         'authorization_session' => $session,
      ]);
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   /**
    * Load current user profile.
    * @param Session $session
    * @return array|mixed
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function loadMyProfile(Session $session): array
   {
      $response = $this->request('GET', 'https://api.skype.com/users/self/profile', [
         'authorization_session' => $session,
      ]);
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   /**
    * Load current user invites list.
    * @param Session $session
    * @return array
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function loadMyInvites(Session $session): array
   {
      $response = $this->request('GET', 'https://edge.skype.com/pcs/contacts/v2/users/self/invites', [
         'authorization_session' => $session,
      ]);
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   /**
    * Send message from skype Account in the Session to Skype Account or Skype Group in the arguments.
    * @param Session $session
    * @param Conversation $conversation
    * @param string $content
    * @return int|mixed
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function sendMessage(Session $session, Conversation $conversation, $content): array
   {
      $dateTime = DateTime::createFromFormat('0.u00 U', microtime());
      $url = sprintf(
         '%s/users/ME/conversations/%d:%s/messages',
         $session->getRegistrationToken()->getMessengerUrl(),
         $conversation->getMode(),
         $conversation->getName()
      );
      $body = [
         'clientmessageid' => $dateTime->format('Uu'),
         'composetime' => $dateTime->format('Y-m-d\TH:i:s.v\Z'),
         'content' => $content,
         'messagetype' => 'RichText',
         'contenttype' => 'text',
         'imdisplayname' => $session->getAccount()->getConversation()->getLabel(),
         'receiverdisplayname' => $conversation->getLabel(),
      ];
      $response = $this->request('POST', $url, [
         'body' => json_encode($body),
         'authorization_session' => $session,
         'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
         ],
      ]);
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   /**
    * Load messages for Account from Conversation to Another Account or in the Group.
    * @param Session $session
    * @param Conversation $conversation
    * @param int $size Amount of messages.
    * @return array
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function loadMessages(Session $session, Conversation $conversation, $size = 100): array
   {
      if ($size > 199 or $size < 1)
      {
         $size = 199;
      }
      $url = sprintf(
         '%s/users/ME/conversations/%d:%s/messages',
         $session->getRegistrationToken()->getMessengerUrl(),
         $conversation->getMode(),
         $conversation->getName()
      );
      $response = $this->request('GET', $url, [
         'query' => [
            'startTime' => 0,
            'pageSize' => $size,
            'view' => 'msnp24Equivalent',
            'targetType' => 'Passport|Skype|Lync|Thread',
         ],
         'authorization_session' => $session,
         'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
         ],
      ]);
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   /**
    * Create Group with Admin from Session and Members from arguments.
    * @param Session $session
    * @param string $topic
    * @param Conversation[] $members
    * @return array
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function createGroup(Session $session, string $topic, array $members = []): array
   {
      $conversation = $session->getAccount()->getConversation();
      $body = [
         'members' => [
            [
               'id' => $conversation->getMode() . ':' . $conversation->getName(),
               'role' => 'Admin',
            ]
         ],
         'properties' => [
            'historydisclosed' => true,
            'topic' => $topic,
         ],
      ];
      foreach ($members as $member)
      {
         $body['members'][] = [
            'id' => $member->getMode() . ':' . $member->getName(),
            'role' => 'User',
         ];
      }
      $url = sprintf(
         '%s/threads',
         $session->getRegistrationToken()->getMessengerUrl()
      );
      $response = $this->request('POST', $url, [
         'body' => json_encode($body),
         'authorization_session' => $session,
         'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
         ],
      ]);
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   /**
    * Make server request.
    * Extends basic options by internal.
    * Catch redirects while request.
    * @param string $method
    * @param string $url
    * @param array $options [
    *    'authorization_session' => <Session> Add auth headers by Session.
    * ]
    * @param string $redirectUrl Returns url on that those 301 redirect.
    * @return \Symfony\Contracts\HttpClient\ResponseInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   private function request(string $method, string $url, array $options = [], string &$redirectUrl = null): ?ResponseInterface
   {
      if (isset($options['authorization_session']))
      {
         /* @var $session Session */
         $session = $options['authorization_session'];
         if ($session->getSkypeToken())
         {
            $options['headers']['X-Skypetoken'] = $session->getSkypeToken()->getSkypeToken();
            $options['headers']['Authentication'] = 'skypetoken=' . $session->getSkypeToken()->getSkypeToken();
         }
         if ($session->getRegistrationToken())
         {
            $options['headers']['RegistrationToken'] = 'registrationToken=' . $session->getRegistrationToken()->getRegistrationToken();
         }
         unset($options['authorization_session']);
      }
      $options['on_progress'] = function (int $dlNow, int $dlSize, array $info) use (&$redirectUrl): void
      {
         if (isset($info['http_code']) && ($info['http_code'] == 301))
         {
            foreach ($info['response_headers'] as $responseHeader)
            {
               if ('Location' === substr($responseHeader, 0, 8))
               {
                  $redirectUrl = trim(substr($responseHeader, 9));
                  break;
               }
            }
         }
      };
      try
      {
         $response = $this->httpClient->request($method, $url, $options);
      } catch (ClientException $exception)
      {
         if (!empty($redirectUrl))
         {
            $response = $this->request($method, $redirectUrl, $options, $redirectUrl);
         }
      }
      return $response;
   }

}