<?php

namespace MyStats;

use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use PocketMoney\PocketMoney;

class EconomyManager {

    /** @var MyStats*/
    public $plugin;

    public static $economy;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        self::$economy = $this->plugin->getConfig()->get("economy");
    }

    /**
     * @return EconomyAPI|PocketMoney
     */
    public function getEconomy() {
        if(self::$economy != "false") {
            switch (self::$economy) {
                case "EconomyAPI":
                    $eco = EconomyAPI::getInstance();
                    return $eco;
                case "PocketMoney":
                    $eco = new PocketMoney();
                    return $eco;
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
            case "PocketMoney":
                return intval($this->getEconomy()->getMoney($player->getName()));
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
