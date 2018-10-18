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

namespace czechpmdevs\mystats\factions;

use czechpmdevs\mystats\MyStats;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class FactionManager
 * @package mystats\factions
 */
class FactionManager {

    /** @var  MyStats */
    public $plugin;

    private $factions;

    /**
     * FactionManager constructor.
     * @param MyStats $plugin
     */
    public function __construct(MyStats $plugin) {
        $this->plugin = $plugin;
        $this->loadFactions();
    }

    private function loadFactions($plugin = "FactionsPro") {
        if(!boolval($this->plugin->getDataManager()->configData["factions"])) return;
        $factions = $this->plugin->getServer()->getPluginManager()->getPlugin($plugin);
        if(!$factions) {
            // BETA FACTIONS
            if ($plugin == "FactionsPro") {
                $this->loadFactions("FactionsProBeta");
            } else {
                $this->factions = false;
            }
        }
        else {
            $this->factions = $factions;
        }
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getFaction(Player $player) {
        if(!$this->factions) {
            return "";
        }
        return $this->factions->getPlayerFaction($player->getName());
    }
}