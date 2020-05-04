<?php


namespace koth\listeners;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\level\Position;

use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\player\PlayerRespawnEvent;

use pocketmine\Server;

use pocketmine\event\player\PlayerJoinEvent;


use pocketmine\Player;

use koth\KothMain;
use koth\lang\KothLanguage;

use pocketmine\scheduler\ClosureTask;




class KothListener implements Listener

{

    private $plugin;
	public static $kothtask;
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

          if($this->plugin->kothplayer->isInGame($p)){

         
            $p->addTitle($this->plugin->getData("still_running_title"),$this->plugin->getData("still_running_sub"));

		  $old = $this->arena->spawns[array_rand($this->arena->spawns)];
		$p->teleport($old); //todo add ClosureTask just in case.
	  }else{
		  //a hack to prevent messy respawn position for current owners.
		   self::$kothtask[$p->getId()] = new ClosureTask(function () use ($p, $ev): void {
			   if($p->isOnline()){
		     
$this->arena->teleportFinish($p); 
				   $ev->setRespawnPosition($p);  //sets respawn position after teleportation has been successful
			    self::$kothtask[$p->getId()]->getHandler()->cancel();
                        unset(self::$kothtask[$p->getId()]);
                        return;
                    }
		    });
                $this->plugin->getScheduler()->scheduleRepeatingTask(self::$kothtask[$p->getId()], 20 * 1);
	}
	  

    }

    public function onLeave(PlayerQuitEvent $ev){
        $player = $ev->getPlayer();
        if($this->plugin->kothplayer->isInGame($player)){
        $this->arena->removePlayer($ev->getPlayer());
    }
        }

    public function onCommand(PlayerCommandPreprocessEvent $ev){
      $player = $ev->getPlayer();
          if($this->plugin->kothplayer->isInGame($player)){
		  $message = $ev->getMessage();
       if(substr($message, 0, 1) != "/") {
	       return;
       }
	       $command = substr(explode(" ", $message)[0], 1);
       if(in_array(strtolower($command), $this->plugin->getConfig()->get("spawn-command"))){
            $this->arena->removePlayer($ev->getPlayer());
        }
	
				
				if(in_array(strtolower($command), $this->plugin->getConfig()->get("BlockedCommands"))) {
					$ev->setCancelled();
    $ev->setCancelled();

            $ev->getPlayer()->sendMessage(KothLanguage::getMessage("KOTH_BLOCKED_COMMAND_MESSAGE"));
                
        }

    }

      }

    public function GamemodeChange(PlayerMoveEvent $event){

        $player = $event->getPlayer();

            if($this->plugin->kothplayer->isInGame($player)){

                if($player->getGamemode() > 0){

                    $player->setGamemode(Player::SURVIVAL);
                        $player->sendMessage(KothLanguage::getMessage("KOTH_SURVIVAL_MODE"));
                    

                }

            }

        }

    }
