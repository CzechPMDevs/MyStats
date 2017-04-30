<?php

namespace MyStats;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class MyStats extends PluginBase{

    /** @var  EventListener */
    public $listener;

    /** @var  EconomyManager */
    public $economyManager;

    public $prefix;
    public $economy;

    public function onEnable() {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MyStatsTask($this), 1);
        $this->getLogger()->info($this->prefix." §6Loading MyStats...");
        $this->loadConfig();
        $this->getEconomy();
        $this->getListener();
        $this->getServer()->getPluginManager()->registerEvents($this->listener, $this);
    }

    public static function getPermissionMessage() {
        return "§cYou do not have permission to use this command";
    }

    public function loadConfig() {
        // Save dirs
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."players");

        // Save config
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
        }

        $this->prefix = str_replace("&","§",$this->getConfig()->get("prefix"))."§r§e ";
    }

    public function getListener() {
        $this->listener = new EventListener($this);
    }

    public function getEconomy() {
        $this->economyManager = new EconomyManager($this);
        $this->economy = EconomyManager::$economy;
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
        $msg = str_replace("{M}", $this->economyManager->getPlayerMoney($p), $msg);

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
                                    "§3/sedit addlevel §aAdd level for stats on screen\n".
                                    "§3/sedit settext §aSet text on screen");
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
                                    $this->economyManager->setEconomy($args[1]);
                                    $this->getEconomy();
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
}
