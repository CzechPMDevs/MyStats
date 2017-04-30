<?php

namespace MyStats;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\Config;

class EventListener implements Listener {

    /** @var  MyStats */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $e) {
        $p = $e->getPlayer();
        if(!is_file($this->plugin->getDataFolder()."players/{$p->getName()}.yml")) {
            file_put_contents($this->plugin->getDataFolder()."players/{$p->getName()}.yml", $this->plugin->getResource("data.yml"));
        }
        else {
            $cfg = new Config($this->plugin->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
            $cfg->set("joins", $cfg->get("joins")+1);
            $cfg->save();
        }
    }

    public function onDeath(PlayerDeathEvent $e) {
        $en = $e->getEntity();
        if($en instanceof Player) {
            $player = $en;
            $pcfg = new Config($this->plugin->getDataFolder()."players/{$player->getName()}.yml", Config::YAML);
            $pcfg->set("deaths", $pcfg->get("deaths")+1);
            $pcfg->save();

            $dmg = $en->getLastDamageCause();
            if($dmg instanceof EntityDamageEvent && $dmg instanceof EntityDamageByEntityEvent){
                $d = $dmg->getDamager();
                if($d instanceof Player) {
                    $damager = $d;
                    $pcfg = new Config($this->plugin->getDataFolder()."players/{$damager->getName()}.yml", Config::YAML);
                    $pcfg->set("kills", $pcfg->get("kills")+1);
                    $pcfg->save();
                }
            }
        }
    }

    public function onPlace(BlockPlaceEvent $e) {
        if(!$e->isCancelled()) {
            $p = $e->getPlayer();
            $cfg = new Config($this->plugin->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
            $cfg->set("placed", $cfg->get("placed")+1);
            $cfg->save();
        }
    }

    public function onBreak(BlockBreakEvent $e) {
        $p = $e->getPlayer();
        $cfg = new Config($this->plugin->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
        $cfg->set("breaked", $cfg->get("breaked")+1);
        $cfg->save();
    }
}
