<?php

namespace MyStats\Util;

use MyStats\MyStats;
use pocketmine\Player;

/**
 * Class DataManager
 * @package MyStats\Util
 */
class DataManager {

    const BREAKED = 0;
    const BROKEN = 0;
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
        $this->loadData();
    }

    /**
     * @param Player $player
     * @return array $configData
     */
    public function getConfigData(Player $player):array {
        return file_exists(ConfigManager::getPlayerPath($player)) ? ConfigManager::getPlayerConfig($player)->getAll() : ["BrokenBlocks" => 0,
            "PlacedBlocks" => 0,
            "Kills" => 0,
            "Deaths" => 0,
            "Joins" => 0
        ];
    }

    /**
     * @param Player $player
     */
    public function createData(Player $player) {
        if(empty($this->data[strtolower($player->getName())])) {
            $this->data[strtolower($player->getName())] = new Data($player, $this, $this->getConfigData($player));
        }
        $data = $this->getConfigData($player);
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
        foreach($this->data as $data) {
            $config = ConfigManager::getPlayerConfig($data->player, true);
            $config->set("BrokenBlocks", $data->getBreakedBlocks());
            $config->set("PlacedBlocks", $data->getPlacedBlocks());
            $config->set("Kills", $data->getKills());
            $config->set("Deaths", $data->getDeaths());
            $config->set("Joins", $data->getJoins());
            $config->save();
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