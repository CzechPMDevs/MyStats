<?php

namespace mystats\task;

use mystats\MyStats;
use pocketmine\scheduler\Task;

/**
 * Class MyStatsTask
 * @package mystats\task
 */
abstract class MyStatsTask extends Task {

    /**
     * @return MyStats
     */
    public function getPlugin():MyStats {
        return MyStats::getInstance();
    }
}
