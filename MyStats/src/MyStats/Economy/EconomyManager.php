<?php

namespace MyStats\Economy;

use MyStats\MyStats;
use MyStats\Util\ConfigManager;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;

/**
 * Class EconomyManager
 * @package MyStats\Economy
 */
class EconomyManager {

    /** @var MyStats*/
    public $plugin;

    /** @var bool|string $economy */
    public static $economy;

    /**
     * EconomyManager constructor.
     * @param $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
        self::$economy = is_bool(ConfigManager::getConfig()->get("economy")) ? boolval(ConfigManager::getConfig()->get("economy")) : strval(ConfigManager::getConfig()->get("economy"));
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
                return intval($this->getEconomy()->myMoney($player));
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
