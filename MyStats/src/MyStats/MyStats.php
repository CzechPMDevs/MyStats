<?php

namespace MyStats;

use MyStats\Economy\EconomyManager;
use MyStats\Event\EventListener;
use MyStats\Task\SendStatsTask;
use MyStats\Util\ConfigManager;
use MyStats\Util\DataManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

/**
 * Class MyStats
 * @package MyStats
 */
class MyStats extends PluginBase{

    const NAME = "MyStats";
    const VERSION = "1.4.0 [BETA 1]";
    const AUTHOR = "GamakCZ";
    const GITHUB = "https://github.com/CzechPMDevs/MyStats/";

    /** @var  MyStats $instance */
    static $instance;

    /** @var  EventListener $eventListener */
    public $eventListener;

    /** @var  EconomyManager $economyManager */
    public $economyManager;

    /** @var  DataManager $dataManager */
    public $dataManager;

    /** @var  SendStatsTask $sendStatsTask */
    public $sendStatsTask;

    public function onEnable() {
        self::$instance = $this;
        $this->dataManager = new DataManager($this);
        $this->economyManager = new EconomyManager($this);
        $this->getServer()->getPluginManager()->registerEvents($this->eventListener = new EventListener($this), $this);
        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n§5**********************************************\n".
                "§6 ---- == §c[§aMultiWorld§c]§6== ----\n".
                "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                "§9> Author: §eCzechPMDevs :: GamakCZ, Kyd\n".
                "§9> GitHub: §e".self::GITHUB."\n".
                "§9> Package: §e{$phar}\n".
                "§9> Language: §eEnglish\n".
                "§5**********************************************");
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to ".self::GITHUB."issues to fix it.");
        }
    }

    /**
     * @return MyStats $instance
     */
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix() {
        return ConfigManager::getPrefix();
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
        $msg = str_replace("{I}{id}", $p->getInventory()->getItemInHand()->getId(), $msg);
        $msg = str_replace("{I}{name}", $p->getInventory()->getItemInHand()->getName(), $msg);

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
