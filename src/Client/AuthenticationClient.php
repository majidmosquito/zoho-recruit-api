<?php

namespace Humantech\Zoho\Recruit\Api\Client;

class AuthenticationClient extends AbstractClient implements AuthenticationClientInterface
{
    const API_AUTH_URL = 'https://accounts.zoho.eu/apiauthtoken/nb/create?SCOPE=%s&EMAIL_ID=%s&PASSWORD=%s';

    /**
     * @param  string $username
     * @param  string $password
     *
     * @return string
     */
    protected function getApiResponseAuthToken($username, $password, $scope = 'ZohoRecruit/recruitapi')
    {
        $response = $this->sendRequest('POST', sprintf(
            self::API_AUTH_URL,
            urlencode($scope),
            urlencode($username),
            urlencode($password)
        ));

        return $response->getBody()->getContents();
    }

    /**
     * @inheritdoc
     */
    public function generateAuthToken($username, $password, $scope = 'ZohoRecruit/recruitapi')
    {
        $content = $this->getApiResponseAuthToken($username, $password, $scope);

        if (strpos($content, 'RESULT=TRUE') === false) {
            preg_match('/CAUSE\=(\w*)/i', $content, $matches);

            throw new AuthenticationClientException($matches[1]);
        }

        preg_match('/AUTHTOKEN\=(\S*)/i', $content, $matches);

        return $matches[1];
    }
}
