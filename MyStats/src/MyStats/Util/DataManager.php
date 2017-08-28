<?php

namespace MyStats\Util;

use MyStats\MyStats;
use pocketmine\Player;
use pocketmine\utils\Config;

/**
 * Class DataManager
 * @package MyStats\Util
 */
class DataManager {

    const BREAKED = 0;
    const PLACE = 1;
    const KILL = 2;
    const DEATH = 3;
    const JOIN = 4;

    /** @var  Data[] $data */
    public $data;

    /** @var MyStats $plugin */
    public $plugin;

    /** @var  string|mixed $mainFormat */
    public $mainFormat, $cmdFormat;

    /**
     * DataManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $player
     * @return array $configData
     */
    public function getConfigData(Player $player):array {
        return file_exists(ConfigManager::getPlayerPath($player)) ? ConfigManager::getPlayerConfig($player)->getAll() : ["BreakedBlocks" => 0,
            "PlacedBlocks" => 0,
            "Kills" => 0,
            "Deaths" => 0,
            "Joins" => 1
        ];
    }

    /**
     * @param Player $player
     */
    public function createData(Player $player) {
        if(empty($this->data[strtolower($player->getName())])) {
            $this->data[strtolower($player->getName())] = new Data($player, $this, $this->getConfigData($player));
        }
    }

    /**
     * @param Player $player
     * @param int $id
     */
    public function add(Player $player, int $id) {
        $data = $this->data[strtolower($player->getName())];
        $data->add($id);
    }

    public function loadData() {
        $config = ConfigManager::getConfig();
        $this->mainFormat = strval($config->get("mainFormat"));
        $this->cmdFormat = strval($config->get("cmdFormat"));
    }

    public function saveData() {
        for ($x = count($this->data); $x > 0; $x--) {
            $data = $this->data[intval($x)-1];
        }
    }

    /**
     * @param Player $player
     * @return Data $data
     */
    public function getPlayerData(Player $player):Data {
        return $this->data[strtolower($player->getName())];
    }
}