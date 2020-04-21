<?php


namespace koth;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use pocketmine\Server;

use koth\arenas\KothArena;
use koth\listeners\KothListener;
use koth\commands\KothCommand;
use koth\lang\KothLanguage;
use koth\intergrations\DiscordIntergration;
use koth\tasks\KothTimer;




class KothMain extends PluginBase

{

    public $msg;

    private $c;

    public $arena = null;

    public $fac;

    public $discord;

       public $stopped = true;

    public $started = false;

    

    public function hasStartedKoth(): bool{

        return $this->started;

    }

    public function setStartKoth(bool $started){

        $this->started = $started;

    }

    public function hasStoppedKoth(): bool{

        return $this->stopped;

    

    }

    public function setStopKoth(bool $stopped){

        $this->stopped = $stopped;

    }

    public function onEnable() : void{

        @mkdir($this->getDataFolder());
	    $this->koth = new Config($this->getDataFolder() . "kothinfo.yml", Config::YAML);

        $this->saveDefaultConfig();
        $this->msg = $this->getConfig();
        $this->lang = new KothLanguage($this);

      

        $this->c = new Config($this->getDataFolder()."arena.yml", Config::YAML);
$all = $this->c->getAll();
        if (isset($all["spawns"]) && $all["p1"] && $all["p2"]){

		
         if(!file_exists($this->getDataFolder() . "kothinfo.yml")){

         

            $this->koth->set("eventtime", $this->getEventTime());

            $this->koth->save();

            $this->getLogger()->debug("Koth timer config has been generated successfully.");
       
        }
if($this->msg->get("discord-support")){
  
	  $this->discord = new DiscordIntergration();
}

        $this->koth = new Config($this->getDataFolder() . "kothinfo.yml");

        $this->getLogger()->notice("KOTH Plugin Enabled!");
       

        $all = $this->c->getAll();

     
            $this->arena = new KothArena($this,$all["spawns"],["p1" => $all["p1"], "p2" => $all["p2"]]);

            $this->getLogger()->notice("KOTH Arena Loaded Successfully");
            $this->getScheduler()->scheduleDelayedRepeatingTask(new KothTimer($this, $this->arena), 20 * 1, 20 * 1);

        }else{

            $this->getLogger()->alert("No arena setup! Please set one up!");

        }

        //Register Listener

        $this->getServer()->getPluginManager()->registerEvents(new KothListener($this),$this);

      

        

        $this->getServer()->getCommandMap()->register("koth", new KothCommand("koth",$this));



        $this->fac = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");

        if ($this->fac == null){
$this->getLogger()->critical("FactionsPro Plugin not found... Disabled {faction} support!");
	}else{
         $this->getLogger()->notice("FactionsPro plugin found. Enabled {faction} support.");

        }
    }
    public function getLang(): KothLanguage{
        return $this->lang;
    }

    public function getFaction(Player $player){

        return $this->fac == null ? "" : $this->fac->getPlayerFaction($player->getName());

    }

    public function getKothTimer() : int{

        return $this->koth->get("eventtime");

    }

    public function setKothTimer(int $kothtime){

        $this->koth->set("eventtime", $kothtime);

        $this->koth->save();

    }
	public function getEventTime() : int{
		return $this->msg->get("event_time");
	}
	public function setEventTime(int $eventtime){
		$this->msg->set("event_time", $eventtime);
		$this->msg->save();
	}
	public function setGameTime(int $gametime){
		$this->msg->set("game_time", $gametime);
		$this->msg->save();
	}
	public function getGameTime(){
	       return $this->msg->get("game_time");
	}
	
     public function startKoth(){

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            $arena->preStart();

        }

    }

    public function stopKoth(){

          $arena = $this->arena;

        if ($arena instanceof KothArena) {

            $arena->resetGame();

        }

    }

     public function sentToKoth(KothPlayer $player, bool $leave = false){

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            if ($arena->isRunning()){
				if(!$leave){

                $arena->addPlayer($player);
				}else{
					$arena->removePlayer($player);
    }
	
		

        }

    }
	 }

    public function onDisable() : void{

        $arena = $this->arena;

        if ($arena instanceof KothArena){

            $arena->resetGame();

             $this->setStopKoth(true);

            $this->setStartKoth(false);

         

        }

    }
    public function getKothArena(): KothArena{
        return $this->arena;
    }

    public function getRewards() : array {

        $all = $this->msg->getAll();

        return isset($all["rewards"]) ? $all["rewards"] : [];

    }

    public function setPoint(Player $player, string $type){

        $save = $player->getX().":".$player->getY().":".$player->getZ().":".$player->getLevel()->getName();

        $all = $this->c->getAll();

        if ($type === "spawn"){

            $all["spawns"][] = $save;

        }else{

            $all[$type] = $save;

        }

        $this->c->setAll($all);

        $this->c->save();

    }

    public function startArena() : bool {

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            $arena->preStart();

            return true;

        }

        return false;

    }

    public function forceStop() : bool {

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            $arena->resetGame();

            return true;

        }

        return false;

    }

    public function isRunning() : bool {

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            if ($arena->isRunning()) return true;

        }

        return false;

    }

    public function sendToKoth(Player $player) : bool {

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            if ($arena->isRunning()){

                $arena->addPlayer($player);

                return true;

            }

        }

        return false;

    }

    public function prefix() : string {

        $all = $this->msg->getAll();

        return isset($all["prefix"]) ? $all["prefix"] : "[KOTH] ";

    }

    public function removePlayer(Player $player){

        $arena = $this->arena;

        if ($arena instanceof KothArena) {

            $arena->removePlayer($player);

        }

        return false;

    }

    public function getData($type) : string {

        $all = $this->msg->getAll();

        return isset($all[$type]) ? $all[$type] : "";

    }

}
