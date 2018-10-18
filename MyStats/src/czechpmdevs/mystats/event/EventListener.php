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

namespace czechpmdevs\mystats\event;

use czechpmdevs\mystats\MyStats;
use czechpmdevs\mystats\ScoreboardBuilder;
use czechpmdevs\mystats\utils\DataManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\SetLocalPlayerAsInitializedPacket;
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
     * @param EntityLevelChangeEvent $event
     */
    public function onEntityLevelChange(EntityLevelChangeEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) return;

        if($this->getPlugin()->getDataManager()->configData["filter"]) {
            $scoreboardWorlds = $this->getPlugin()->getDataManager()->configData["scoreboardWorlds"];
            if(in_array($event->getTarget()->getFolderName(), $scoreboardWorlds)) {
                $this->updateBoard($entity, $event->getTarget());
            }
            else {
                if(in_array($event->getOrigin()->getFolderName(), $scoreboardWorlds)) {
                    ScoreboardBuilder::removeBoard($entity, strtolower($event->getOrigin()->getFolderName()));
                }
            }
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onReceive(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if($packet instanceof SetLocalPlayerAsInitializedPacket) {
            if ($this->getPlugin()->getDataManager()->configData["filter"] == "true") {
                if (in_array($player->getLevel()->getFolderName(), $this->getPlugin()->getDataManager()->configData["scoreboardWorlds"])) {
                    $format = $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT);
                    ScoreboardBuilder::sendBoard($player, $this->getPlugin()->translateMessage($player, implode(PHP_EOL, $format)));
                    var_dump(1);
                }
            }
            elseif($this->getPlugin()->getDataManager()->configData["defaultFormat"] == DataManager::SCOREBOARD_WORLD) {
                $format = $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT);
                ScoreboardBuilder::sendBoard($player, $this->getPlugin()->translateMessage($player, implode(PHP_EOL, $format)));
                var_dump(2);
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if(!$event->isCancelled()) {
            $this->getPlugin()->getDataManager()->add($player, DataManager::BROKEN);
        }
        $this->updateBoard($player);
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        if(!$event->isCancelled()) {
            $this->getPlugin()->getDataManager()->add($player, DataManager::PLACE);
        }
        $this->updateBoard($player);
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
        $this->updateBoard($event->getPlayer());
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->getPlugin()->getDataManager()->createData($player);
        $this->getPlugin()->getDataManager()->add($player, DataManager::JOIN);
    }

    /**
     * @param Player $player
     * @param Level|null $level
     */
    private function updateBoard(Player $player, Level $level = null) {
        if($level === null) $level = $player->getLevel();
        $dataMgr = $this->plugin->getDataManager();
        if((string)($dataMgr->configData["filter"]) == 'false') {
            if((string)($dataMgr->configData["defaultFormat"]) == '0') {
                $format = $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT);
                ScoreboardBuilder::sendBoard($player, $this->getPlugin()->translateMessage($player, implode(PHP_EOL, $format)), strtolower($level->getFolderName()));
            }
        }
        else {
            if(in_array($level->getFolderName(), $dataMgr->configData["scoreboardWorlds"])) {
                $format = $this->getPlugin()->getDataManager()->getFormat(DataManager::MAIN_FORMAT);
                ScoreboardBuilder::sendBoard($player, $this->getPlugin()->translateMessage($player, implode(PHP_EOL, $format)), strtolower($level->getFolderName()));
            }
        }
    }

    /**
     * @return MyStats
     */
    public function getPlugin(): MyStats {
        return MyStats::getInstance();
    }
}
