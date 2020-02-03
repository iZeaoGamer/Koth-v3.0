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
use pocketmine\Server;
class KothTimer extends Task
{
    private $arena;
    private $plugin;
 //   private $time = 30;
    public function __construct(KothMain $owner, KothArena $arena) {
        $this->arena = $arena;
        $this->plugin = $owner;
    }
    public function onRun(int $currentTick) : void {
     //   $msg = $this->plugin->getData("starting");
    //    $msg = str_replace("{sec}",$this->time,$msg);
    
       // $msg = $this->plugin->prefix().$msg;
       if(!file_exists($this->plugin->getDataFolder() . "kothinfo.yml")){
          $this->plugin->setKothTimer(1000);
       }
       $this->plugin->setKothTimer($this->plugin->getKothTimer() - 1);
       $this->time = $this->plugin->getKothTimer();
       
       if($this->time === 300 || $this->time === 600 || $this->time === 1800 || $this->time === 3600){
           $this->plugin->discord->sendToDiscord("**KOTH EVENT**\nKoth event is starting in " . gmdate("H:i:s", $this->time) . " on OP Factions.",
           $this->plugin->msg->get("webhook-url"),            $this->plugin->msg->get("bot-displayname"));
       }
           
     //  if(file_exists($this->plugin->getDataFolder() . "kothinfo.yml")){
       if ($this->time == 30 || $this->time == 15 || $this->time < 6){
           
           $this->plugin->discord->sendToDiscord("**KOTH EVENT**\nKoth timer is starting in " . gmdate("i:s", $this->time) . " on OP Factions.",
           $this->plugin->msg->get("webhook-url"),            $this->plugin->msg->get("bot-displayname"));
            //$this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&6KOTH event starts in &b" . gmdate("i:s", $this->time)));
       $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&6Koth event timer starts in &b" . gmdate("i:s", $this->time)));
        }
        $this->time--;
        if ($this->time < 1){
            $this->arena->preStart();
           
            $this->plugin->discord->sendToDiscord("**KOTH EVENT**\nKoth game is about to start! Join a koth game by going on OP Factions, and type /koth join\nIP: play.zectorpe.ml Port: 19132",
            $this->plugin->msg->get("webhook-url"),
            $this->plugin->msg->get("bot-displayname"));
            $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&6KOTH Event is starting.. Type: &b/koth join &6to join the koth event!"));
         //   $this->getHandler()->cancel();
         $this->plugin->setKothTimer($this->plugin->msg->get("event_time"));
   //     }
        //$this->arena->sendPopup(TextFormat::colorize("&6Game Starting in §b". gmdate("i:s", $this->time)));
    }
    
    }
}
