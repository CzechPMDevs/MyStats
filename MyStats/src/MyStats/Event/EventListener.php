<?php

namespace MyStats\Event;

use MyStats\MyStats;
use MyStats\Util\DataManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Player;

/**
 * Class EventListener
 * @package MyStats\Event
 */
class EventListener implements Listener {

    /** @var  MyStats */
    public $plugin;

    /**
     * EventListener constructor.
     * @param $plugin
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if(!$event->isCancelled()) {
            $this->plugin->dataManager->add($player, DataManager::BREAKED);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        if(!$event->isCancelled()) {
            $this->plugin->dataManager->add($player, DataManager::PLACE);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event) {
        $entity = $event->getEntity();
        $lastDamageCause = $entity->getLastDamageCause();
        if($entity instanceof Player && $lastDamageCause instanceof EntityDamageByEntityEvent) {
            $damager = $lastDamageCause->getDamager();
            if($damager instanceof Player) $this->plugin->dataManager->add($damager, DataManager::KILL);
        }
        $this->plugin->dataManager->add($entity, DataManager::DEATH);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->plugin->dataManager->add($player, DataManager::JOIN);
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event) {
        $player = $event->getPlayer();
        $this->plugin->dataManager->createData($player);
    }
}
