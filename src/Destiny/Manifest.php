<?php

namespace Destiny;

use Destiny\Api\Client;
use ZipArchive;
use SQLite3;

class Manifest
{
    public function __construct(Client $oDestinyApi = null)
    {
        $this->api = $oDestinyApi;
        $this->manifest_path = getcwd() .'/manifest/';
        $this->setting_file = $this->manifest_path . 'settings.json';
        $this->settings = $this->loadSettings();
    }

    public function check()
    {
        if(!$this->api)
            return false;

        $oManifest = $this->api->getManifest();
        if($oManifest && isset($oManifest->mobileWorldContentPaths->en))
        {
            $strDatabase = $oManifest->mobileWorldContentPaths->en;
            if($this->getSetting('database') != $strDatabase)
            {
                // New database found
                $aTables = $this->updateManifest($strDatabase);
                $this->setSetting('database', $strDatabase);
                $this->setSetting('tables', $aTables);
                return true;
            }
            else return true;
        }
        else return false;
    }

    private function updateManifest($strDatabase)
    {
        // Get Manifest zip
        $zData = $this->api->getManifest($strDatabase);
        $strCachePath = $this->manifest_path .'cache/'. pathinfo($strDatabase, PATHINFO_BASENAME);

        // Create cache folder if not exists
        if(!file_exists(dirname($strCachePath)))
            mkdir(dirname($strCachePath), 0777, true);

        // Store zip and unzip
        file_put_contents($strCachePath.'.zip', $zData);
        $zZip = new ZipArchive();
        if($zZip->open($strCachePath .'.zip') === true)
        {
            $zZip->extractTo($this->manifest_path .'cache');
            $zZip->close();
        }

        // Extract table names to export to settings
        $aTables = [];
        if($db = new SQLite3($strCachePath))
        {
            $oResult = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
            while($aRow = $oResult->fetchArray()) 
            {
                $aTable = [];
                $oResult2 = $db->query("PRAGMA table_info(". $aRow['name'] .")");
                while($aRow2 = $oResult2->fetchArray())
                {
                    $aTable[] = $aRow2[1];
                }
                $aTables[$aRow['name']] = $aTable;
            }
        }

        // Remove zip after extracting
        if(!empty($aTables) && is_writable($strCachePath .'.zip'))
            unlink($strCachePath .'.zip');

        return $aTables;
    }

    public function loadSettings()
    {
        if(!file_exists($this->setting_file))
            return (object)[];

        return json_decode(file_get_contents($this->setting_file));
    }

    public function setSetting($name, $value)
    {
        $this->settings->{$name} = $value;
        file_put_contents($this->setting_file, json_encode($this->settings));
    }

    public function getSetting($name)
    {
        if(isset($this->settings->{$name}))
            return $this->settings->{$name};

        return '';
    }

    public function queryManifest($strQuery)
    {
        $strDatabase = $this->getSetting('database');
        $strCacheFilePath = $this->manifest_path .'cache/'. pathinfo($strDatabase, PATHINFO_BASENAME);

        $aResults = [];
        if($db = new SQLite3($strCacheFilePath))
        {
            $oResult = $db->query($strQuery);
            while($aRow = $oResult->fetchArray())
            {
                $strKey = is_numeric($aRow[0]) ? sprintf('%u', $aRow[0] & 0xFFFFFFFF) : $aRow[0];
                $aResults[$strKey] = json_decode($aRow[1]);
            }
        }
        return $aResults;
    }

    public function browseDefinition($strTableName)
    {
        $strTableName = 'Destiny'. $strTableName .'Definition';
        return $this->queryManifest('SELECT * FROM '. $strTableName);
    }

    public function getDefinition($strTableName, $id)
    {
        $strTableName = 'Destiny'. $strTableName .'Definition';
        $aTables = $this->getSetting('tables');

        $strKey = $aTables->{$strTableName}[0];
        $strWhere = ' WHERE '. (is_numeric($id) ? $strKey .'='. $id .' OR '. $strKey .'='. ($id-4294967296) : $strKey .'="'. $id .'"');
        $aResults = $this->queryManifest('SELECT * FROM '. $strTableName . $strWhere);

        // Typecast to string since floats mess up index
        return isset($aResults[(string)$id]) ? $aResults[(string)$id] : false;
    }
}
?>