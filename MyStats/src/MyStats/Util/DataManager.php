<?php

namespace MyStats\Util;

use MyStats\MyStats;
use pocketmine\Player;

/**
 * Class DataManager
 * @package MyStats\Util
 */
class DataManager {

    const PLACE = 0;
    const BREAKED = 1;
    const KILL = 2;
    const DEATH = 3;
    const JOIN = 4;

    /** @var  array $data */
    public $data;

    /**
     * DataManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {

    }

    /**
     * @param Player $player
     * @param int $data
     */
    public function add(Player $player, int $data) {
        $this->data[strtolower($player->getName())][$data] = $this->data[strtolower($player->getName())][$data]+1;
    }

    /**
     * @param Player $player
     */
    public function checkJoin(Player $player) {
        if(isset($this->data[strtolower($player->getName())])) {

        }
    }
}