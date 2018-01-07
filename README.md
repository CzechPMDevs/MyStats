# MyStats

**_The most customizable HUD plugin_**

### MyStats supports:
 - MCPE 1.1 - 1.2
 - Api 3.0.0-ALPHA7, 3.0.0-ALPHA8, 3.0.0-ALPHA9, 3.0.0-ALPHA10
 - PocketMine, BlueLight

[![Poggit-CI](https://poggit.pmmp.io/ci.shield/CzechPMDevs/MyStats/MyStats)](https://poggit.pmmp.io/ci/CzechPMDevs/MyStats/MyStats)

### News:

- 1.4.6 Released:
    - Added FactionsPro and PurePerms support
    - Fixed various bugs
    - Namespace updated to \mystats\
    - Added new poggit icon
    - New API method (mystats\MyStats::getAPI())
    - More concise settings (config update)
    - Plugin clean-up

### Pictures:
![MyStats](https://image.ibb.co/eZjUGk/MyStats.png)

### Phar download:
- Version 1.4.6 (Stable):
    - [![](https://poggit.pmmp.io/shield.api/MyStats)](https://poggit.pmmp.io/p/MyStats/1.4.6)
- Version 1.4.5 (Stable):
    - [![](https://poggit.pmmp.io/shield.api/MyStats)](https://poggit.pmmp.io/p/MyStats)
- Version 1.4.4 (Stable):
    - Poggit: https://poggit.pmmp.io/p/MyStats/1.4.4
- Version 1.4.3 (Stable):
    - Poggit: https://poggit.pmmp.io/r/12936/MyStats_dev-75.phar
- Version 1.4.2 (Unstable):
    - GitHub: https://github.com/CzechPMDevs/MyStats/releases/1.4.2
    - Poggit: https://poggit.pmmp.io/p/MyStats/1.4.2
- Version 1.4.1(Unstable):
    - GitHub: https://github.com/CzechPMDevs/MyStats/releases/1.4.1
    - Poggit: https://poggit.pmmp.io/p/MyStats/1.4.1
- Version 1.4.0(Unstable):
    - GitHub: https://github.com/CzechPMDevs/MyStats/releases/1.4.0
    - Poggit: https://poggit.pmmp.io/p/MyStats/1.4.0
- Version Latest (Unstable):
    - Poggit: https://poggit.pmmp.io/ci/CzechPMDevs/MyStats/MyStats
- Version 1.3.0 (Unstable):
    - Poggit: https://poggit.pmmp.io/ci/CzechPMDevs/MyStats/~/dev:47
- Version v1.2.0 (Unstable):
    - GitHub: https://github.com/CzechPMDevs/MyStats/releases/tag/1.2.0
    
### API:

- Get player`s data
```php
public function getData(Player $player): \mystats\utils\Data {
    $api = \mystats\MyStats::getAPI();
    return $api->getPlayerData($player);
}
```

- Get player`s kills
```php
public function getData(Player $player): \mystats\utils\Data {
    $api = \mystats\MyStats::getAPI();
    return $api->getPlayerData($player);
}


public function getKills(Player $player): int {
    $data = $this->getData($player);
    return $data->getKills();
}
```

### Commands:

#### Stats command

- description: Displays your stats
- usage: /stats
- aliases:
    - ms
    - mystats
- permission: ms.cmd.stats

### Dependencies:

#### PurePerms:

- optional
- https://poggit.pmmp.io/p/PurePerms/1.4.1-3

#### FactionsPro:

- optional
- https://poggit.pmmp.io/p/FactionsPro/1.3.11-7

#### EconomyAPI:

- optional
- https://poggit.pmmp.io/p/EconomyAPI/5.7.1-4

### Images:
![MyStats](https://image.ibb.co/eZjUGk/MyStats.png)

### Format list:

| format | description |
| --- | --- |
| %name | player`s name |
| %level | player`s level |
| %x, %y, %z | player`s coords |
| %itemid | player`s item in hand id |
| %itemname | player`s item in hand name |
| %broken | broken blocks |
| %placed | placed blocks |
| %kills | kills |
| %deaths | deaths |
| %money | player`s money |
| %rank | player`s rank |
| %faction | player`s faction |
| %online | count online players |
| %version | server version |
| %ip | server adress |
| %port | server adress |
| %tps | server tps |

### Config:

- default config:

```yaml

---

config-version: '1.4.6'

prefix: '&5&l[ &r&2MyStats &l&5]'

economy: 'false'

factions: 'false'

ranks: 'false'

mainFormat:
  - '&5-- == &6[&eMyStats&6] &5== ---'
  - '&3Welcome: &b%name'
  - '&3You are playing on &b%level'
  - '&9- &3Kills: %kills'
  - '&9- &3Deaths: %deaths'

cmdFormat:
  - '&5--- == &6[&eMyStats&6] &5== ---'
  - '&9- &3Name: &b%name'
  - '&9- &3K/D: &b%kills / %deaths'
  - '&9- &3Broken Blocks: &b%broken'
  - '&9- &3Placed Blocks: &b%placed'
  - '&9- &3Joins: &b%joins'

filter: 'false'

defaultFormat: '1'

popupWorlds:
  - Lobby
  - Spawn
  - PlotMe

tipWorlds:
  - world
  - Hub
...

```

#### Enabling economy:

```yaml
# ---------------------------------------------------------------------------- #

##
### Economy
##

# Economy types: EconomyAPI
# To enable economy type Economy type
economy: 'EconomyAPI'

# ---------------------------------------------------------------------------- #
```

- If the EconomyAPI plugin is not found, the value of the money is set to 0.

#### Enanling ranks (PurePerms) or factions:

##### ranks (PurePerms):

```yaml
# ---------------------------------------------------------------------------- #


##
### PurePerms
##

# Enable pureperms
ranks: 'true'

# ---------------------------------------------------------------------------- #
```

- If the PurePerms plugin is not found, the value of the rank is set to ' '.

##### factions:

- Supported Factions plugins:
    - FactionsPro
    - FactionsProBeta

```yaml
# ---------------------------------------------------------------------------- #

##
### Factions
##

# Enable faction
factions: 'true'

# ---------------------------------------------------------------------------- #
```

- If the factions plugin is not found, the value of the fanction is set to ' '.