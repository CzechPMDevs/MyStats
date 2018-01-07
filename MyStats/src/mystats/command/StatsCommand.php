<?php

namespace mystats\command;

use mystats\MyStats;
use mystats\utils\DataManager;
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
        if(empty($args[0]) && ($sender instanceof Player) && $sender->hasPermission("ms.cmd.stats")) {
            foreach ($this->getPlugin()->getDataManager()->getFormat(DataManager::COMMAND_FORMAT) as $messages) {
                $sender->sendMessage($this->getPlugin()->translateMessage($sender, $messages));
            }
            return false;
        }
        return false;
    }

    /**
     * @return MyStats $plugin
     * @return Plugin
     */
    public function getPlugin(): Plugin {
        return MyStats::getInstance();
    }
}
