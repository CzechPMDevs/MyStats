<?php

/**
 *  Copyright (C) 2018  CzechPMDevs
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


declare(strict_types=1);

namespace czechpmdevs\mystats\economy;

use czechpmdevs\mystats\MyStats;
use czechpmdevs\mystats\utils\ConfigManager;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;

/**
 * Class EconomyManager
 * @package mystats\Economy
 */
class EconomyManager {

    /** @var MyStats*/
    public $plugin;

    /** @var bool|string $economy */
    public static $economy = "EconomyAPI";

    /**
     * EconomyManager constructor.
     * @param $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
        self::$economy = is_bool(ConfigManager::getConfig()->get("economy")) ? (bool)(ConfigManager::getConfig()->get("economy")) : (string)(ConfigManager::getConfig()->get("economy"));
    }

    /**
     * @return EconomyAPI
     */
    public function getEconomy() {
        if(self::$economy != false) {
            switch (self::$economy) {
                case "EconomyAPI":
                    $eco = EconomyAPI::getInstance();
                    return $eco;
                default:
                    break;
            }
        }
    }
    /**
     * @param Player $player
     * @return int|string
     */
    public function getPlayerMoney(Player $player) {
        switch (self::$economy) {
            case "false":
                return "0";
            case "EconomyAPI":
                return (int)($this->getEconomy()->myMoney($player));
            default:
                return "0";
        }
    }
    /**
     * @param string $economy
     */
    public function setEconomy($economy) {
        $this->plugin->getConfig()->set("economy", $economy);
        $this->plugin->getConfig()->save();
        self::$economy = $economy;
    }
}
