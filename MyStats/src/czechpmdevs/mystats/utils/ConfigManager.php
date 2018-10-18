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
use pocketmine\Server;
use pocketmine\utils\Config;

/**
 * Class ConfigManager
 * @package mystats\utils
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
        $this->init();
    }

    public function init() {
        if (!is_dir(self::getDataFolder())) {
            @mkdir(self::getDataFolder());
        }
        if (!is_dir(self::getDataFolder() . "players")) {
            @mkdir(self::getDataFolder() . "players");
        }
        if (is_file(self::getDataFolder() . "/config.yml.old")) {
            @unlink(self::getDataFolder() . "/config.yml.old");
        }
        if (!is_file(self::getDataFolder() . "/config.yml")) {
            $this->plugin->saveResource("/config.yml");
        } else {
            if (!$this->plugin->getConfig()->exists("config-version")) {
                rename(self::getDataFolder() . "/config.yml", self::getDataFolder() . "/config.yml.old");
                $this->plugin->saveResource("/config.yml");
                $this->plugin->getLogger()->info("Config updated! Old config can be found at " . self::getDataFolder() . "/config.yml.old");
            } elseif ($this->plugin->getConfig()->get("config-version") != "1.5.0") {
                rename(self::getDataFolder() . "/config.yml", self::getDataFolder() . "/config.yml.old");
                $this->plugin->saveResource("/config.yml");
                $this->plugin->getLogger()->info("Config updated! Old config can be found at " . self::getDataFolder() . "/config.yml.old");
            }
        }
        $this->config = new Config(self::getDataFolder() . "/config.yml", Config::YAML);
        self::$prefix = $this->config->get("prefix");
    }

    /**
     * @param Player $player
     * @param string $message
     * @return string $message
     */
    public static function translateMessage(Player $player, string $message):string {
        $data = MyStats::getInstance()->getDataManager()->getPlayerData($player);
        $message = str_replace("%name", $player->getName(), $message);
        $message = str_replace("%x", $player->getX(), $message);
        $message = str_replace("%y", $player->getY(), $message);
        $message = str_replace("%z", $player->getZ(), $message);
        $message = str_replace("%level", $player->getLevel()->getName(), $message);
        $message = str_replace("%broken", $data->getBrokenBlocks(), $message);
        $message = str_replace("%placed", $data->getPlacedBlocks(), $message);
        $message = str_replace("%kills", $data->getKills(), $message);
        $message = str_replace("%deaths", $data->getDeaths(), $message);
        $message = str_replace("%joins", $data->getJoins(), $message);
        $message = str_replace("%money", $data->getMoney(), $message);
        $message = str_replace("%faction", $data->getFaction(), $message);
        $message = str_replace("%rank", $data->getRank(), $message);
        $message = str_replace("%itemid", $player->getInventory()->getItemInHand()->getId(), $message);
        $message = str_replace("%itemname", $player->getInventory()->getItemInHand()->getName(), $message);
        $message = str_replace("%online", Server::getInstance()->getQueryInformation()->getPlayerCount(), $message);
        $message = str_replace("%max", Server::getInstance()->getQueryInformation()->getMaxPlayerCount(), $message);
        $message = str_replace("%ip", Server::getInstance()->getIp(), $message);
        $message = str_replace("%port", Server::getInstance()->getPort(), $message);
        $message = str_replace("%version", Server::getInstance()->getVersion(), $message);
        $message = str_replace("%line", "\n", $message);
        $message = str_replace("&", "§", $message);
        $message = str_replace("%tps", Server::getInstance()->getTicksPerSecond(), $message);
        $message = str_replace("%ping", $player->getPing(), $message);
        return $message;
    }

    /**
     * @return Config $config
     */
    public static function getConfig(): Config {
        return MyStats::getInstance()->getConfigManager()->config;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix(): string {
        return strval(self::$prefix);
    }

    /**
     * @return string $dataFolder
     */
    public static function getDataFolder(): string {
        return MyStats::getInstance()->getDataFolder();
    }

    /**
     * @return string $dataPath
     */
    public static function getDataPath(): string {
        return Server::getInstance()->getDataPath();
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getPlayerPath(Player $player): string {
        return MyStats::getInstance()->getDataFolder()."players/".$player->getName().".yml";
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
     */
    public static function getPlayerConfig(Player $player): Config {
        return new Config(self::getPlayerPath($player), Config::YAML);
    }
}
