<?php

declare(strict_types=1);

namespace mystats\factions;

use mystats\MyStats;
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