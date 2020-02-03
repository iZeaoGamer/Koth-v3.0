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
use pocketmine\event\player\PlayerRespawnEvent;use pocketmine\event\player\PlayerCreationEvent;
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
    }	
     public function onJoin(PlayerJoinEvent $event){
          $p = $event->getPlayer();
		 $this->incap[] = $p->getName();
		 $this->notin[] = $p->getName();
     }
    public function onRespawn(PlayerRespawnEvent $ev){
       // if ($this->plugin->isRunning()){
            $p = $ev->getPlayer();
             if($p instanceof KothPlayer){
          if($p->isInGame()){
         
            $p->addTitle($this->plugin->getData("still_running_title"),$this->plugin->getData("still_running_sub"));
		$this->plugin->sendRandomSpot($p);		}
    }
    }	public function onCreation(PlayerCreationEvent $event){		if($this->plugin->msg->get("player-creation")){$event->setPlayerClass(KothPlayer::class);}	}
    public function onLeave(PlayerQuitEvent $ev){        $player = $ev->getPlayer();        if($player instanceof KothPlayer){        if($player->isInGame()){        $this->plugin->removePlayer($ev->getPlayer());    }        }    }
    public function onCommand(PlayerCommandPreprocessEvent $ev){
    //  $spawn = new Position(204, 189, 329, Server::getInstance()->getLevelByName("FacSpawn2"));
      //if($spawn->distance($ev->getPlayer() < 200)){
      $player = $ev->getPlayer();
      if($player instanceof KothPlayer){
          if($player->isInGame()){
       if(substr($ev->getMessage(), 0, 6) === "/spawn"){
            $this->plugin->removePlayer($ev->getPlayer());
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
   /* public function Move(PlayerMoveEvent $event){
        $player = $event->getPlayer();
       
             $arena = $this->plugin->arena;
             if($arena instanceof KothArena){
                 if(!$arena->inCapture($player)){
         $id = array_search($player->getName(), $this->incap);
							unset($this->incap[$id]);
					//		$this->notin[] = $player->getName();
     //   $title = TextFormat::colorize("&cNow leaving");
      //  $subtitle = TextFormat::colorize("&b" . $fac . "'s &6claim.");
    //    $player->addTitle($title, $subtitle);
            // $this->infac[] = $player->getName();
       // $title = TextFormat::colorize("&6Now entering");
   ///     $subtitle = TextFormat::colorize("&b" . $fac , "'s &6claim.");
     //   $player->addTitle($title, $subtitle);
    }else{
        if(!in_array($player->getName(), $this->incap)){
           //  $id = array_search($player->getName(), $this->notin);
			//				unset($this->notin[$id]);
            $this->incap[] = $player->getName();
            if($arena->players instanceof Player){
            $arena->players->sendTitle(TextFormat::colorize("&7&l" . $player->getName() . " &d&lis"), TextFormat::colorize(" &d&lcapturing the koth"));
          //   }
        }
    }
    }
     $arena = $this->plugin->arena;
             if($arena instanceof KothArena){
    if($arena->inCapture($player)){
         $id = array_search($player->getName(), $this->notin);
							unset($this->notin[$id]);
     }else{
         if(!in_array($player->getName(), $this->notin)){
							$this->notin[] = $player->getName();
							if($arena->players instanceof Player){
					$arena->players->addTitle(TextFormat::colorize("&7&l" . $player->getName() . " &d&lis"), TextFormat::colorize(" &d&lno longer capturing the koth."));		
            //  $entity->addTitle(TextFormat::colorize("&7&l" . $entity->getName() . " &d&lis"), TextFormat::colorize(" &d&lno longer capturing the koth."));
         }
             
         }
}
}
}
}
}