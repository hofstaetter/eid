<?php

namespace Hofstaetter\Eid;

use Gamegos\JWS\JWS;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'EID';

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * Indicates if the session state should be utilized.
     *
     * @var bool
     */
    protected $stateless = true;

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['openid', 'profile', 'eid'];

    /**
     * @return \SocialiteProviders\Manager\OAuth2\User
     *
     * @throws \Laravel\Socialite\Two\InvalidStateException
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $response = $this->getAccessTokenResponse($this->getCode());
        $this->credentialsResponseBody = $response;

        $this->user = $this->mapUserToObject($this->getUserByToken(
            $token = $this->parseIdToken($response)
        ));

        if ($this->user instanceof User) {
            $this->user->setAccessTokenResponseBody($this->credentialsResponseBody);
        }

        return $this->user->setToken($token)
                    ->setRefreshToken($this->parseRefreshToken($response))
                    ->setExpiresIn($this->parseExpiresIn($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getBaseUrl().'/auth/idp/profile/oidc/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getBaseUrl().'/auth/idp/profile/oidc/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        return (new JWS())->decode($token)['payload'];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'        => $user['urn:pvpgvat:oidc.bpk'],
            'nickname'  => $user['given_name'],
            'name'      => "{$user['given_name']} {$user['family_name']}",
            'email'     => null,
            'avatar'    => null,
        ]);
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return 'https://'.$this->getConfig('endpoint', 'eid.oesterreich.gv.at');
    }

    /**
     * Get the id token from the token response body.
     *
     * @param  array  $body
     * @return string
     */
    protected function parseIdToken($body)
    {
        return $body['id_token'];
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['endpoint'];
    }
}
