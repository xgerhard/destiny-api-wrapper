# Destiny2 PHP API wrapper


A PHP Client/API wrapper for the Destiny 2 API

## Installation

Run the following command:

```shell
composer require xgerhard/destiny-api-wrapper
```

## Usage

### DestinyClient

Get your API-key here: <https://bungie.net/en/application>

```php
$oDestiny = new Destiny\Client('Bungie-API-key-here');
```

**Searching for a player**

```php
use Destiny\Client;
use Destiny\Exceptions\PlayerNotFoundException;

try
{
    $oDestiny = new Destiny\Client('Bungie-API-key-here');
    $oPlayer = $oDestiny->searchPlayer('xgerhard');

    echo $oPlayer->membershipId; // 4611686018467322796
    echo $oPlayer->membershipType; // 4
    echo $oPlayer->displayName; // xgerhard#21555

    // There are multiple ways of searching a player
    // Search on all platforms by player username
    $oPlayer = $oDestiny->searchPlayer('xgerhard');

    // Search on specific platform by player username (1 = xbox, 2 = ps, 3 = pc)
    $oPlayer = $oDestiny->searchPlayer('xgerhard', 1);

    // Search by BungieNet uniqueName
    // Set an unique ID here: https://www.bungie.net/en/Profile/Settings/?category=AboutMe
    // Link the platforms you play on here: https://www.bungie.net/en/Profile/Settings/?category=Accounts
    // By using profiles->getCurrent you will receive the platform you played on most recently
    $oPlayer = $oDestiny->searchUser('xgerhard')->profiles->getCurrent();

    // Combination of searchPlayer & searchUser
    // Search by platform username first, if no results search by uniqueName on BungieNet
    $oPlayer = $oDestiny->searchPlayerUser('xgerhard');

    // Search on specific platform only, if no results search by uniqueName on BungieNet
    $oPlayer = $oDestiny->searchPlayerUser('xgerhard', 1);

    // $oPlayer = Destiny\Player
}
catch(PlayerNotFoundException $e)
{
    // If no players could be found
    echo $e->getMessage();
}
```

**loadPlayer**

If you store the player details in for example a database, you can load the `Destiny\Player` object by providing (at least) the player `membershipType` and `membershipId`.

```php
use Destiny\Client;
use Destiny\Exceptions\InvalidPlayerParametersException;

try
{
    $oDestiny = new Destiny\Client('Bungie-API-key-here');
    $oPlayer = $oDestiny->loadPlayer([
        'membershipType' => 4,
        'membershipId' => '4611686018467322796',
        'displayName' => 'xgerhard#21555'
    ]);

    // $oPlayer = Destiny\Player
}
catch(InvalidPlayerParametersException $e)
{
    // If required player data is missing
    echo $e->getMessage();
}
```

### Destiny\Player
#### CharacterCollection

The player characters are stored in a character collection. This collection can be queried to get the character data.

**getAll()**

Returns all characters

```php
$oPlayer = $oDestiny->searchPlayer('xgerhard');
$aCharacters = $oPlayer->characters->getAll();

/*
Array
(
    [2305843009301405871] => Destiny\Character Object
        (
            [membershipId] => 4611686018467322796
            [membershipType] => 4
            [characterId] => 2305843009301405871
            ....
        )

    [2305843009301408262] => Destiny\Character Object
        (
            [membershipId] => 4611686018467322796
            [membershipType] => 4
            [characterId] => 2305843009301408262
            ....
        )
)
*/
```

**getCurrent()**

Returns the current (last played) character

```php
$oPlayer = $oDestiny->searchPlayer('xgerhard');
$aCharacters = $oPlayer->characters->getCurrent();

/*
Destiny\Character Object
(
    [membershipId] => 4611686018467322796
    [membershipType] => 4
    [characterId] => 2305843009301408262
    [dateLastPlayed] => 2019-06-22T15:43:44Z
    [minutesPlayedThisSession] => 30
    ....
)
*/
```

**fetch()**

To fetch characters a list of components needs to be provided, by default it will load and fetch them all (inventory, progression, activities etc.).
To reduce the size of the `Destiny\CharacterCollection` object and probably speed up the code, you can manually provide these components.

Available components: <https://bungie-net.github.io/multi/schema_Destiny-DestinyComponentType.html#schema_Destiny-DestinyComponentType>

When `fetch()` is finished it will return all characters (function `getAll()`).

```php
$oPlayer = $oDestiny->searchPlayer('xgerhard');

// If I only need the characters data (for example: characterId, dateLastPlayed)
$aCharacters = $oPlayer->characters->fetch([200]);

// If I want to check characters inventory
$oPlayer->characters->fetch([
    200, // Character basic info
    205, // Character equipment
    300, // Item instances
    305 // Item sockets
]);

// Get primary weapon for all characters
foreach($oPlayer->characters->getAll() as $oCharacter)
{
    $oPrimary = $oCharacter->inventory->get('primary');
}

// Or just for the current character
$oPlayer->characters->getCurrent()->inventory->get('primary');
```
### Destiny\Character
#### InventoryCollection

Characters have an inventory property, use the 'get' function to request inventory/equipment items.
The first parameter is the identifier of the item, the second parameter is a boolean to include perk details.

```php
$oPlayer = $oDestiny->searchUser('xgerhard')->profiles->getCurrent();

// Fetch character data, including equipment, item instances, item sockets
$oCurrentCharacter = $oPlayer->characters->getCurrent([200, 205, 300, 305]);

$oPrimary = $oCurrentCharacter->inventory->get('primary', true));

/*
Destiny\EquipmentItem Object
(
    [itemInstanceId] => 6917529067574353832
    [itemHash] => 347366834
    [name] => Ace of Spades
    [bucketTypeHash] => 1498876634
    [light] => 750
    [quantity] => 1
    [perks] => Array
        (
            [0] => Memento Mori
            [1] => Corkscrew Rifling
            [2] => High-Caliber Rounds
            [3] => Firefly
            [4] => Smooth Grip
            [5] => Last Hand
            [6] => Empty Catalyst Socket
        )
)
*/

// $oPrimary = Destiny\EquipmentItem
```

The first parameter can be an array to request multiple items.

```php
$aItems = $oCurrentCharacter->inventory->get(['helmet', 'chest']);

/*
Array
(
    [helmet] => Destiny\EquipmentItem Object
        (
            [itemInstanceId] => 6917529088474208321
            [itemHash] => 2124666626
            [name] => Wing Discipline
            [bucketTypeHash] => 3448274439
            [light] => 750
            [quantity] => 1
        )
    [chest] => Destiny\EquipmentItem Object
        (
            [itemInstanceId] => 6917529085963882227
            [itemHash] => 2562470699
            [name] => Tangled Web Plate
            [bucketTypeHash] => 14239492
            [light] => 750
            [quantity] => 1
        )
)
*/
```

### Manifest
The manifest is a database containing information about items/activities etc. you can query the database by using the hash of the item.

**getManifest()**
```php
$oManifest = $oDestiny->getManifest();
$oItem = $oManifest->getDefinition('InventoryItem', 3588934839);

/*
stdClass Object
(
    [displayProperties] => stdClass Object
        (
            [description] => "Wings flutter. Beauty distracts. Poison injects. The butterfly's curse extends to your enemies. A short life, shortened further by your hand." —Ada-1
            [name] => Le Monarque
            [icon] => /common/destiny2_content/icons/4b66c583e88a316cc3b0cf017a3e9a7b.jpg
            [hasIcon] => 1
        )
    ....
*/

// $oManifest = Destiny\Manifest
```

**updateManifest()**

To keep the database up to date, with for example new items, you can use this function to check for updates.
If the response of the function is true, the manifest is either updated or already was up to date.

```php
var_dump($oDestiny->updateManifest());
/*
bool(true)
*/
```

### License
This Destiny API wrapper is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).