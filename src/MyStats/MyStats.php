<?php

namespace MyStats;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

// Economy
use onebone\economyapi\EconomyAPI;
use PocketMoney\PocketMoney;

class MyStats extends PluginBase implements Listener {

    public $prefix;
    public $economy = "false";

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MyStatsTask($this), 1);
        $this->getLogger()->info($this->prefix." §6Loading MyStats...");
        $this->loadConfig();
        $this->getEconomy();
    }

    public static function getPermissionMessage() {
        return "§cYou do not have permission to use this command";
    }

    public function loadConfig() {
        // Config update
        if(is_file($this->getDataFolder()."/config.yml") && !$this->getConfig()->exists("economy")) {
            rename($this->getDataFolder()."/config.yml", $this->getDataFolder()."/config-old.yml");
        }

        // Save dirs
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."players");

        // Save config
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
        }

        // Set to config levels
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        if($cfg->get("levels") == "") {
            $cfg->set("levels", ["Lobby", "Hub", "Spawn", "Survival", "Creative", "Shop", "world"]);
            $cfg->save();
        }

        $this->prefix = str_replace("&","§",$cfg->get("prefix"))."§r§e ";
    }

    public function getEconomy() {
        if($this->getConfig()->get("economy") == "EconomyAPI") {
            if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->isEnabled()) {
                $this->economy = "EconomyAPI";
            }
        }
        elseif($this->getConfig()->get("economy") == "PocketMoney") {
            if($this->getServer()->getPluginManager()->getPlugin("PocketMoney")->isEnabled()) {
                $this->economy = "PocketMoney";
            }
        }
        else {
            $this->economy = "false";
        }
    }

    /**
     * @param Player $player
     * @return bool|false|float|int|string
     */
    public function getMoney(Player $player) {
        if($this->economy == "EconomyAPI") {
            $economyapi = new EconomyAPI();
            return $economyapi->myMoney($player);
        }
        elseif($this->economy == "PocketMoney") {
            $pocketmoney = new PocketMoney();
            return $pocketmoney->getMoney($player->getName());
        }
        else {
            return "§cPlugin not found!";
        }
    }

    /**
     * @param $msg
     * @param Player $p
     * @return mixed
     */
    public function translateMessage($msg, Player $p, $int) {
        $m = "                                                             ";
        $pcfg = new Config($this->getDataFolder() . "players/{$p->getName()}.yml", Config::YAML);

        // Server
        $msg = str_replace("{O}", count($this->getServer()->getOnlinePlayers()), $msg);
        $msg = str_replace("{V}", $this->getServer()->getVersion(), $msg);
        $msg = str_replace("{IP}", $this->getServer()->getIp(), $msg);
        $msg = str_replace("{PORT}", $this->getServer()->getPort(), $msg);

        // Economy
        $msg = str_replace("{M}", $this->getMoney($p), $msg);

        // Player
        $msg = str_replace("{P}{name}", $p->getName(), $msg);
        $msg = str_replace("{P}{x}", $p->getX(), $msg);
        $msg = str_replace("{P}{y}", $p->getY(), $msg);
        $msg = str_replace("{P}{z}", $p->getZ(), $msg);

        // Stats
        $msg = str_replace("{P}", $pcfg->get("placed"), $msg);
        $msg = str_replace("{B}", $pcfg->get("breaked"), $msg);
        $msg = str_replace("{D}", $pcfg->get("deaths"), $msg);
        $msg = str_replace("{K}", $pcfg->get("kills"), $msg);
        $msg = str_replace("{J}", $pcfg->get("joins"), $msg);

        // Item in hand
        $msg = str_replace("{I}{id}", $p->getItemInHand()->getId(), $msg);
        $msg = str_replace("{I}{name}", $p->getItemInHand()->getName(), $msg);

        // Text
        $msg = str_replace("&", "§", $msg);
        if($int == 1) $msg = str_replace("{L}", "\n{$m}", $msg);
        if($int == 2) $msg = str_replace("{L}", "\n", $msg);
        return $msg;
    }

    /**
     * @param Player $p
     * @return mixed
     */
    public function getStats(Player $p, $int) {
        return $this->translateMessage($this->getConfig()->get("format-{$int}"), $p, $int);
    }

    /**
     * @param string $economy
     */
    public function setEconomy($economy) {
        $this->getConfig()->set("economy", $economy);
        $this->getConfig()->save();
        $this->economy = $economy;
    }

    /**
     * @param string $levelname
     */
    public function addLevel($levelname) {
        $levels = $this->getConfig()->get("levels");
        $this->getConfig()->set("levels",array_push($levels,$levelname));
        $this->getConfig()->save();
    }

    public function onCommand(CommandSender $s, Command $cmd, $label, array $args)
    {
        if ($s instanceof Player) {
            switch ($cmd->getName()) {
                case "stats":
                    $s->sendMessage($this->getStats($s, 2));
                    break;
                case "sedit":
                    if (isset($args[0])) {
                        switch ($args[0]) {
                            case "help":
                                if (!$s->hasPermission("ms.sedit")) {
                                    $s->hasPermission(self::getPermissionMessage());
                                    break;
                                }
                                $s->sendMessage("§5--- §c[ §bMyStats §c] §5---\n" .
                                    "§3/sedit help §aDisplays help menu\n" .
                                    "§3/sedit economy §aChange economy type\n" .
                                    "§3/sedit addlevel §aAdd level for stats on screen");
                                break;
                            case "economy":
                                if (!$s->hasPermission("ms.edit")) {
                                    $s->sendMessage(self::getPermissionMessage());
                                    break;
                                }
                                if (empty($args[1])) {
                                    $s->sendMessage($this->prefix . "§7Usage: §c/sedit economy <PocketMoney | EconomyAPI | false>");
                                    break;
                                }
                                if ($args[1] == "EconomyAPI" || $args[1] == "PocketMoney" || $args[1] == "false") {
                                    $this->setEconomy($args[1]);
                                    $s->sendMessage($this->prefix . "Economy updated to " . $this->economy . ".");
                                } else {
                                    $s->sendMessage($this->prefix . "§7Usage: §c/sedit economy <PocketMoney | EconomyAPI | false>");
                                }

                                break;
                            case "addlevel":
                                if (!$s->hasPermission("ms.edit")) {
                                    $s->sendMessage(self::getPermissionMessage());
                                    break;
                                }
                                if (empty($args[1])) {
                                    $s->sendMessage($this->prefix . "§7Usage: §c/sedit addlevel <level>");
                                    break;
                                }
                                if (file_exists($this->getServer()->getDataPath() . $args[1])) {
                                    $this->addLevel($args[1]);
                                    $s->sendMessage($this->prefix . "§aLevel added to level list!");
                                } else {
                                    $s->sendMessage($this->prefix . "§cLevel does not exists.");
                                }
                                break;
                            case "settext":
                                if (!$s->hasPermission("ms.edit")) {
                                    $s->sendMessage(self::getPermissionMessage());
                                    break;
                                }
                                if (empty($args[1])) {
                                    $s->sendMessage($this->prefix . "§7Usage: §c/sedit settext <text>");
                                    break;
                                }
                                $this->getConfig()->set("format-1", str_replace("_", " ", $args[1]));
                                $this->getConfig()->save();
                                $s->sendMessage($this->prefix."§aText updated!");
                                $this->getServer()->reload();
                                break;
                            default:
                                $s->sendMessage($this->prefix . "§7Usage: §c/sedit <help>");
                                break;
                        }
                    } else {
                        $s->sendMessage($this->prefix . "§7Usage: §c/sedit <help>");
                    }
                    break;
            }
        }
    }

    public function onJoin(PlayerJoinEvent $e) {
        $p = $e->getPlayer();
        if(!is_file($this->getDataFolder()."players/{$p->getName()}.yml")) {
            $cfg = new Config($this->getDataFolder()."players/{$p->getName()}.yml", Config::YAML);
            $cfg->set("kills", "0");
            $cfg->set("deaths", "0");
            $cfg->set("placed", "0");
            $cfg->set("breaked", "0");
            $cfg->set("joins", "1");
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

            $dmg = $en->getLastDamageCause();
            if($dmg instanceof EntityDamageEvent && $dmg instanceof EntityDamageByEntityEvent){
                $d = $dmg->getDamager();
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
