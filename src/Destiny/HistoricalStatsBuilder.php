<?php

namespace Destiny;

use Destiny\Exceptions\InvalidArgumentException;
use Destiny\Definitions\MembershipTypeDefinition;
use Destiny\Definitions\ComponentTypeDefinition;
use Destiny\Definitions\ActivityModeTypeDefinition;

class HistoricalStatsBuilder
{
    private $dayEnd;
    private $dayStart;
    private $groups = [];
    private $modes = [];
    private $periodType;

    public function withDayEnd($strDayEnd)
    {
        $this->dayEnd = $strDayEnd;

        return $this;
    }

    public function withDayStart($strDayStart)
    {
        $this->dayStart = $strDayStart;

        return $this;
    }

    public function withGroups($group)
    {
        if(is_array($group))
            $this->groups = array_merge($this->groups, $group);
        else
            $this->groups[] = $group;

        return $this;
    }

    public function withModes($mode)
    {
        if(is_array($mode))
            $this->modes = array_merge($this->modes, $mode);
        else
            $this->modes[] = $mode;

        return $this;
    }

    public function withPeriodType($strPeriodType)
    {
        $this->periodType = $strPeriodType;

        return $this;
    }

    public function build()
    {
        $this->validate();

        return $this;
    }

    public function validate()
    {
        if(!empty($this->groups))
        {
            $aGroups = ['general', 'weapons', 'medals'];
            foreach($this->groups as $i => $strGroup)
            {
                if(in_array(strtolower($strGroup), $aGroups))
                    $this->groups[$i] = ucfirst(strtolower($strGroup));
                else
                    throw new InvalidArgumentException('Invalid group of stats provided, available values: ', implode(', ', $aGroups));
            }
            $this->groups = array_unique($this->groups);
        }

        if(!empty($this->modes))
        {
            $oActivityModeTypeDefinition = new ActivityModeTypeDefinition;
            $aModes = $oActivityModeTypeDefinition->getOptions();

            foreach($this->modes as $i => $mode)
            {
                if(isset($aModes[strtoupper($mode)]))
                    $this->modes[$i] = $aModes[strtoupper($mode)];
                elseif(in_array($mode, array_values($aModes)))
                    $this->modes[$i] = $mode;
                else
                    throw new InvalidArgumentException('Invalid ActivityModeType provided');
            }
            $this->modes = array_unique($this->modes);
        }

        function validateDate($strDate)
        {
            $aDate = explode('-', $strDate);
            return count($aDate) == 3 && checkdate($aDate[1], $aDate[2], $aDate[0]);
        }

        if($this->dayEnd && !validateDate($this->dayEnd))
            throw new InvalidArgumentException('Invalid dayEnd format provided, use yyyy-mm-dd');

        if($this->dayEnd && !validateDate($this->dayStart))
            throw new InvalidArgumentException('Invalid dayEnd format provided, use yyyy-mm-dd');

        if($this->periodType)
        {
            $val = false;
            $aPeriodTypes = ['Daily', 'AllTime', 'Activity'];

            // Guess we better loop the options, for the right capitalization
            foreach($aPeriodTypes as $strPeriodType)
            {
                if(strtolower($this->periodType) == strtolower($strPeriodType))
                {
                    $val = $strPeriodType;
                    break;
                }
            }

            if($val)
                $this->periodType = $val;
            else
                throw new InvalidArgumentException('Invalid periodType provided, available values: '. implode(', ', $aPeriodTypes));
        }
    }

    public function getDayEnd()
    {
        return $this->dayEnd;
    }

    public function getDayStart()
    {
        return $this->dayStart;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getModes()
    {
        return $this->modes;
    }

    public function getPeriodType()
    {
        return $this->periodType;
    }

    public function getParameters()
    {
        return array_filter([
            'dayend' => $this->getDayEnd(),
            'daystart' => $this->getDayStart(),
            'groups' => (!empty($this->getGroups()) ? implode(',', $this->getGroups()) : null),
            'modes' => (!empty($this->getModes()) ? implode(',', $this->getModes()) : null),
            'periodType' => $this->getPeriodType()
        ]);
    }
}
?>