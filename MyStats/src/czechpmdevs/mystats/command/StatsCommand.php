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

namespace czechpmdevs\mystats\command;

use czechpmdevs\mystats\MyStats;
use czechpmdevs\mystats\utils\DataManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class StatsCommand
 * @package mystats\Command
 */
class StatsCommand extends Command implements PluginIdentifiableCommand {

    /**
     * StatsCommand constructor.
     */
    public function __construct() {
        parent::__construct("stats", "Displays your stats.", null, ["mystats", "ms"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!isset($args[0]) && ($sender instanceof Player) && $sender->hasPermission("ms.cmd.stats")) {
            foreach ($this->getPlugin()->getDataManager()->getFormat(DataManager::COMMAND_FORMAT) as $messages) {
                $sender->sendMessage($this->getPlugin()->translateMessage($sender, $messages));
            }
            return false;
        }
        else {
            if(($player = $this->getPlugin()->getServer()->getPlayer($args[0])) instanceof Player)  {
                foreach ($this->getPlugin()->getDataManager()->getFormat(DataManager::COMMAND_FORMAT) as $messages) {
                    $sender->sendMessage($this->getPlugin()->translateMessage($sender, $messages));
                }
            }
            else {
                $sender->sendMessage("§cPlayer was not found.");
            }
        }
    }

    /**
     * @return MyStats $plugin
     */
    public function getPlugin(): Plugin {
        return MyStats::getInstance();
    }
}
