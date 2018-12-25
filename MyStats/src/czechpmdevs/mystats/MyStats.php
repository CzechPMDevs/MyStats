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

namespace czechpmdevs\mystats;

use czechpmdevs\mystats\command\StatsCommand;
use czechpmdevs\mystats\economy\EconomyManager;
use czechpmdevs\mystats\event\EventListener;
use czechpmdevs\mystats\factions\FactionManager;
use czechpmdevs\mystats\ranks\RanksManager;
use czechpmdevs\mystats\task\SendStatsTask;
use czechpmdevs\mystats\utils\ConfigManager;
use czechpmdevs\mystats\utils\Data;
use czechpmdevs\mystats\utils\DataManager;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;

/**
 * Class mystats
 * @package mystats
 */
class MyStats extends PluginBase{

    /** @var  MyStats $instance */
    private static $instance;

    /** @var  API $pluginApi */
    private static $pluginApi;

    /** @var  array $managers */
    public $managers;

    /** @var  array $commands */
    public $commands;

    /** @var  array $tasks */
    public $tasks;

    /** @var array $listeners */
    public $listeners;

    public function onEnable() {
        self::$instance = $this;
        self::$pluginApi = new API;
        $this->registerCommands();
        $this->registerManagers();
        $this->registerListeners();
        $this->registerTasks();
    }

    public function onDisable() {
        $this->getDataManager()->saveData();
    }

    /**
     * @param Player $player
     * @return Data
     *
     * API function
     */
    public static function getPlayerData(Player $player): Data {
        return self::getInstance()->getDataManager()->getPlayerData($player);
    }

    /**
     * @return MyStats $instance
     */
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * @return API $api
     */
    public static function getAPI() {
        return self::$pluginApi;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix() {
        return ConfigManager::getPrefix();
    }

    /**
     * @param Player $player
     * @param string $message
     * @return string
     */
    public function translateMessage(Player $player, string $message):string {
        return ConfigManager::translateMessage($player, $message);
    }

    public function registerListeners() {
        $this->listeners["EventListener"] = new EventListener($this);
    }

    public function registerTasks() {
        $this->tasks["SendStatsTask"] = new SendStatsTask();
        foreach ($this->tasks as $task) {
            if($task instanceof Task) {
                $this->getScheduler()->scheduleRepeatingTask($task, 20);
            }
        }
    }

    public function registerCommands() {
        $this->commands["StatsCommand"] = new StatsCommand;
        foreach ($this->commands as $command) {
            if($command instanceof Command) {
                $this->getServer()->getCommandMap()->register("MyStats", $command);
            }
        }
    }

    public function registerManagers() {
        $this->managers["ConfigManager"] = new ConfigManager($this);
        $this->managers["EconomyManager"] = new EconomyManager($this);
        $this->managers["DataManager"] = new DataManager($this);
        $this->managers["FactionManager"] = new FactionManager($this);
        $this->managers["RanksManager"] = new RanksManager($this);
    }

    /**
     * @return RanksManager
     */
    public function getRanksManager() {
        return $this->managers["RanksManager"];
    }

    /**
     * @return FactionManager
     */
    public function getFactionManager() {
        return $this->managers["FactionManager"];
    }

    /**
     * @return DataManager
     */
    public function getDataManager() {
        return $this->managers["DataManager"];
    }

    /**
     * @return EconomyManager
     */
    public function getEconomyManager() {
        return $this->managers["EconomyManager"];
    }

    /**
     * @return ConfigManager
     */
    public function getConfigManager() {
        return $this->managers["ConfigManager"];
    }
}
