<?php

/**

 * Created by PhpStorm.

 * User: JeremyMorales

 * Date: 6/22/17

 * Time: 10:26 AM

 */

namespace koth;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\level\Position;

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerCreationEvent;

use pocketmine\Server;

use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\utils\TextFormat;

use pocketmine\Player;




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
		if($this->plugin->msg->get("player-creation")){
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
       if(substr($ev->getMessage(), 0, 6) === "/spawn"){
            $this->arena->removePlayer($ev->getPlayer());
        }
         if(substr($ev->getMessage(), 0, 2) === "/h" || substr($ev->getMessage(), 0, 5) === "/home" || substr($ev->getMessage(), 0, 4) === "/afk" || substr($ev->getMessage(), 0, 8) === "/godmode" || substr($ev->getMessage(), 0, 4) === "/god"){
            $ev->setCancelled();

            $ev->getPlayer()->sendMessage(TextFormat::colorize("&cYou cannot use this command in koth."));

        }

    }

      }

    }

    public function GamemodeChange(PlayerMoveEvent $event){

        $player = $event->getPlayer();

        if($player instanceof KothPlayer){

            if($player->isInGame()){

                if($player->getGamemode() > 0){

                    $player->setGamemode(0);

                    $player->sendMessage(TextFormat::colorize("&6Your gamemode has been changed to &bsurvival &6because you’re currently in a koth game."));

                    

                }

            }

        }

    }

}
