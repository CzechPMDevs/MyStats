<?php

namespace MyStats\Util;

use pocketmine\Player;

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
        var_dump($configData);
    }

    public function getFormat() {

    }

    public function getBreakedBlocks() {
        return isset($this->data["BreakedBlocks"]) ? intval($this->data["BreakedBlocks"]) : intval(0);
    }

    public function getPlacedBlocks() {

    }

    public function getKills() {

    }

    public function getDeaths() {

    }

    public function getJoins() {

    }

    public function getMoney() {
        return $this->dataManager->plugin->economyManager->getPlayerMoney($this->player);
    }
}