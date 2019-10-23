<?php

namespace Destiny\Collections;

use Destiny\EquipmentItem;

class InventoryCollection
{
    private $items = [];
    private $instances;
    private $sockets;

    public function __construct($aInventory)
    {
        if(!$aInventory)
            return;
        else
        {
            if(isset($aInventory['equipment']) && !empty($aInventory['equipment']))
            {
                foreach($aInventory['equipment'] as $oEquipmentItem)
                {
                    $this->items[$oEquipmentItem->bucketHash] = $oEquipmentItem;
                }
            }

            $this->instances = $aInventory['instances'];
            $this->sockets = $aInventory['sockets'];
        }
    }

    public function getWeapons($bPerks)
    {
        return $this->get(['primary', 'secondary', 'heavy'], $bPerks);
    }

    public function getGear($bPerks)
    {
        return $this->get(['helmet', 'gauntlet', 'chest', 'legs'], $bPerks);
    }

    public function get($aSearchItems, $bPerks = false)
    {
        $aHashes = [
            'primary' => 1498876634,
            'secondary' => 2465295065,
            'heavy' => 953998645,
            'helmet' => 3448274439,
            'gauntlet' => 3551918588,
            'chest' => 14239492,
            'legs' => 20886954,
            'classitem' => 1585787867,
            'ghost' => 4023194814,
            'vehicle' => 2025709351,
            'ship' => 284967655,
            'subclass' => 3284755031,
            'clan' => 4292445962,
            'emblem' => 4274335291,
            'emote' => 3054419239,
            'aura' => 1269569095
        ];

        $bArray = true;
        if(!is_array($aSearchItems))
        {
            $aSearchItems = [$aSearchItems];
            $bArray = false;
        }

        $a = [];
        foreach($aSearchItems as $strSearchItem)
        {
            $strSearchItem = strtolower($strSearchItem);
            $oItem = false;

            if(isset($aHashes[$strSearchItem]))
            {
                $iItemHash = $aHashes[$strSearchItem];
                if(isset($this->items[$iItemHash]))
                {
                    $oItem = new EquipmentItem($this->items[$iItemHash]);
                    $oItem->load(
                        $this->instances->{$oItem->itemInstanceId},
                        (isset($this->sockets->{$oItem->itemInstanceId}->sockets) ? $this->sockets->{$oItem->itemInstanceId}->sockets : []),
                        !!$bPerks
                    );
                }
            }
            $a[$strSearchItem] = $oItem;
        }
        return $bArray === false ? $a[$strSearchItem] : $a;
    }
}