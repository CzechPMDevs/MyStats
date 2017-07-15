<?php

namespace MyStats\Task;

use MyStats\MyStats;
use pocketmine\scheduler\PluginTask;

class MyStatsTask extends PluginTask  {

    /** @var  MyStats */
    public $plugin;

    public function __construct($plugin) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick) {
        $cfg = $this->plugin->getConfig();
        $levels = $cfg->get("levels");
        foreach ($levels as $level) {
            if ($this->plugin->getServer()->isLevelGenerated($level)) {
                $this->plugin->getServer()->loadLevel($level);
                foreach ($this->plugin->getServer()->getLevelByName($level)->getPlayers() as $p) {
                    $msg = $this->plugin->getStats($p, 1);
                    $p->sendTip("                                                             ".$msg);
                }
            }
        }
    }
}
