<?php

namespace MyStats\Util;

use pocketmine\Player;

/**
 * Class Data
 * @package MyStats\Util
 */
class Data {

    /** @var  DataManager $dataManager */
    public $dataManager;

    /** @var  Player $player */
    public $player;

    /** @var  array $configData */
    public $configData;

    /** @var array $data */
    public $data = [];

    /**
     * Data constructor.
     * @param Player $player
     * @param DataManager $dataManager
     * @param array $configData
     */
    public function __construct(Player $player, DataManager $dataManager, array $configData) {
        $this->player = $player;
        $this->dataManager = $dataManager;
        $this->configData = $configData;
        if(is_array($configData) && count($configData) == 5) {
            $this->data = $configData;
        }
    }

    /**
     * @param int $id
     */
    public function add(int $id) {
        switch ($id) {
            case DataManager::BROKEN:
                $this->addBrokenBlock();
                break;
            case DataManager::PLACE:
                $this->addPlacedBlock();
                break;
            case DataManager::KILL:
                $this->addKill();
                break;
            case DataManager::DEATH:
                $this->addDeath();
                break;
            case DataManager::JOIN:
                $this->addJoin();
                break;
        }
    }

    public function addBrokenBlock() {
        isset($this->data["BrokenBlocks"]) ? $this->data["BrokenBlocks"] = $this->data["BrokenBlocks"]+1 : $this->data["BrokenBlocks"] = 1;
    }

    public function addPlacedBlock() {
        isset($this->data["PlacedBlocks"]) ? $this->data["PlacedBlocks"] = $this->data["PlacedBlocks"]+1 : $this->data["PlacedBlocks"] = 1;
    }

    public function addKill() {
        isset($this->data["Kills"]) ? $this->data["Kills"] = $this->data["Kills"]+1 : $this->data["Kills"] = 1;
    }

    public function addDeath() {
        isset($this->data["Deaths"]) ? $this->data["Deaths"] = $this->data["Deaths"]+1 : $this->data["Deaths"] = 1;
    }

    public function addJoin() {
        isset($this->data["Joins"]) ? $this->data["Joins"] = $this->data["Joins"]+1 : $this->data["Joins"] = 1;
    }

    /**
     * @return int
     */
    public function getBrokedBlocks():int {
        return isset($this->data["BreakedBlocks"]) ? intval($this->data["BreakedBlocks"]) : intval(0);
    }

    /**
     * @return int
     */
    public function getPlacedBlocks():int {
        return isset($this->data["PlacedBlocks"]) ? intval($this->data["PlacedBlocks"]) : intval(0);
    }

    /**
     * @return int
     */
    public function getKills():int {
        return isset($this->data["Kills"]) ? intval($this->data["Kills"]) : intval(0);
    }

    /**
     * @return int
     */
    public function getDeaths():int {
        return isset($this->data["Deaths"]) ? intval($this->data["Deaths"]) : intval(0);
    }

    /**
     * @return int
     */
    public function getJoins():int {
        return isset($this->data["Joins"]) ? intval($this->data["Joins"]) : intval(1);
    }

    /**
     * @return int
     */
    public function getMoney():int {
        return intval($this->dataManager->plugin->economyManager->getPlayerMoney($this->player));
    }
}