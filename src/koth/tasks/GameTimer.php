<?php

namespace koth\tasks;

use pocketmine\scheduler\Task;

use koth\lang\KothLanguage;
use koth\arenas\KothArena;
use koth\KothMain;

class GameTimer extends Task

{

    private $plugin;

    private $arena;

    private $time;

    public function __construct(KothMain $plugin, KothArena $arena){

        $this->plugin = $plugin;

        $this->arena = $arena;

        $this->time = $plugin->getData("game_time") * 60;

    }

    public function onRun(int $currentTick) : void{

        $time = $this->time--;

        if ($time < 1){

            $this->arena->endGame();

        

            return;

        } 

        $this->arena->sendPopup(KothLanguage::getMessage("KOTH_TIME_LEFT_POPUP", [
            "{time}" => gmdate("i:s", $time)]));
           

      $this->arena->checkPlayers();

    }

}

