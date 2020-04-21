<?php


namespace koth\tasks;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use koth\KothMain;
use koth\arenas\KothArena;
use koth\lang\KothLanguage;

class KothTimer extends Task
{
    
    private $arena;
    private $plugin;

    public function __construct(KothMain $plugin, KothArena $arena) {

        $this->arena = $arena;
        $this->plugin = $plugin;
    }
    public function onRun(int $currentTick) : void {
       if(!file_exists($this->plugin->getDataFolder() . "kothinfo.yml")){
          $this->plugin->setKothTimer($this->plugin->msg->get("event_time"));
       }
       $this->plugin->setKothTimer($this->plugin->getKothTimer() - 1);
       $this->time = $this->plugin->getKothTimer();
       if($this->time === 300 || $this->time === 600 || $this->time === 1800 || $this->time === 3600){
           if($this->plugin->msg->get("discord-support")){
           $this->plugin->discord->sendToDiscord(KothLanguage::getMessage("KOTH_STARTING_DISCORD", [
               "{line}" => "\n",
               "{time}" => gmdate("H:i:s", $this->time)]), $this->plugin->msg->get("webhook-url"), $this->plugin->msg->get("bot-displayname"));
           }
       }
       if ($this->time == 30 || $this->time == 15 || $this->time < 6){
if($this->plugin->msg->get("discord-support")){
           
           $this->plugin->discord->sendToDiscord(KothLanguage::getMessage("KOTH_STARTING_DISCORD", [
               "{time}" => gmdate("H:i:s", $this->time)]), $this->plugin->msg->get("webhook-url"), $this->plugin->msg->get("bot-displayname"));
       }
            
       $this->plugin->getServer()->broadcastMessage(KothLanguage::getMessage("KOTH_STARTING_MESSAGE", [
           "{line}" => "\n",
           "{time}" => gmdate("H:i:s", $this->time)]));
       }
        $this->time--;
        if ($this->time < 1){
            $this->arena->preStart();
if($this->plugin->msg->get("discord-support")){
            $this->plugin->discord->sendToDiscord(KothLanguage::getMessage("KOTH_PRESTART_SUCCESS_DISCORD", [
                "{line}" => "\n"
            ]), $this->plugin->msg->get("webhook-url"), $this->plugin->msg->get("bot-displayname"));
         }
            $this->plugin->getServer()->broadcastMessage(KothLanguage::getMessage("KOTH_PRESART_SUCCESS"));
         $this->plugin->setKothTimer($this->plugin->getEventTime());
    }
    }
}
