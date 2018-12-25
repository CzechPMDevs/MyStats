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

use pocketmine\Player;

/**
 * Class Data
 * @package mystats\Util
 */
class Data {

    /** @var  DataManager $dataManager */
    public $dataManager;

    /** @var  Player $player */
    public $player;

    /** @var  array $configData */
    public $configData;

    /** @var array $data */
    private $data = [];

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
            case DataManager::SKYWARS_WIN:
                $this->addSWWin();
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

    public function addSWWin() {
        isset($this->data["SkyWarsWins"]) ? $this->data["SkyWarsWins"] = $this->data["SkyWarsWins"]+1 : $this->data["SkyWarsWins"] = 1;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return (($player = $this->player) instanceof Player) ? $player : null;
    }

    /**
     * @return DataManager
     */
    public function getDataManager(): DataManager {
        return $this->dataManager;
    }

    /**
     * @return int
     */
    public function getBrokenBlocks(): int {
        return isset($this->data["BrokenBlocks"]) ? (int)$this->data["BrokenBlocks"] : 0;
    }

    /**
     * @return int
     */
    public function getPlacedBlocks(): int {
        return isset($this->data["PlacedBlocks"]) ? (int)$this->data["PlacedBlocks"] : 0;
    }

    /**
     * @return int
     */
    public function getKills(): int {
        return isset($this->data["Kills"]) ? (int)$this->data["Kills"] : 0;
    }

    /**
     * @return int
     */
    public function getDeaths(): int {
        return isset($this->data["Deaths"]) ? (int)$this->data["Deaths"] : 0;
    }

    /**
     * @return int
     */
    public function getJoins(): int {
        return isset($this->data["Joins"]) ? (int)$this->data["Joins"] : 1;
    }

    public function getSkyWarsWins(): int {
        return isset($this->data["SkyWarsWins"]) ? (int)$this->data["SkyWarsWins"] : 1;
    }

    /**
     * @return int
     */
    public function getMoney(): int {
        return intval($this->getDataManager()->getPlugin()->getEconomyManager()->getPlayerMoney($this->getPlayer()));
    }

    /**
     * @return string
     */
    public function getFaction(): string {
        return strval($this->getDataManager()->getPlugin()->getFactionManager()->getFaction($this->getPlayer()));
    }

    /**
     * @return string
     */
    public function getRank(): string {
        return strval($this->getDataManager()->getPlugin()->getRanksManager()->getRank($this->getPlayer()));
    }

    /**
     * @return array
     */
    public function getAll(): array {
        return $this->data;
    }
}