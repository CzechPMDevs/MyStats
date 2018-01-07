<?php

declare(strict_types=1);

namespace mystats\ranks;

use mystats\MyStats;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class RanksManager
 * @package mystats\ranks
 */
class RanksManager {

    /** @var MyStats $plugin */
    public $plugin;

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
        if($ranksPlugin instanceof Plugin && $ranksPlugin->isEnabled()) {
            $this->ranksPlugin = $ranksPlugin;
        }
        else {
            $this->ranksPlugin = false;
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