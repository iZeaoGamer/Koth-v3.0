<?php



namespace koth\tasks;

use pocketmine\scheduler\Task;

use koth\lang\KothLanguage;
use koth\KothMain;
use koth\arenas\KothArena;

class PreGameTimer extends Task

{

    private $arena;

    private $plugin;

    private $time = 30;

    public function __construct(KothMain $plugin, KothArena $arena) {

        $this->arena = $arena;

        $this->plugin = $plugin;

    }

    public function onRun(int $currentTick) : void {

        if ($this->time == 30 || $this->time == 15 || $this->time < 6){

            $this->plugin->getServer()->broadcastMessage(KothLanguage::getMessage("KOTH_PRESTART_COUNTDOWN", [
                "{time}" => gmdate("i:s", $this->time)]));

        }

        $this->time--;

        if ($this->time < 1){

            $this->arena->startGame();

            $this->plugin->getServer()->broadcastMessage(KothLanguage::getMessage("KOTH_BEGUN"));
                

            $this->getHandler()->cancel();

        }

        $this->arena->sendPopup(KothLanguage::getMessage("KOTH_STARTING_POPUP", [
            "{time}" => gmdate("i:s", $this->time)]));

    }

    

    }