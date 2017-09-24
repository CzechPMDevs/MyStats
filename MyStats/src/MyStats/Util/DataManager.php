<?php

namespace MyStats\Util;

use MyStats\MyStats;
use pocketmine\Player;

/**
 * Class DataManager
 * @package MyStats\Util
 */
class DataManager {

    const BROKEN = 0;
    const PLACE = 1;
    const KILL = 2;
    const DEATH = 3;
    const JOIN = 4;
    const MAIN_FORMAT = 0;
    const COMMAND_FORMAT = 1;
    const POPUP_WORLD = 0;
    const TIP_WORLD = 1;

    /** @var  Data[] $data */
    public $data;

    /** @var MyStats $plugin */
    public $plugin;

    /** @var  string|mixed $mainFormat */
    public $mainFormat, $cmdFormat;

    /** @var  array */
    public $popupWorlds, $tipWorlds;

    /**
     * DataManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
        $this->loadData();
    }

    /**
     * @param int $mode
     * @return string $format
     */
    public function getFormat(int $mode):string {
        return $mode == self::MAIN_FORMAT ? $this->mainFormat : $this->cmdFormat;
    }

    public function getWorld(int $mode):array  {
        return $mode == self::POPUP_WORLD ? $this->popupWorlds : $this->tipWorlds;
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
     * @return Data
     */
    public function createData(Player $player):Data {
        if(empty($this->data[strtolower($player->getName())])) {
            $this->data[strtolower($player->getName())] = new Data($player, $this, $this->getConfigData($player));
        }
        return $this->data[strtolower($player->getName())];
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
        $this->popupWorlds = (array)$config->get("popupWorlds");
        $this->tipWorlds = (array)$config->get("tipWorlds");
    }

    public function saveData() {
        foreach ($this->data as $data) {
            if($data instanceof Data) {
                $config = ConfigManager::getPlayerConfig($data->getPlayer(), true);
                $config->set("BrokenBlocks", $data->getBrokenBlocks());
                $config->set("PlacedBlocks", $data->getPlacedBlocks());
                $config->set("Kills", $data->getKills());
                $config->set("Deaths", $data->getDeaths());
                $config->set("Joins", $data->getJoins());
                $config->save();
            }
        }
    }

    /**
     * @param Player $player
     * @return Data $data
     */
    public function getPlayerData(Player $player):Data {
        return isset($this->data[strtolower($player->getName())]) ? $this->data[strtolower($player->getName())] : $this->createData($player);
    }

    /**
     * @return MyStats
     */
    public function getPlugin():MyStats {
        return $this->plugin;
    }
}