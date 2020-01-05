<?php

namespace Destiny\Definitions;

use Destiny\Definitions\Definition;

class MembershipTypeDefinition extends Definition
{
    const XBOX = 1;
    const PSN = 2;
    const STEAM = 3;
    const BLIZZARD = 4;
    const STADIA = 5;
    const BNET = 254;
    const ALL = -1;
}