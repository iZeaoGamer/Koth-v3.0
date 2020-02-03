<?php
/**
 * Created by PhpStorm.
 * User: JeremyMorales
 * Date: 6/22/17
 * Time: 10:51 AM
 */
namespace koth;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
class PreGameTimer extends Task
{
    private $arena;
    private $plugin;
    private $time = 30;
    public function __construct(KothMain $owner, KothArena $arena) {
        $this->arena = $arena;
        $this->plugin = $owner;
    }
    public function onRun(int $currentTick) : void {
        $msg = $this->plugin->getData("starting");
        $msg = str_replace("{sec}",$this->time,$msg);
        $msg = $this->plugin->prefix().$msg;
        if ($this->time == 30 || $this->time == 15 || $this->time < 6){
            $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&6KOTH event starts in &b" . gmdate("i:s", $this->time)));
        }
        $this->time--;
        if ($this->time < 1){
            $this->arena->startGame();
            $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&6KOTH Event has started. Type: &b/koth join &6to join the koth event!"));
            $this->getHandler()->cancel();
        }
        $this->arena->sendPopup(TextFormat::colorize("&6Game Starting in §b". gmdate("i:s", $this->time)));
    }
    
    }
