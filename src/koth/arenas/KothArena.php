<?php


namespace koth\arenas;

use pocketmine\command\ConsoleCommandSender;

use pocketmine\level\Position;

use pocketmine\Player;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use pocketmine\item\Item;

use pocketmine\math\Vector3;
use FactionsPro\FactionMain;

use koth\KothMain;
use koth\tasks\GameTimer;
use koth\tasks\KothTimer;
use koth\tasks\PreGameTimer;
use koth\lang\KothLanguage;
use koth\KothPlayer;


class KothArena
{

    private $running = false;

    public $players = [];

    public $spawns = [];

    private $p1;

    private $p2;


    public $plugin;


    private $timer = null;

    public function __construct(KothMain $main, $spawns, $capture){

        $this->plugin = $main;
	   
    

      foreach($spawns as $val => $pos){
				$strPos = explode(':', $pos);
	      if(!$this->plugin->getServer()->isLevelLoaded($strPos[3])){
	      Server::getInstance()->loadLevel($strPos[3]);
	      }

				$this->spawns[] = new Position(intval($strPos[0]), intval($strPos[1]), intval($strPos[2]), $main->getServer()->getLevelByName($strPos[3]));

        }

       $l = explode(":",$capture["p1"]);

        $this->p1 = new Position($l[0],$l[1],$l[2],$main->getServer()->getLevelByName($l[3]));

        $l = explode(":",$capture["p2"]);

        $this->p2 = new Position($l[0],$l[1],$l[2],$main->getServer()->getLevelByName($l[3]));

    }

    public function inCapture(Player $player) : bool {

        

       $l = $player->getPosition();

        $x = $l->getX();

        $z = $l->getZ();

        $y = $l->getY();

        $p1 = $this->p1;

        $p2= $this->p2;

        $minx = min($p1->getX(),$p2->getX());

        $maxx = max($p1->getX(),$p2->getX());

        $minz = min($p1->getZ(),$p2->getZ());

        $maxz = max($p1->getZ(),$p2->getZ());

        $miny = min($p1->getY(),$p2->getY());

        $maxy = max($p1->getY(),$p2->getY());

        

        return ($minx <= $x && $x <= $maxx && $minz <= $z && $z <= $maxz && $miny <= $y && $y <= $maxy);

   }
   public function startGame(){

        $task = new GameTimer($this->plugin,$this);

        $handler = $this->plugin->getScheduler()->scheduleRepeatingTask($task,20);

        $task->setHandler($handler);

        $this->timer = $task;

    }

     public function resetAllPlayers(){

     

        foreach ($this->players as $player => $time){

            $p = $this->plugin->getServer()->getPlayer($player);

            

           if($this->plugin->kothplayer->isInGame($p)){

                $this->plugin->kothplayer->setInGame($p, false);
                $this->teleportFinish($p);

            
            }

            unset($this->players[$player]);

        }

    }
    public function teleportFinish(Player $player){
        if($this->plugin->getConfig()->get("teleport-type") === "default"){
            $spawn = $this->plugin->getServer()->getDefaultLevel()->getSpawnLocation();
        }elseif($this->plugin->getConfig()->get("teleport-type") === "world-spawn"){
                $spawn = $event->getPlayer()->getLevel()->getSpawnLocation();
                }elseif($this->plugin->getConfig()->get("teleport-type") === "cords"){
                        $cords = explode(", ", $this->plugin->getConfig()->get("coordinates"));
                        $x = $cords[0];
                        $y = $cords[1];
                        $z = $cords[2];
            $spawn = new Position($x, $y, $z, $this->plugin->getServer()->getLevelByName($this->plugin->getConfig()->get("world")));
	}
        $player->teleport($spawn);
	  //todo add error messages.
        
    
    }

    public function resetCapture(Player $player){

        if (isset($this->players[$player->getName()])){

            $this->players[$player->getName()] = $this->plugin->getData("capture_time");

        }

    }

    public function resetGame(){

        $this->resetAllPlayers();

        $this->players = [];

        $this->running = false;
	     $this->plugin->started = false;
	    $this->plugin->stopped = true;

        $timer = $this->timer;

        if ($timer instanceof Task && !$timer->getHandler()->isCancelled()) $timer->getHandler()->cancel();

        $this->timer = null;

    }

    public function isRunning() : bool {

        return $this->running;

    }

    public function checkPlayers(){

        foreach ($this->players as $player => $time){

            $p = $this->plugin->getServer()->getPlayer($player);

            if ($p instanceof Player){

              if($this->inCapture($p)){

                    $time = --$this->players[$player];

                    $this->sendProgress($p,$time);

                    if ($time < 1){

                        $this->won($p);

                    }

                }

            }else{

                unset($this->players[$player]);

            }

        }

    }
	public function preStart(){

        $task = new PreGameTimer($this->plugin,$this);

        $handler = $this->plugin->getScheduler()->scheduleRepeatingTask($task,20);

        $task->setHandler($handler);

        $this->timer = $task;

        $this->running = true;
		$this->plugin->started = true;
		$this->plugin->stopped = false;

    }

    public function won(Player $player){

        $prefix = $this->plugin->prefix();

        $msg = $this->plugin->getData("win");

        $msg = str_replace("{player}", $player->getName(), $msg);
	$msg = str_replace("{faction}", $this->plugin->getFaction($player), $msg);    
        $msg = $prefix.$msg;
if($this->plugin->msg->get("discord-support")){
        $this->plugin->discord->sendToDiscord(KothLanguage::getMessage("KOTH_ENDED_DISCORD", [
            "{winner}" => $player->getName(),
            "{line}" => "\n",
            "{faction}" => $this->plugin->getFaction($player)

        ]), $this->plugin->msg->get("webhook-url"), $this->plugin->msg->get("bot-displayname"));
        
}
        $factionMode = ($this->plugin->fac instanceof FactionMain ? " " . KothLanguage::getMessage("KOTH_WON_FACTIONMODE", [
        "{faction}" => $this->plugin->getFaction($player),
        "{player}" => $player->getName()]) : "");
        $this->plugin->getServer()->broadcastMessage(KothLanguage::getMessage("KOTH_WIN_MESSAGE", [
            "{factionmode}" => $factionMode
        ]));
           
        $this->giveRewards($player);

        $this->endGame();

    }

    public function removePlayer(Player $player){

        if (isset($this->players[$player->getName()])) unset($this->players[$player->getName()]);
     
      

        if($this->plugin->kothplayer->isInGame($player)){
		 $this->teleportFinish($player);

            $this->plugin->kothplayer->setInGame($player, false);
         }
			if($this->plugin->msg->get("discord-support")){

            $this->plugin->discord->sendToDiscord(KothLanguage::getMessage("KOTH_PLAYER_QUIT", [ 
            "{player}" => $player->getName()
            ]), $this->plugin->msg->get("webhook-url"), $this->plugin->msg->get("bot-displayname"));
               
    }
    }


    public function sendProgress(Player $p, int $time){
        $max = $this->plugin->getData("capture_time");

        $time = $this->plugin->getData("capture_time") - $time;

        $percent = (($time / $max)*100).'%';

       

    
$this->sendTip(KothLanguage::getMessage("CAPTURING_POINT_TIP", [
    "{percent}" => $percent,
    "{player}" => $p->getName()
]));
     

 if($percent == 1){
$this->addTitlePercentage($p, $percent);
  

 }

    if($percent == 25){

      
       $this->addTitlePercentage($p, $percent);
    }

    if($percent == 50){

    
    $this->addTitlePercentage($p, $percent);
    }

    if($percent == 75){

      
      $this->addTitlePercentage($p, $percent);
    }

    if($percent > 94){

     
      $this->addTitlePercentage($p, $percent);
      
    }
    }

    public function addTitlePercentage(Player $player, $percent){
        Server::getInstance()->broadcastMessage(KothLanguage::getMessage("CAPTURING_POINT_MESSAGE", [
            "{percent}" => $percent,
            "{player}" => $player->getName()
            ]));
    }

    public function endGame(){

        foreach ($this->players as $player => $time){

            $p = $this->plugin->getServer()->getPlayer($player);

           if($this->plugin->kothplayer->isInGame($p)){
            $this->plugin->kothplayer->setInGame($p, false);
            }

            
             $this->teleportFinish($p);
$p->sendMessage(KothLanguage::getMessage("KOTH_ENDED_BROADCASTED"));

            unset($this->players[$player]);

        }

        $this->resetGame();

    }

    public function sendPopup(string $msg){

        foreach ($this->players as $player => $time){

            $p = $this->plugin->getServer()->getPlayer($player);

            if ($p instanceof Player) $p->sendPopup($msg);

        }

    }

     public function sendTip(string $msg){

        foreach ($this->players as $player => $time){

            $p = $this->plugin->getServer()->getPlayer($player);

            if ($p instanceof Player) $p->sendTip($msg);

        }

    }


     public function sendTitle(string $title, string $subtitle){

        foreach ($this->players as $player => $time){

            $p = $this->plugin->getServer()->getPlayer($player);

            if ($p instanceof Player) $p->addTitle($title, $subtitle);

        }

    }

    public function giveRewards(Player $player){
        $rewards = $this->plugin->getRewards();
	    $faction = $this->plugin->getFaction($player);
        $name = $player->getName();
        foreach ($rewards as $key => $reward){
            $reward = str_replace("{player}", $name, $reward);
		$reward = str_replace("{faction}", $faction, $reward);
            $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),$reward);
        }
    }
    public function addPlayer(Player $player){

        $this->players[$player->getName()] = $this->plugin->getData("capture_time");

        $this->sendRandomSpot($player);
if(!$this->plugin->kothplayer->isInGame($player)){
            $this->plugin->kothplayer->setInGame($player, true);
        }
if($this->plugin->msg->get("discord-support")){
            $this->plugin->discord->sendToDiscord(KothLanguage::getMessage("KOTH_JOIN", [
                "{line}" => "\n",
                "{player}" => $player->getName()
            ]), $this->plugin->msg->get("webhook-url"), $this->plugin->msg->get("bot-displayname"));
               
}
	}
	public function sendRandomSpot(Player $player) : bool{
		$old = $this->spawns[array_rand($this->spawns)];
		$player->teleport($old);
		return true;
	}
}
