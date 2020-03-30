<?php


namespace koth\listeners;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\level\Position;

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerCreationEvent;

use pocketmine\Server;

use pocketmine\event\player\PlayerJoinEvent;


use pocketmine\Player;

use koth\KothMain;
use koth\KothPlayer;
use koth\lang\KothLanguage;




class KothListener implements Listener

{

    private $plugin;

    public function __construct(KothMain $main)

    {

	   
        $this->plugin = $main;
	    $this->arena = $main->arena;

    }
	
     public function onJoin(PlayerJoinEvent $event){

          $p = $event->getPlayer();

		 $this->incap[] = $p->getName();

		 $this->notin[] = $p->getName();

     }

    public function onRespawn(PlayerRespawnEvent $ev){

            $p = $ev->getPlayer();

             if($p instanceof KothPlayer){

          if($p->isInGame()){

         
            $p->addTitle($this->plugin->getData("still_running_title"),$this->plugin->getData("still_running_sub"));

		  $old = $this->arena->spawns[array_rand($this->arena->spawns)];
		$ev->setRespawnPosition($old);
		}

    }

    }
	public function onCreation(PlayerCreationEvent $event){
		if($this->plugin->getConfig()->get("player-creation")){
$event->setPlayerClass(KothPlayer::class);
}
	}

    public function onLeave(PlayerQuitEvent $ev){
        $player = $ev->getPlayer();
        if($player instanceof KothPlayer){
        if($player->isInGame()){
        $this->arena->removePlayer($ev->getPlayer());
    }
        }
    }

    public function onCommand(PlayerCommandPreprocessEvent $ev){
      $player = $ev->getPlayer();
      if($player instanceof KothPlayer){
          if($player->isInGame()){
       if(substr($ev->getMessage(), 0, 6) === $this->plugin->getConfig()->get("spawn-command")){
            $this->arena->removePlayer($ev->getPlayer());
        }
        $message = $ev->getMessage();
        if($message[0] != "/") {
            return;
        }
    $command = strtolower(substr($message, 1));
        if(in_array($command, (array)$this->plugin->getConfig()->get("BlockedCommands"))) {
    $ev->setCancelled();

            $ev->getPlayer()->sendMessage(KothLanguage::getMessage("KOTH_BLOCKED_COMMAND_MESSAGE"));
                
        }

    }

      }

    }

    public function GamemodeChange(PlayerMoveEvent $event){

        $player = $event->getPlayer();

        if($player instanceof KothPlayer){

            if($player->isInGame()){

                if($player->getGamemode() > 0){

                    $player->setGamemode(Player::SURVIVAL);
                        $player->sendMessage(KothLanguage::getMessage("KOTH_SURVIVAL_MODE"));
                    

                }

            }

        }

    }

}