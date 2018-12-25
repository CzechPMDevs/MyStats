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

namespace czechpmdevs\mystats\utils;

use czechpmdevs\mystats\MyStats;
use pocketmine\Player;

/**
 * Class DataManager
 * @package mystats\Util
 */
class DataManager {

    public const BROKEN = 0;
    public const PLACE = 1;
    public const KILL = 2;
    public const DEATH = 3;
    public const JOIN = 4;
    public const SKYWARS_WIN = 5;

    public const MAIN_FORMAT = 0;
    public const COMMAND_FORMAT = 1;

    public const SCOREBOARD_WORLD = 0;
    public const POPUP_WORLD = 1;
    public const TIP_WORLD = 2;

    public const DEFAULT_DATA = [
        "BrokenBlocks" => 0,
        "PlacedBlocks" => 0,
        "Kills" => 0,
        "Deaths" => 0,
        "Joins" => 0,
        "SkyWarsWins" => 0
    ];

    /** @var  Data[] $data */
    public $data = [];

    /** @var MyStats $plugin */
    public $plugin;

    /** @var array $configData */
    public $configData = [];

    /**
     * DataManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
        $this->loadData();
    }

    /**
     * @param int $mode
     * @return array $format
     */
    public function getFormat(int $mode):array {
        return $mode == self::MAIN_FORMAT ? $this->configData["mainFormat"] : $this->configData["cmdFormat"];
    }

    public function getWorld(int $mode):array  {
        return $mode == self::POPUP_WORLD ? $this->configData["popupWorlds"] : $this->configData["tipWorlds"];
    }

    /**
     * @param Player $player
     * @return array $configData
     */
    public function getConfigData(Player $player):array {
        return is_file(ConfigManager::getPlayerPath($player)) ? ConfigManager::getPlayerConfig($player)->getAll() : self::DEFAULT_DATA;
    }

    /**
     * @param Player $player
     * @return Data
     */
    public function createData(Player $player):Data {
        /** @var Data $data */
        $data = null;
        if(!is_file(ConfigManager::getPlayerPath($player))) {
            if(!isset($this->data[$player->getName()])) {
                $this->data[$player->getName()] = new Data($player, $this, $this->getConfigData($player));
                $data = $this->data[$player->getName()];
            }
            else {
                return $this->data[$player->getName()];
            }
        }
        else {
            $data = $this->data[$player->getName()] = new Data($player, $this, $this->getConfigData($player));
        }
        return $data;
    }

    /**
     * @param Player $player
     * @param int $id
     */
    public function add(Player $player, int $id) {
        if(isset($this->data[$player->getName()])) {
            $this->getPlayerData($player)->add($id);
        }
        else {
            $this->createData($player);
            $this->add($player, $id);
        }
    }

    public function loadData() {
        $this->configData = ConfigManager::getConfig()->getAll();
    }

    public function saveData() {
        foreach ($this->data as $data) {
            if($data instanceof Data) {
                $config = ConfigManager::getPlayerConfig($data->getPlayer());
                $config->setAll($data->getAll());
                $config->save();
            }
        }
    }

    /**
     * @param Player $player
     * @return Data $data
     */
    public function getPlayerData(Player $player):Data {
        return isset($this->data[$player->getName()]) ? $this->data[$player->getName()] : $this->createData($player);
    }

    /**
     * @return MyStats
     */
    public function getPlugin():MyStats {
        return $this->plugin;
    }
}