<?php

namespace MyStats;

use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\Config;

class MyStats extends PluginBase implements Listener {

    public $prefix;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MyStatsTask($this), 1);
        $this->getLogger()->info($this->prefix." §6Loading MyStats...");
        $this->loadConfig();
    }

    public function loadConfig() {
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."players");
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
        }
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        if($cfg->get("levels") == "") {
            $cfg->set("levels", ["Lobby", "Hub", "Spawn", "Survival", "Creative", "Shop", "world"]);
            $cfg->save();
        }
        $this->prefix = "{$cfg->get("prefix")}§f ";
    }

    /**
     * @param $msg
     * @param Player $p
     * @return mixed
     */
    public function translateMessage($msg, Player $p) {
        $m = "                                                             ";
        $pcfg = new Config($this->getDataFolder() . "players/{$p->getName()}.yml", Config::YAML);
        $msg = str_replace("{L}", "\n{$m}", $msg);
        $msg = str_replace("{N}", $p->getName(), $msg);
        $msg = str_replace("{O}", count($this->getServer()->getOnlinePlayers()), $msg);
        $msg = str_replace("{P}", $pcfg->get("placed"), $msg);
        $msg = str_replace("{B}", $pcfg->get("breaked"), $msg);
        $msg = str_replace("{D}", $pcfg->get("deaths"), $msg);
        $msg = str_replace("{K}", $pcfg->get("kills"), $msg);
        $msg = str_replace("{J}", $pcfg->get("joins"), $msg);
        return $msg;
    }

    /**
     * @param Player $p
     * @return mixed
     */
    public function getStats(Player $p) {
        return $this->translateMessage($this->getConfig()->get("format"), $p);
    }

    /**
     * @param int | string $line
     * @param $text
     */
    public function setLine($line, $text) {
        $format = $this->getConfig()->get("format");
        $args = explode("{L}", $format);
        if($line < 0) {
            $args[$line-1] = $text;
            $this->getConfig()->set("format",implode("{L}",$args));
            $this->getConfig()->save();
        }
    }

    /**
     * @param int|string$line
     * @return mixed
     */
    public function getLine($line) {
        $format = $this->getConfig()->get("format");
        $args = explode("{L}", $format);
        if($line > 0) {
            return $args[$line-1];
        }
    }

    public function onJoin(PlayerJoinEvent $e) {
        $p = $e->getPlayer();
        if(!is_file($this->getDataFolder()."players/{$p->getName()}.yml")) {
            $cfg = new Config($this->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
            $cfg->set("kills", 0.0);
            $cfg->set("deaths", 0.0);
            $cfg->set("placed", 0.0);
            $cfg->set("breaked", 0.0);
            $cfg->set("joins", 1);
            $cfg->save();
        }
        else {
            $cfg = new Config($this->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
            $cfg->set("joins", $cfg->get("joins")+1);
            $cfg->save();
        }
    }

    public function onDeath(PlayerDeathEvent $e) {
        $en = $e->getEntity();
        if($en instanceof Player) {
            $player = $en;
            $pcfg = new Config($this->getDataFolder()."players/{$player->getName()}.yml", Config::YAML);
            $pcfg->set("deaths", $pcfg->get("deaths")+1);
            $pcfg->save();

            if($e instanceof EntityDamageByEntityEvent) {
                $d = $e->getDamager();
                if($d instanceof Player) {
                    $damager = $d;
                    $pcfg = new Config($this->getDataFolder()."players/{$damager->getName()}.yml", Config::YAML);
                    $pcfg->set("kills", $pcfg->get("kills")+1);
                    $pcfg->save();
                }
            }
        }
    }

    public function onPlace(BlockPlaceEvent $e) {
        $p = $e->getPlayer();
        $cfg = new Config($this->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
        $cfg->set("placed", $cfg->get("placed")+1);
        $cfg->save();
    }

    public function onBreak(BlockBreakEvent $e) {
        $p = $e->getPlayer();
        $cfg = new Config($this->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
        $cfg->set("breaked", $cfg->get("breaked")+1);
        $cfg->save();
    }
}

class MyStatsTask extends PluginTask {

    /** @var  MyStats */
    public $plugin;

    public function __construct($plugin) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick) {
        $cfg = $this->plugin->getConfig();
        $levels = $cfg->get("levels");
        foreach ($levels as $level) {
            if (file_exists($this->plugin->getServer()->getDataPath() . "worlds/{$level}")) {
                foreach ($this->plugin->getServer()->getLevelByName($level)->getPlayers() as $p) {
                    $msg = $this->plugin->getStats($p);
                    $p->sendTip("                                                             ".$msg);
                }
            }
        }
    }
}
