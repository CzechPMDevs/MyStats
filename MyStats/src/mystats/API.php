<?php

declare(strict_types=1);

namespace mystats;

use pocketmine\Player;

/**
 * Class API
 * @package mystats
 */
class API {

    /**
     * @return MyStats
     */
    private function getPlugin() {
        return MyStats::getInstance();
    }

    /**
     * @param Player $player
     * @return utils\Data
     */
    public function getPlayerData(Player $player) {
        return $this->getPlugin()->getDataManager()->getPlayerData($player);
    }
}