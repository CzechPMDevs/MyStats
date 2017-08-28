<?php

namespace MyStats\Util;

use MyStats\MyStats;
use MyStats\Task\SendStatsTask;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

/**
 * Class ConfigManager
 * @package MyStats\Util
 */
class ConfigManager {

    /** @var  string $prefix */
    static $prefix;

    /** @var  MyStats $plugin */
    public $plugin;

    /** @var  Config $config */
    public $config;

    /**
     * ConfigManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
    }

    public function init() {
        if(!is_dir(self::getDataFolder())) {
            @mkdir(self::getDataFolder());
        }
        if(!is_dir(self::getDataFolder()."players")) {
            @mkdir(self::getDataFolder()."players");
        }
        if(!is_file(self::getDataFolder()."/config.yml")) {
            MyStats::getInstance()->saveResource("/config.yml");
        }
        $this->config = $this->plugin->getConfig();
        self::$prefix = $this->config->get("prefix");
        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask($this->plugin->sendStatsTask = new SendStatsTask($this->plugin), intval($this->config->get("time"))*20);
    }

    /**
     * @return Config $config
     */
    public static function getConfig():Config {
        return MyStats::getInstance()->configManager->config;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix():string {
        return strval(self::$prefix);
    }

    /**
     * @return string $dataFolder
     */
    public static function getDataFolder():string {
        return MyStats::getInstance()->getDataFolder();
    }

    /**
     * @return string $dataPath
     */
    public static function getDataPath():string {
        return Server::getInstance()->getDataPath();
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getPlayerPath(Player $player):string {
        return MyStats::getInstance()->getDataFolder()."players/".strtolower($player->getName()).".yml";
    }

    /**
     * @param Player $player
     * @param array $data
     */
    public static function savePlayerData(Player $player, array $data) {
        unlink(self::getPlayerPath($player));
        $config = new Config(self::getPlayerPath($player),Config::YAML, $data);
        $config->save();
    }

    /**
     * @param Player $player
     * @return Config
     *
     * Nevím zda to funguje .-.
     */
    public static function getPlayerConfig(Player $player, bool $force = false):Config {
        return $force ? new Config(self::getPlayerPath($player), Config::YAML) : (file_exists(self::getPlayerPath($player)) ? new Config(self::getPlayerPath($player), Config::YAML) : null);
    }
}