<?php

namespace Destiny;

use Destiny\Manifest;

class EquipmentItem
{
    public function __construct($oEquipmentItem)
    {
        $this->itemInstanceId = isset($oEquipmentItem->itemInstanceId) ? $oEquipmentItem->itemInstanceId : 0;
        $this->itemHash = $oEquipmentItem->itemHash;
    }

    public function load($oItemInstance, $aSockets = [], $bPerks)
    {
        // These bucket types we want to show
        $aBucketTypeHashes = [
            1469714392, // Consumables
            3313201758, // Modifications
            2422292810 // BUCKET_TEMPORARY 🤔
        ];

        $oManifest = new Manifest;
        $oItem = $oManifest->getDefinition('InventoryItem', $this->itemHash);

        // Set basic item info
        $this->name = $oItem->displayProperties->name;
        $this->bucketTypeHash = isset($oItem->inventory->bucketTypeHash) ? $oItem->inventory->bucketTypeHash : 0;
        $this->light = isset($oItemInstance->primaryStat->value) ? $oItemInstance->primaryStat->value : 0;
        $this->quantity = isset($oItemInstance->quantity) ? $oItemInstance->quantity : 1;

        if($bPerks && !$oItem->redacted)
        {
            if(!empty($aSockets))
            {
                foreach($aSockets as $oSocket)
                {
                    if($oSocket->isEnabled && $oSocket->isVisible)
                    {
                        $oPlug = $oManifest->getDefinition('InventoryItem', $oSocket->plugHash);

                        // Show progress if tracker is enabled
                        if(isset($oSocket->plugObjectives[0]) && $oSocket->plugObjectives[0]->visible)
                        {
                            $oObjective = $oManifest->getDefinition('Objective', $oSocket->plugObjectives[0]->objectiveHash);
                            if(isset($oObjective->progressDescription) && trim($oObjective->progressDescription) != '')
                                $oPlug->displayProperties->name .= ' ('. $oObjective->progressDescription .': '. $oSocket->plugObjectives[0]->progress .')';
                        }

                        // Show tier upgrade type
                        if(strpos($oPlug->displayProperties->name, 'Tier ') !== false && isset($oPlug->investmentStats[0]))
                        {
                            $oStat = $oManifest->getDefinition('Stat', $oPlug->investmentStats[0]->statTypeHash);
                            if(isset($oStat->displayProperties->name))
                                $oPlug->displayProperties->name = 'Tier '. $oPlug->investmentStats[0]->value .' ('. $oStat->displayProperties->name .')';
                        }

                        // Only show perks from certain bucket types
                        if(in_array($oPlug->inventory->bucketTypeHash, $aBucketTypeHashes))
                            $this->perks[] = $oPlug->displayProperties->name;
                    }
                }
            }
        }

        // If item has costs
        if(isset($oItemInstance->costs) && !empty($oItemInstance->costs))
        {
            $aCosts = [];
            foreach($oItemInstance->costs as $oCost)
            {
                $oCostItem = new EquipmentItem($oCost);
                $oCostItem->load($oCost, [], false);
                unset($oCostItem->itemInstanceId, $oCostItem->bucketTypeHash, $oCostItem->light);
                $aCosts[] = $oCostItem;
            }
            $this->costs = $aCosts;
        }
    }
}
?>