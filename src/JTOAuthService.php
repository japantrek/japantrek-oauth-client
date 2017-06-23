<?php
namespace JT\JTOAuth;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;

/**
 * @author nvb <nvb@aproxima.ru>
 *
 */
class JTOAuthService extends AbstractService
{

    /**
     * @param CredentialsInterface 	$credentials
     * @param ClientInterface 		$httpClient
     * @param TokenStorageInterface $storage
     * @param array 				$scopes
     * @param UriInterface 			$baseApiUri
     * @param string 				$stateParameterInAutUrl
     * @param string 				$apiVersion
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
	)
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri ?: new Uri('https://cars.japantrek.ru'), true);
    }


    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri($this->baseApiUri.'/oauth/v2/auth');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->baseApiUri.'/oauth/v2/token');
    }

    /**
     * {@inheritdoc}
     * @see \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse()
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token($data['access_token'], $data['refresh_token'], $data['expires_in']);

        unset($data['access_token']);
        unset($data['refresh_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * Returns a class constant from ServiceInterface defining the authorization method used for the API
     * Header is the sane default.
     *
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }
    
    /**
     * {@inheritDoc}
     * @see AbstractService::isValidScope()
     */
    public function isValidScope($scope)
    {
    	return true;
    }
}