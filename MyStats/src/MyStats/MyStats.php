<?php

/**
 * 1.4 Changelog:
 *
 * - Various bug fixes
 * - Added per-world format support
 * - Added popup format support
 * - Added %tps, %maxPlayers
 * - Breaked -> Broken fix
 * - Api update
 * - Added support for api 3.0.0-ALHPA8
 */

namespace MyStats;

use MyStats\Command\StatsCommand;
use MyStats\Economy\EconomyManager;
use MyStats\Event\EventListener;
use MyStats\Task\SendStatsTask;
use MyStats\Util\ConfigManager;
use MyStats\Util\Data;
use MyStats\Util\DataManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;

/**
 * Class MyStats
 * @package MyStats
 */
class MyStats extends PluginBase{

    const NAME = "MyStats";
    const VERSION = "1.4.4";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/MyStats/";
    const RELEASE = true;

    /** @var  MyStats $instance */
    static $instance;

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
        $this->registerCommands();
        $this->registerManagers();
        $this->registerListeners();
        $this->registerTasks();
        $this->check();

        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n§5**********************************************\n".
                "§6 ---- == §c[§aMyStats§c]§6== ----\n".
                "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                "§9> Author: §eCzechPMDevs :: GamakCZ\n".
                "§9> GitHub: §e".self::GITHUB."\n".
                "§9> Package: §e{$phar}\n".
                "§9> Language: §eEnglish\n".
                "§5**********************************************");
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to §7".self::GITHUB."issues §6to fix it.");
        }
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
            $this->getLogger()->notice("You are running non-stable version of MyStats!");
            $this->getLogger()->notice("Please, download stable plugin from release (".self::GITHUB."/releases)");
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
