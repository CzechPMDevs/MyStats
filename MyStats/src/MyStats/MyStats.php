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
    const VERSION = "1.4.0 [BETA 2]";
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

    /** @var  ConfigManager $configManager */
    public $configManager;

    /** @var  SendStatsTask $sendStatsTask */
    public $sendStatsTask;

    public function onEnable() {
        self::$instance = $this;
        $this->configManager = new ConfigManager($this);
        $this->configManager->init();
        $this->dataManager = new DataManager($this);
        $this->economyManager = new EconomyManager($this);

        $this->getServer()->getPluginManager()->registerEvents($this->eventListener = new EventListener($this), $this);
        if($this->isEnabled()) {
            $phar = null;
            $this->isPhar() ? $phar = "Phar" : $phar = "src";
            $this->getLogger()->info("\n§5**********************************************\n".
                "§6 ---- == §c[§aMyStats§c]§6== ----\n".
                "§9> Version: §e{$this->getDescription()->getVersion()}\n".
                "§9> Author: §eCzechPMDevs :: GamakCZ\n".
                "§9> GitHub: §e".self::GITHUB."\n".
                "§9> Package: §e{$phar}\n".
                "§9> Language: §eEnglish\n".
                "§5**********************************************");
        }
        else {
            $this->getLogger()->info(self::getPrefix()."§6Submit issue to §7".self::GITHUB."issues §6to fix it.");
        }
    }

    public function onDisable() {
        $this->dataManager->saveData();
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

    public function translateMessage(string $message, Player $player):string {
        $data = $this->dataManager->getPlayerData($player);
        $message = str_replace("%name", $player->getName(), $message);
        $message = str_replace("%x", $player->getY(), $message);
        $message = str_replace("%y", $player->getY(), $message);
        $message = str_replace("%z", $player->getZ(), $message);
        $message = str_replace("%level", $player->getLevel()->getName(), $message);
        $message = str_replace("%breaked", $data->getBreakedBlocks(), $message);
        $message = str_replace("%placed", $data->getPlacedBlocks(), $message);
        $message = str_replace("%kills", $data->getKills(), $message);
        $message = str_replace("%deaths", $data->getDeaths(), $message);
        $message = str_replace("%joins", $data->getJoins(), $message);
        $message = str_replace("%money", $data->getMoney(), $message);
        $message = str_replace("%online", $this->getServer()->getQueryInformation()->getPlayerCount(), $message);
        $message = str_replace("%ip", $this->getServer()->getIp(), $message);
        $message = str_replace("%port", $this->getServer()->getPort(), $message);
        $message = str_replace("%version", $this->getServer()->getVersion(), $message);
        $message = str_replace("%line", "\n", $message);
        $message = str_replace("&", "§", $message);

        return $message;
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool {
        $cmd = $command->getName();
        if(in_array($cmd, ["ms", "stats", "mystats"])) {
            if(empty($args[0]) && ($sender instanceof Player)) {
                $format = $this->translateMessage($this->dataManager->cmdFormat, $sender);
                $sender->sendMessage($format);
                return false;
            }
            return false;
        }
    }
}
