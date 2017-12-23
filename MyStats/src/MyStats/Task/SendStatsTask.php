<?php

namespace mystats\task;

use mystats\MyStats;
use mystats\utils\DataManager;

/**
 * Class SendStatsTask
 * @package mystats\task
 */
class SendStatsTask extends MyStatsTask  {

    /** @var  MyStats */
    public $plugin;

    /**
     * SendStatsTask constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $format = $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT);
        if(count($this->plugin->getServer()->getOnlinePlayers()) > 0) {
            if(is_array($tipWorlds = $this->getPlugin()->getDataManager()->getWorld(DataManager::TIP_WORLD))) {
                foreach ((array)$tipWorlds as $world) {
                    if($this->plugin->getServer()->isLevelGenerated($world) && $this->plugin->getServer()->isLevelLoaded($world)) {
                        foreach ($this->plugin->getServer()->getLevelByName($world)->getPlayers() as $worldPlayer) {
                            $worldPlayer->sendTip(str_repeat(" ",60).str_replace("%line", "\n".str_repeat(" ", 60),MyStats::getInstance()->translateMessage($worldPlayer, $format)));
                        }
                    }
                }
            }
            if(is_array($popupWorlds = $this->getPlugin()->getDataManager()->getWorld(DataManager::POPUP_WORLD))) {
                foreach ((array)$popupWorlds as $world) {
                    if($this->plugin->getServer()->isLevelGenerated($world) && $this->plugin->getServer()->isLevelLoaded($world)) {
                        foreach ($this->plugin->getServer()->getLevelByName($world)->getPlayers() as $worldPlayer) {
                            $worldPlayer->sendPopup(str_repeat("  ", 30).str_replace("%line", "\n".str_repeat("  ", 15),MyStats::getInstance()->translateMessage($worldPlayer, $format)));
                        }
                    }
                }
            }
        }
    }
}
