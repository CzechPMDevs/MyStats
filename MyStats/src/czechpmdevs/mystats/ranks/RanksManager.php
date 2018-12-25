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

namespace czechpmdevs\mystats\ranks;

use czechpmdevs\mystats\MyStats;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

/**
 * Class RanksManager
 * @package mystats\ranks
 */
class RanksManager {

    /** @var MyStats $plugin */
    public $plugin;

    /** @var PluginBase $ranksPlugin */
    private $ranksPlugin;

    /**
     * PurePermsManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
        $this->loadRanks();
    }

    public function loadRanks() {
        if(!boolval($this->plugin->getDataManager()->configData["ranks"])) return;
        $ranksPlugin = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
        if(!$ranksPlugin) {
            $this->ranksPlugin = false;
        }
        else {
            $this->ranksPlugin = $ranksPlugin;
        }
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getRank(Player $player) {
        if(!$this->ranksPlugin) return "";
        return $this->ranksPlugin->getUserDataMgr()->getGroup($player, null);
    }
}