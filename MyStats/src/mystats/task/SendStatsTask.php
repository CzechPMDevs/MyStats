<?php

declare(strict_types=1);

namespace mystats\task;

use mystats\MyStats;
use mystats\utils\DataManager;
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
        return $this->getPlugin()->translateMessage($player, strval(str_repeat(" ", 60).implode("\n".str_repeat(" ", 60), $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT))));
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $dataMgr = $this->getPlugin()->getDataManager();

        if(!boolval($dataMgr->configData["filter"])) {
            if(intval($dataMgr->configData["defaultFormat"]) == 0) {
                $this->getPlugin()->getLogger()->info($dataMgr->configData["defaultFormat"]);
                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    $player->sendPopup($this->getFormat($player));
                }
                return;
            }
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                $player->sendTip($this->getFormat($player));
            }
            return;
        }

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
