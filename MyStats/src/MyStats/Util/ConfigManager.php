<?php

namespace MyStats\Util;

use MyStats\MyStats;
use MyStats\Task\SendStatsTask;
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
        if(is_dir(self::getDataFolder())) {
            @mkdir(self::getDataFolder());
        }
        if(is_file(self::getDataFolder()."/config.yml")) {
            $this->plugin->saveResource("/config.yml");
        }
        $this->config = $this->plugin->getConfig();
        self::$prefix = $this->config->get("prefix");
        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask($this->plugin->sendStatsTask = new SendStatsTask($this->plugin), intval($this->config->get("time"))*20);
    }

    /**
     * @return Config $config
     */
    public function getConfig():Config {
        return $this->config;
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
        return Server::getInstance();
    }
}