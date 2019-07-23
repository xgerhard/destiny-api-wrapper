<?php

namespace Destiny\Api;

use GuzzleHttp\Client as GuzzleClient;
use Destiny\Exceptions\DestinyApiException;

class Client
{
    private $apiKey;
    private $baseUrl = 'https://www.bungie.net/Platform/Destiny2';

    public function __construct($strApiKey)
    {
        $this->apiKey = $strApiKey;
    }

    public function searchDestinyPlayer($strDisplayName, $iMembershipType = null)
    {
        return $this->request(
            '/SearchDestinyPlayer/'. ($iMembershipType ? $iMembershipType : '-1') .'/'. rawurldecode($strDisplayName)
        );
    }

    public function getLinkedProfiles($iMembershipType, $iMembershipId, $bAllMemberships = false)
    {
        return $this->request(
            '/'. $iMembershipType .'/Profile/'. $iMembershipId .'LinkedPrifles/',
            ['getAllMemberships' => $bAllMemberships]
        );
    }

    public function getProfile($iMembershipType, $iMembershipId, $aComponents = [200])
    {
        return $this->request(
            '/'. $iMembershipType .'/Profile/'. $iMembershipId .'/',
            ['components' => implode(',', $aComponents)]
        );
    }

    public function getCharacter($iMembershipType, $iMembershipId, $iCharacterId, $aComponents = [])
    {
        return $this->request(
            '/'. $iMembershipType .'/Profile/'. $iMembershipId .'/Character/'. $iCharacterId .'/',
            ['components' => implode(',', $aComponents)]
        );
    }

    public function getVendors($iMembershipType, $iMembershipId, $iCharacterId, $aComponents = [])
    {
        return $this->request(
            '/'. $iMembershipType .'/Profile/'. $iMembershipId .'/Character/'. $iCharacterId .'/Vendors/',
            ['components' => implode(',', $aComponents)]
        );
    }

    public function getVendor($iMembershipType, $iMembershipId, $iCharacterId, $iVendorHash, $aComponents = [])
    {
        return $this->request(
            '/'. $iMembershipType .'/Profile/'. $iMembershipId .'/Character/'. $iCharacterId .'/Vendors/'. $iVendorHash .'/',
            ['components' => implode(',', $aComponents)]
        );
    }

    public function getPublicVendors()
    {
        
    }

    public function getHistoricalStats($iMembershipType, $iMembershipId, $iCharacterId = 0, $aParameters = [])
    {
        return $this->request(
            '/'. $iMembershipType .'/Account/'. $iMembershipId .'/Character/'. $iCharacterId .'/Stats/',
            $aParameters
        );
    }

    public function getActivityHistory()
    {
        
    }

    private function request($strUrl, $aParameters = [], $strMethod = 'GET', $aHeaders = [], $aPost = [])
    {
        $aHeaders = array_merge([
            'X-API-Key' => $this->apiKey
        ], $aHeaders);

        $aRequestData = [];
        if(!empty($aHeaders)) $aRequestData['headers'] = $aHeaders;
        if(!empty($aPost)) $aRequestData['form_params'] = $aPost;

        $strUrl = $this->baseUrl . $strUrl;
        if(!empty($aParameters))
            $strUrl .= '?'. http_build_query($aParameters);

        $oGuzzle = new GuzzleClient([
            'http_errors' => false, 
            'verify' => false
        ]);

        $oResponse = $oGuzzle->request($strMethod, $strUrl, $aRequestData);
        $oResponseBody = json_decode($oResponse->getBody()->getContents());
        if($oResponseBody->ErrorCode != 1)
            throw new DestinyApiException($oResponseBody->Message);
        else
            return $oResponseBody->Response;
    }
}