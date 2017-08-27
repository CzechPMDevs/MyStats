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
    public function updateMessage(string $message, Player $player):string {
        $message = str_replace("%name", $player->getName(), $message);
        $message = str_replace("%x", $player->getY(), $message);
        $message = str_replace("%y", $player->getY(), $message);
        $message = str_replace("%z", $player->getZ(), $message);
        $message = str_replace("%level", $player->getLevel()->getName(), $message);
        return $message;
    }

    public function onRun(int $currentTick) {

    }
}
