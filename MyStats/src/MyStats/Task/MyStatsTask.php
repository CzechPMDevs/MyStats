<?php

namespace MyStats\Task;

use MyStats\MyStats;
use pocketmine\scheduler\Task;

/**
 * Class MyStatsTask
 * @package MyStats\Task
 */
abstract class MyStatsTask extends Task {

    /**
     * @return MyStats
     */
    public function getPlugin():MyStats {
        return MyStats::getInstance();
    }
}