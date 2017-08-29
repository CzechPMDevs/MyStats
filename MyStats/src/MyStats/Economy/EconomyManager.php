<?php

namespace MyStats\Economy;

use MyStats\MyStats;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use PocketMoney\PocketMoney;

class EconomyManager {

    /** @var MyStats*/
    public $plugin;

    /** @var bool|string $economy */
    public static $economy;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        self::$economy = is_bool($this->plugin->getConfig()->get("economy")) ? boolval($this->plugin->getConfig()->get("economy")) : strval($this->plugin->getConfig()->get("economy"));
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
