<?php
namespace JT\JTOAuth;

use OAuth\Common\Http\Uri\Uri;

/**
 * @author nvb <nvb@aproxima.ru>
 *
 */
class OAuthConfig
{
    protected $oauthId;
    protected $oauthSecret;
    protected $oauthRedirectUrl;
    protected $oauthUrl;
    protected $oauthScopes;
    
    /**
     * @param string $oauthId
     * @param string $oauthSecret
     * @param string $oauthRedirectUrl
     * @param array  $oauthScopes
     * @param string $oauthUrl     
     */
    public function __construct($oauthId, $oauthSecret, $oauthRedirectUrl, $oauthScopes= array(), $oauthUrl= 'https://cars.japantrek.ru')
    {
        $this->oauthId= $oauthId;
        $this->oauthSecret= $oauthSecret;
        $this->oauthRedirectUrl= new Uri($oauthRedirectUrl);
        $this->oauthUrl= new Uri($oauthUrl);
        $this->oauthScopes= $oauthScopes;
    }
    
    /**
     * @return string
     */
    public function getOAuthId()
    {
        return $this->oauthId;
    }
    
    /**
     * @return string
     */
    public function getOAuthSecret()
    {
        return $this->oauthSecret;
    }
    
    /**
     * @return Uri
     */
    public function getOAuthRedirectUrll()
    {
        return $this->oauthRedirectUrl;
    }
    
    /**
     * @return Uri
     */
    public function getOAuthUrl()
    {
        return $this->oauthUrl;
    }
    
    /**
     * @return array
     */
    public function getOAuthScopes()
    {
        return $this->oauthScopes;
    }
}