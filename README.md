# Destiny2 PHP API wrapper


A PHP Client/API wrapper for the Destiny 2 API

## Installation

Run the following command:

```shell
composer require xgerhard/destiny-api-wrapper
```

## Usage

### DestinyClient

**DestinyClient**

Get your API-key here: <https://bungie.net/en/application>

```php
$oDestiny = new Destiny\Client('Bungie-API-key-here');
```

**searchPlayer**

```php
use Destiny\Client;
use Destiny\Exceptions\PlayerNotFoundException;

try
{
	$oDestiny = new Destiny\Client('Bungie-API-key-here');
    $oPlayer = $oDestiny->searchPlayer('xgerhard#21555');

    echo $oPlayer->membershipId; // 4611686018467322796
    echo $oPlayer->membershipType; // 4
    echo $oPlayer->displayName; // xgerhard#21555

    // Optional provide the platform (membershipType) to search on:
    $oPlayer = $oDestiny->searchPlayer('xgerhard#21555', 4);

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
$oPlayer = $oDestiny->searchPlayer('xgerhard#21555');
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
$oPlayer = $oDestiny->searchPlayer('xgerhard#21555');
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
$oPlayer = $oDestiny->searchPlayer('xgerhard#21555');

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
    $oPrimary = $oCharacter->inventory->getPrimary();
}

// Or just for the current character
$oPlayer->characters->getCurrent()->inventory->getPrimary();
```

## License

This Destiny API wrapper is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).