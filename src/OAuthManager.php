<?php
namespace JT\JTOAuth;

use Symfony\Component\HttpFoundation\Request;
use OAuth\ServiceFactory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use Psr\Log\LoggerInterface;
use OAuth\Common\Token\Exception\ExpiredTokenException;
use OAuth\OAuth2\Service\Exception\InvalidAuthorizationStateException;
use OAuth\Common\Http\Exception\TokenResponseException;

/**
 * @author nvb <nvb@aproxima.ru>
 *
 */
class OAuthManager
{
    /**
     * @var AuthConfig
     */
    protected $config;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var JTOAuthUser
     */
    protected $user = null;
    
    /**
     * @var JTOAuthService
     */
    protected $service = false;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var string
     */
    protected $error = false;
    
    /**
     * @var integer
     */
    protected $errorNo = 0;
    
    const ERROR_TOKEN_REQUEST_FAILED = 1;
    const ERROR_USER_REQUEST_FAILED = 2;
    const ERROR_SECURITY_STATE_NOT_MATCHED = 3;
    
    /**
     * @param AuthConfig $config
     * @param Request    $request
     */
    public function __construct(OAuthConfig $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
        
        if ($user = $request->getSession()->get('jtoauth_user')) {
            $this->user = $user;
        }
    }
    
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;    
    }
    
    /**
     * @return boolean
     */
    public function isAuthorised()
    {
        return (bool) $this->getUser();
    }
    
    /**
     * @return JTOAuthUser
     */
    public function getUser()
    {
        if ($this->user === null) {
            try {
                $raw = json_decode($this->getService()->request('/api/passport', 'POST'), true);
                
                if ($raw && $raw['data']) {
                    $data = $raw['data'];
                    
                    $this->user = new JTOAuthUser(
                        $data['id'],
                        $data['first_name'],
                        $data['second_name'],
                        $data['last_name'],
                        $data['company'],
                        $data['mail'],
                        $data['phone'],
                        $data['roles']
                    );
                }
            } catch (TokenResponseException $e) {
                $this->error = $e->getMessage();
                $this->errorNo = self::ERROR_USER_REQUEST_FAILED;
                        
                if ($this->logger) {
                    $this->logger->warning('User request failed: ' . $e->getMessage());
                }
                
                $this->user = false;
            } catch (TokenNotFoundException $e) {
                if ($this->logger) {
                    $this->logger->warning('AccessToken not found');
                }
                
                $this->user = false;
            } catch (ExpiredTokenException $e) {
                if ($this->logger) {
                    $this->logger->notice('AccessToken expired');
                }
                
                $this->user = false;
            }
        }
        
        return $this->user;
    }
    
    /**
     * @return JTOAuthService
     */
    protected function initService()
    {
        $serviceFactory = new ServiceFactory();
        
        $credentials = new Credentials(
            $this->config->getOAuthId(),
            $this->config->getOAuthSecret(),
            $this->config->getOAuthRedirectUrll()->getAbsoluteUri()
        );
        
        $storage = new SymfonySession($this->request->getSession());
        
        $serviceFactory->registerService('jtoauth', JTOAuthService::class);
        $this->service = $serviceFactory->createService('jtoauth', $credentials, $storage, $this->config->getOAuthScopes(), $this->config->getOAuthUrl());
        
        return $this->service;
    }
    
    /**
     * @return JTOAuthService
     */
    public function getService()
    {
        return $this->service ?: $this->initService();
    }
    
    /**
     * 
     */
    public function startAuthorisationProcess()
    {
        $response = new RedirectResponse($this->getService()->getAuthorizationUri()->__toString());
        $response->send();
        exit();
    }
    
    /**
     * @return boolean
     */
    public function processResponse()
    {
        try {
            $this->getService()->requestAccessToken($this->request->get('code'), $this->request->get('state'));
            
            return true;
        } catch (InvalidAuthorizationStateException $e) {
            $this->error = 'Полученное значение безопасности не совпадает с исходным.';
            $this->errorNo = self::ERROR_SECURITY_STATE_NOT_MATCHED;
            
            if ($this->logger) {
                $this->logger->error('Попытка подмены значения безопасности.');
            }
        } catch (TokenResponseException $e) {
            $this->error = $e->getMessage();
            $this->errorNo = self::ERROR_TOKEN_REQUEST_FAILED;
            
            if ($this->logger) {
                $this->logger->warning('Ошибка получения токена');
            }
        }
        
        return false;
    }
    
    /**
     * @return number
     */
    public function getLastErrorNo()
    {
        return $this->errorNo;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->error;
    }
}