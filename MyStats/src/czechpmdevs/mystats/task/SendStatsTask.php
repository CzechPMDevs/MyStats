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

namespace czechpmdevs\mystats\task;

use czechpmdevs\mystats\utils\DataManager;
use pocketmine\Player;

/**
 * Class SendStatsTask
 * @package mystats\task
 */
class SendStatsTask extends MyStatsTask  {

    /**
     * @param Player $player
     * @return string
     */
    private function getFormat(Player $player) {
        return $this->getPlugin()->translateMessage($player, "\n". (string)(str_repeat(" ", 60).implode("\n".str_repeat(" ", 60), $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT))));
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $dataMgr = $this->getPlugin()->getDataManager();

        if((string)($dataMgr->configData["filter"]) == 'false') {
            if((string)($dataMgr->configData["defaultFormat"]) == '1') {
                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    $player->sendPopup($this->getFormat($player));
                }
            }
            elseif((string)($dataMgr->configData["defaultFormat"]) == '2') {
                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    $player->sendTip($this->getFormat($player));
                }
            }
        }
        else {
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                if(in_array($player->getLevel()->getName(), $dataMgr->configData["popupWorlds"])) {
                    $player->sendPopup($this->getFormat($player));
                }
                if(in_array($player->getLevel()->getName(), $dataMgr->configData["tipWorlds"])) {
                    $player->sendTip($this->getFormat($player));
                }
            }
        }
    }
}
