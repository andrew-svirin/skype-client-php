<?php

namespace AndriySvirin\SkypeBot;

use AndriySvirin\SkypeBot\exceptions\ClientException;
use AndriySvirin\SkypeBot\exceptions\ClientOauthMicrosoftLoginException;
use AndriySvirin\SkypeBot\exceptions\ClientOauthMicrosoftRedirectLoginException;
use AndriySvirin\SkypeBot\exceptions\ClientOauthSkypeLoginException;
use AndriySvirin\SkypeBot\exceptions\SessionException;
use AndriySvirin\SkypeBot\models\Account;
use AndriySvirin\SkypeBot\models\OAuthMicrosoft;
use AndriySvirin\SkypeBot\models\OAuthMicrosoftRedirect;
use AndriySvirin\SkypeBot\models\OAuthSkype;
use AndriySvirin\SkypeBot\models\Session;
use AndriySvirin\SkypeBot\services\SessionManager;
use DateTime;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Client
{

   const CLIENT_ID = 578134;

   /**
    * @var HttpClientInterface
    */
   private $httpClient;

   /**
    * @var SessionManager
    */
   private $sessionManager;

   public function __construct(SessionManager $sessionManager)
   {
      $this->httpClient = HttpClient::create([
         'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',
         ],
      ]);
      $this->sessionManager = $sessionManager;
   }

   /**
    * Build session headers for http requests.
    * @param Session $session
    * @return array
    */
   private function buildRequestHeaders(Session $session)
   {
      $result = [];
      if ($session->getSkypeToken())
      {
         $result['X-Skypetoken'] = $session->getSkypeToken();
         $result['Authentication'] = 'skypetoken=' . $session->getSkypeToken();
      }
      if ($session->getRegistrationToken())
      {
         $result['RegistrationToken'] = 'registrationToken=' . $session->getRegistrationToken();
      }
      return $result;
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
   private function loginOAuthMicrosoft(Session $session)
   {
      $response = $this->httpClient->request('GET', 'https://login.skype.com/login/oauth/microsoft', [
         'query' => [
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => 'https://web.skype.com/username=' . $session->getAccount()->getUsername(),
         ],
         'headers' => $this->buildRequestHeaders($session),
      ]);
      if (Response::HTTP_OK !== $response->getStatusCode())
      {
         throw new  ClientOauthMicrosoftLoginException('Incorrect status code');
      }
      $page = $response->getContent();
      preg_match('/urlPost:\'(.+)\',/isU', $page, $loginURL);
      preg_match('/name="PPFT" id="(.+)" value="(.+)"/isU', $page, $ppft);
      preg_match("/t:\'(.+)\',A/isU", $page, $ppsx);
      $headers = $response->getHeaders();
      if (empty($loginURL[1]) || empty($ppft[2]) || empty($ppsx[1]) || empty($headers['set-cookie']))
      {
         throw new  ClientOauthMicrosoftLoginException('Missing arguments');
      }
      $oAuthMicrosoft = new OAuthMicrosoft();
      $oAuthMicrosoft->setLoginUrl((string)$loginURL[1]);
      $oAuthMicrosoft->setPPFT((string)$ppft[2]);
      $oAuthMicrosoft->setPPSX((string)$ppsx[1]);
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
   private function loginOAuthMicrosoftRedirect(Session $session)
   {
      $response = $this->httpClient->request('POST', $session->getOAuthMicrosoft()->getLoginUrl(), [
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
         'headers' => array_merge($this->buildRequestHeaders($session), [
            'Cookie' => $session->getOAuthMicrosoft()->getCookies(),
         ]),
      ]);
      if (Response::HTTP_OK !== $response->getStatusCode())
      {
         throw new  ClientOauthMicrosoftRedirectLoginException('Incorrect status code');
      }
      $page = $response->getContent();
      preg_match('/<input type="hidden" name="NAP" id="NAP" value="(.+)">/isU', $page, $NAP);
      preg_match('/<input type="hidden" name="ANON" id="ANON" value="(.+)">/isU', $page, $ANON);
      preg_match('/<input type="hidden" name="t" id="t" value="(.+)">/isU', $page, $t);
      $headers = $response->getHeaders();
      if (empty($NAP[1]) || empty($ANON[1]) || empty($t[1]) || empty($headers['set-cookie']))
      {
         throw new  ClientOauthMicrosoftRedirectLoginException('Missing arguments');
      }
      $oAuthMicrosoftRedirect = new OAuthMicrosoftRedirect();
      $oAuthMicrosoftRedirect->setNAP((string)$NAP[1]);
      $oAuthMicrosoftRedirect->setANON((string)$ANON[1]);
      $oAuthMicrosoftRedirect->setT((string)$t[1]);
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
   private function loginOauthSkype(Session $session)
   {
      $response = $this->httpClient->request('POST', 'https://lw.skype.com/login/oauth/proxy', [
         'query' => [
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => 'https://web.skype.com/&site_name=lw.skype.com&wa=wsignin1.0',
         ],
         'body' => [
            'NAP' => $session->getOAuthMicrosoftRedirect()->getNAP(),
            'ANON' => $session->getOAuthMicrosoftRedirect()->getANON(),
            't' => $session->getOAuthMicrosoftRedirect()->getT(),
         ],
         'headers' => array_merge($this->buildRequestHeaders($session), [
            'Cookie' => $session->getOAuthMicrosoftRedirect()->getCookies(),
         ]),
      ]);
      if (Response::HTTP_OK !== $response->getStatusCode())
      {
         throw new  ClientOauthSkypeLoginException('Incorrect status code');
      }
      $page = $response->getContent();
      preg_match('/<input type="hidden" name="t" value="(.+)"\/>/isU', $page, $t);
      if (empty($t[1]))
      {
         throw new  ClientOauthSkypeLoginException('Missing arguments');
      }
      $oAuthSkype = new OAuthSkype();
      $oAuthSkype->setT((string)$t[1]);
      return $oAuthSkype;
   }

   /**
    * Login to the Skype and setup SkypeToken.
    * This method depends of @see loginOauthSkype retrieved values.
    * @param Session $session
    * @return string
    * @throws SessionException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   private function loginSkype(Session $session)
   {
      $response = $this->httpClient->request('POST', 'https://login.skype.com/login/microsoft', [
         'query' => [
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => 'https://web.skype.com/',
         ],
         'body' => [
            't' => $session->getOAuthSkype()->getT(),
            'site_name' => 'lw.skype.com',
            'oauthPartner' => 999,
            'form' => '',
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => 'https://web.skype.com/'
         ],
      ]);
      if (Response::HTTP_OK !== $response->getStatusCode())
      {
         throw new  SessionException('Incorrect status code');
      }
      $page = $response->getContent();
      preg_match('/<input type="hidden" name="skypetoken" value="(.+)"\/>/isU', $page, $skypeToken);
      if (empty($skypeToken[1]))
      {
         throw new  SessionException('Missing skypeToken');
      }
      $result = (string)$skypeToken[1];
      return $result;
   }

   /**
    * Register in the Skype and setup registrationToken.
    * This method depends of @see loginSkype retrieved values.
    * @param Session $session
    * @return string
    * @throws SessionException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    * @throws ClientOauthMicrosoftLoginException
    */
   private function registerSkype(Session $session)
   {
      $response = $this->httpClient->request('POST', 'https://bn2-client-s.gateway.messenger.live.com/v1/users/ME/endpoints', [
         'body' => '{}',
         'headers' => array_merge($this->buildRequestHeaders($session), [
            'Cookie' => $session->getOAuthMicrosoft()->getCookies(),
         ]),
      ]);
      if (Response::HTTP_OK !== $response->getStatusCode())
      {
         throw new ClientOauthMicrosoftLoginException(sprintf('Incorrect status code %s', $response->getStatusCode()));
      }
      $headers = $response->getHeaders();
      if (empty($headers['set-registrationtoken'][0]))
      {
         throw new SessionException('Missing registrationToken');
      }
      preg_match('/registrationToken=(.+);/isU', $headers['set-registrationtoken'][0], $registrationToken);
      if (empty($registrationToken[1]))
      {
         throw new SessionException('Missing registrationToken');
      }
      $result = (string)$registrationToken[1];
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
    * @throws exceptions\AccountCacheFileSaveException
    * @throws exceptions\SessionDirCreateException
    * @throws exceptions\SessionFileLoadException
    * @throws exceptions\SessionFileRemoveException
    */
   public function login(Account $account, DateTime $now = null)
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
         $skypeToken = $this->loginSkype($session);
         $session->setSkypeToken($skypeToken);
         $registrationToken = $this->registerSkype($session);
         $session->setRegistrationToken($registrationToken);
         $this->sessionManager->saveSession($session);
      }
      elseif ($session->isExpired($now))
      {
         $this->sessionManager->destroyAccountSession($session);
      }
      return $session;
   }

   /**
    * @param Session $session
    * @return array
    * @throws ClientException
    * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
    * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
    */
   public function loadMyProperties(Session $session)
   {
      $response = $this->httpClient->request('GET', 'https://bn2-client-s.gateway.messenger.live.com/v1/users/ME/properties', [
         'headers' => $this->buildRequestHeaders($session),
      ]);
      if (Response::HTTP_OK !== $response->getStatusCode())
      {
         throw new ClientException(sprintf('Incorrect status code %s', $response->getStatusCode()));
      }
      $result = json_decode($response->getContent(), true);
      return $result;
   }

   private function web($url, $mode = "GET", $post = [], $showHeaders = false, $follow = true, $customCookies = "", $customHeaders = [])
   {

      if (!function_exists("curl_init"))
         exit(trigger_error("Skype : cURL is required", E_USER_WARNING));

      if (!empty($post) && is_array($post))
         $post = http_build_query($post);

      if ($this->logged && time() >= $this->expiry)
      {
         $this->logged = false;
         $this->login();
      }

      $headers = $customHeaders;
      if (isset($this->skypeToken))
      {
         $headers[] = "X-Skypetoken: {$this->skypeToken}";
         $headers[] = "Authentication: skypetoken={$this->skypeToken}";
      }

      if (isset($this->registrationToken))
         $headers[] = "RegistrationToken: registrationToken={$this->registrationToken}";

      $curl = curl_init();

      curl_setopt($curl, CURLOPT_URL, $url);
      if (!empty($headers))
         curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $mode);
      if (!empty($post))
      {
         curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
      }
      if ($customCookies)
         curl_setopt($curl, CURLOPT_COOKIE, $customCookies);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36");
      curl_setopt($curl, CURLOPT_HEADER, $showHeaders);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $follow);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($curl);

      curl_close($curl);
      return $result;
   }

   public function logout()
   {
      if (!$this->logged)
         return true;

      unlink("{$this->folder}/auth_{$this->username}");
      unset($this->skypeToken);
      unset($this->registrationToken);

      return true;
   }

   private function URLToUser($url)
   {
      $url = explode(":", $url, 2);

      return end($url);
   }

   private function timestamp()
   {
      return str_replace(".", "", microtime(1));
   }

   public function sendMessage($user, $message)
   {
      $user = $this->URLtoUser($user);
      $mode = strstr($user, "thread.skype") ? 19 : 8;
      $messageID = $this->timestamp();
      $post = [
         "content" => $message,
         "messagetype" => "RichText",
         "contenttype" => "text",
         "clientmessageid" => $messageID,
         'Has-Mentions' => false,
         'imdisplayname' => 'Andriy Svirin',
      ];
      $req = json_decode($this->web("https://bn2-client-s.gateway.messenger.live.com/v1/users/ME/conversations/$mode:$user/messages", "POST", json_encode($post)), true);

      return isset($req["OriginalArrivalTime"]) ? $messageID : 0;
   }

   public function getMessagesList($user, $size = 100)
   {
      $user = $this->URLtoUser($user);
      if ($size > 199 or $size < 1)
         $size = 199;
      $mode = strstr($user, "thread.skype") ? 19 : 8;

      $req = json_decode($this->web("https://bn2-client-s.gateway.messenger.live.com/v1/users/ME/conversations/$mode:$user/messages?startTime=0&pageSize=$size&view=msnp24Equivalent&targetType=Passport|Skype|Lync|Thread"), true);

      return !isset($req["message"]) ? $req["messages"] : [];
   }

   public function createGroup($users = [], $topic = "")
   {
      $users = [];

      foreach ($users as $user)
         $members["members"][] = ["id" => "8:" . $this->URLtoUser($user), "role" => "User"];

      $members["members"][] = ["id" => "8:{$this->username}", "role" => "Admin"];

      $req = $this->web("https://bn2-client-s.gateway.messenger.live.com/v1/threads", "POST", json_encode($members), true);
      preg_match("`19\:(.+)\@thread.skype`isU", $req, $group);

      $group = isset($group[1]) ? "{$group[1]}@thread.skype" : "";

      if (!empty($topic) && !empty($group))
         $this->setGroupTopic($group, $topic);

      return $group;
   }

   public function setGroupTopic($group, $topic)
   {
      $group = $this->URLtoUser($group);
      $post = [
         "topic" => $topic
      ];

      $this->web("https://bn2-client-s.gateway.messenger.live.com/v1/threads/19:$group/properties?name=topic", "PUT", json_encode($post));
   }

   public function getGroupInfo($group)
   {
      $group = $this->URLtoUser($group);
      $req = json_decode($this->web("https://bn2-client-s.gateway.messenger.live.com/v1/threads/19:$group?view=msnp24Equivalent", "GET"), true);

      return !isset($req["code"]) ? $req : [];
   }

   public function addUserToGroup($group, $user)
   {
      $user = $this->URLtoUser($user);
      $post = [
         "role" => "User"
      ];

      $req = $this->web("https://bn2-client-s.gateway.messenger.live.com/v1/threads/19:$group/members/8:$user", "PUT", json_encode($post));

      return empty($req);
   }

   public function kickUser($group, $user)
   {
      $user = $this->URLtoUser($user);
      $req = $this->web("https://bn2-client-s.gateway.messenger.live.com/v1/threads/19:$group/members/8:$user", "DELETE");

      return empty($req);
   }

   public function leaveGroup($group)
   {
      $req = $this->kickUser($group, $this->username);

      return $req;
   }

   public function ifGroupHistoryDisclosed($group, $historydisclosed)
   {
      $group = $this->URLtoUser($group);
      $post = [
         "historydisclosed" => $historydisclosed
      ];

      $req = $this->web("https://bn2-client-s.gateway.messenger.live.com/v1/threads/19:$group/properties?name=historydisclosed", "PUT", json_encode($post));

      return empty($req);
   }

   public function getContactsList()
   {
      $req = json_decode($this->web("https://contacts.skype.com/contacts/v1/users/{$this->username}/contacts?\$filter=type%20eq%20%27skype%27%20or%20type%20eq%20%27msn%27%20or%20type%20eq%20%27pstn%27%20or%20type%20eq%20%27agent%27&reason=default"), true);

      return isset($req["contacts"]) ? $req["contacts"] : [];
   }

   public function readProfile($list)
   {
      $contacts = "";
      foreach ($list as $contact)
         $contacts .= "contacts[]=$contact&";

      $req = json_decode($this->web("https://api.skype.com/users/self/contacts/profiles", "POST", $contacts), true);

      return !empty($req) ? $req : [];
   }

   public function readMyProfile()
   {
      $req = json_decode($this->web("https://api.skype.com/users/self/profile"), true);

      return !empty($req) ? $req : [];
   }

   public function searchSomeone($username)
   {
      $username = $this->URLtoUser($username);
      $req = json_decode($this->web("https://skypegraph.skype.com/search/v1.1/namesearch/swx/?requestid=skype.com-1.63.51&searchstring=$username"), true);
      return !empty($req) ? $req : [];
   }

   public function addContact($username, $greeting = "Hello, I would like to add you to my contacts.")
   {
      $username = $this->URLtoUser($username);
      $post = [
         "greeting" => $greeting
      ];

      $req = $this->web("https://api.skype.com/users/self/contacts/auth-request/$username", "PUT", $post);
      $data = json_decode($req, true);

      return isset($data["code"]) && $data["code"] == 20100;
   }

   public function skypeJoin($id)
   {
      $post = [
         "shortId" => $id,
         "type" => "wl"
      ];
      $group = $this->web("https://join.skype.com/api/v2/conversation/", "POST", json_encode($post), false, false, false, ["Content-Type: application/json"]);
      $group = json_decode($group, true);

      if (!isset($group["Resource"]))
         return "";

      $group = str_replace("19:", "", $group["Resource"]);

      return $this->addUserToGroup($group, $this->username);
   }
}