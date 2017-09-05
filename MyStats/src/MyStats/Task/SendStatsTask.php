<?php

namespace MyStats\Task;

use MyStats\MyStats;
use pocketmine\Player;
use pocketmine\scheduler\Task;

/**
 * Class SendStatsTask
 * @package MyStats\Task
 */
class SendStatsTask extends Task  {

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
     * @param string $message
     * @param Player $player
     * @return string
     */
    public function translateMessage(string $message, Player $player):string {
        $data = $this->plugin->dataManager->getPlayerData($player);
        $message = str_replace("%name", $player->getName(), $message);
        $message = str_replace("%x", $player->getY(), $message);
        $message = str_replace("%y", $player->getY(), $message);
        $message = str_replace("%z", $player->getZ(), $message);
        $message = str_replace("%level", $player->getLevel()->getName(), $message);
        $message = str_replace("%broken", $data->getBrokenBlocks(), $message);
        $message = str_replace("%placed", $data->getPlacedBlocks(), $message);
        $message = str_replace("%kills", $data->getKills(), $message);
        $message = str_replace("%deaths", $data->getDeaths(), $message);
        $message = str_replace("%joins", $data->getJoins(), $message);
        $message = str_replace("%money", $data->getMoney(), $message);
        $message = str_replace("%online", $this->plugin->getServer()->getQueryInformation()->getPlayerCount(), $message);
        $message = str_replace("%ip", $this->plugin->getServer()->getIp(), $message);
        $message = str_replace("%port", $this->plugin->getServer()->getPort(), $message);
        $message = str_replace("%version", $this->plugin->getServer()->getVersion(), $message);
        $message = str_replace("%line", "\n".str_repeat(" ", 60), $message);
        $message = str_replace("&", "§", $message);

        return $message;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $format = $this->plugin->dataManager->mainFormat;
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if(count($this->plugin->getServer()->getOnlinePlayers()) > 0) {
                $format = $this->translateMessage($format, $player);
                $player->sendTip(str_repeat(" ", 60).$format);
            }
        }
    }
}
