<?php

declare(strict_types=1);

/**
 * 1.4 Changelog:
 *
 * - Various bug fixes
 * - Added per-world format support
 * - Added popup format support
 * - Added %tps, %maxPlayers
 * - Api update
 * - Added support for api 3.0.0-ALHPA8
 */

/**
 * 1.4.4 Changelog:
 *
 * - Added support for api 3.0.0-ALPHA9
 */

/**
 * 1.4.5 Changelog:
 *
 * - Added support for api 3.0.0-ALPHA10
 */

/**
 * 1.4.6 Changelog
 *
 * - Various bug fixes
 * - Clean up
 * - Added factions support (%faction)
 * - More concise settings
 * - Added version to config
 * - new api (MyStats::getAPI() method)
 * - changed namespace to \mystats\
 * - new poggit icon
 * - Added ranks (PurePerms) support (%rank)
 */

namespace mystats;

use mystats\command\StatsCommand;
use mystats\economy\EconomyManager;
use mystats\event\EventListener;
use mystats\factions\FactionManager;
use mystats\ranks\RanksManager;
use mystats\task\SendStatsTask;
use mystats\utils\ConfigManager;
use mystats\utils\Data;
use mystats\utils\DataManager;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;

/**
 * Class mystats
 * @package mystats
 */
class MyStats extends PluginBase{

    const NAME = "MyStats";
    const VERSION = "1.4.7";
    const AUTHOR = "VixikCZ";
    const GITHUB = "https://github.com/CzechPMDevs/MyStats/";
    const RELEASE = true;
    const PX = "";

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
        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n".
                "§c--------------------------------\n".
                "§6§lCzechPMDevs §r§e>>> §bMyStatsd\n".
                "§o§9The most customizable HUD plugin.\n".
                "§aAuthors: §7VixikCZ\n".
                "§aVersion: §7".$this->getDescription()->getVersion()."\n".
                "§aStatus: §7Loading...\n".
                "§c--------------------------------");
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to §7".self::GITHUB."issues §6to fix it.");
        }
        self::$instance = $this;
        self::$pluginApi = new API;
        $this->registerCommands();
        $this->registerManagers();
        $this->registerListeners();
        $this->registerTasks();
        $this->check();
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
    public static function getPlayerData(Player $player):Data {
        return self::getInstance()->getDataManager()->getPlayerData($player);
    }

    /**
     * @return MyStats $instance
     */
    public static function getInstance() {
        return self::$instance;
    }

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

    public function check() {
        if($this->getDescription()->getVersion() != self::VERSION or $this->getDescription()->getName() != self::NAME) {
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->critical("Download plugin from github! (".self::GITHUB."releases)");
        }
        if(!self::RELEASE) {
            $this->getLogger()->notice("You are running non-stable version of mystats!");
            $this->getLogger()->notice("Please, download stable plugin from release (".self::GITHUB."releases)");
        }
    }

    public function registerListeners() {
        $this->listeners["EventListener"] = new EventListener($this);
    }

    public function registerTasks() {
        $this->tasks["SendStatsTask"] = new SendStatsTask($this);
        foreach ($this->tasks as $task) {
            if($task instanceof Task) {
                $this->getServer()->getScheduler()->scheduleRepeatingTask($task, 20);
            }
        }
    }

    public function registerCommands() {
        $this->commands["StatsCommand"] = new StatsCommand;
        foreach ($this->commands as $command) {
            if($command instanceof Command) {
                $this->getServer()->getCommandMap()->register("stats", $command);
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
