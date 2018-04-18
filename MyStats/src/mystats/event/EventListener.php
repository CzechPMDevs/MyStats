<?php

declare(strict_types=1);

namespace mystats\event;

use mystats\MyStats;
use mystats\utils\DataManager;
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
 * @package mystats\Event
 */
class EventListener implements Listener {

    /** @var  MyStats */
    public $plugin;

    /**
     * EventListener constructor.
     * @param $plugin
     */
    public function __construct(MyStats $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin = $plugin);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if(!$event->isCancelled()) {
            $this->getPlugin()->getDataManager()->add($player, DataManager::BROKEN);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        if(!$event->isCancelled()) {
            $this->getPlugin()->getDataManager()->add($player, DataManager::PLACE);
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
            if($damager instanceof Player) $this->getPlugin()->getDataManager()->add($damager, DataManager::KILL);
        }
        $this->getPlugin()->getDataManager()->add($entity, DataManager::DEATH);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->getPlugin()->getDataManager()->createData($player);
        $this->getPlugin()->getDataManager()->add($player, DataManager::JOIN);
    }

    public function getPlugin():MyStats {
        return MyStats::getInstance();
    }
}
