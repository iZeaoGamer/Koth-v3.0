<?php

/**

 * Created by PhpStorm

 * User: 

 * Date: 

 * Time: 

 */

namespace koth;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use iZeaoGamer\ZectorPEPlayer\ZectorPlayer;

use pocketmine\Server;



class KothMain extends PluginBase

{

    public $msg;

    private $c;

    public $arena = null;

    private $fac;

    public $kothtime = 10800;

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

        $this->msg = new Config($this->getDataFolder()."config.yml",Config::YAML,[

            "capture_time" => 100,
			"player-creation" => false,

            "game_time" => 10,
			"event_time" => 86400,
			
			"discord-support" => true,
			
			"webhook-url" => "https://discordapp.com/api/webhooks/discord-webhook-goes-here",

			"bot-displayname" => "Koth Event",
            "reset_capture_progress" => true,

            "prefix" => "[KOTH] ",

            "starting" => "Game starting in {sec}. Join Game now! (/koth join)",

            "begin" => "KOTH Started! (/koth join)",

            "joined" => "Joined game successfully!, Be the first to capture the area now!",

            "win" => "{faction} | {player} has captured the area and won the event!",

            "end" => "Event has ended!",

            "not_running" => "There is no KOTH event running at the moment!",

            "still_running_title" => "KOTH Running!",

            "still_running_sub" => "Join now with /koth join !",

            "progress" => "Capturing... {percent}%",

            "end_game" => "Game Ended!",

            "game_bar" => "KOTH Time Left: {time}",

            "rewards" => [

                "givemoney {player} 1000",

                "give {player} diamond 2"

        ]

        ]);

          // $this->getServer()->getCommandMap()->register("koth", new KothCommand("koth",$this));

        $this->c = new Config($this->getDataFolder()."arena.yml", Config::YAML);
$all = $this->c->getAll();
        if (isset($all["spawns"]) && $all["p1"] && $all["p2"]){

         if(!file_exists($this->getDataFolder() . "kothinfo.yml")){

            $this->koth = new Config($this->getDataFolder() . "kothinfo.yml", Config::YAML);

            $this->koth->set("eventtime", 10800);

            $this->koth->save();

            $this->getLogger()->info("Koth timer config has been generated successfully.");
       
        }

      //  $this->discord = Server::getInstance()->getPluginManager()->getPlugin("ZectorPEPlayer");
	  $this->discord = new DiscordIntergration();

        $this->koth = new Config($this->getDataFolder() . "kothinfo.yml");

        $this->getLogger()->notice("KOTH Plugin Enabled!");
       

        $all = $this->c->getAll();

      
            $this->arena = new KothArena($this,$all["spawns"],["p1" => $all["p1"], "p2" => $all["p2"]]);

            $this->getLogger()->info("KOTH Arena Loaded Successfully");
            $this->getScheduler()->scheduleDelayedRepeatingTask(new KothTimer($this, $this->arena), 20 * 1, 20 * 1);

        }else{

            $this->getLogger()->alert("No arena setup! Please set one up!");

        }

        //Register Listener

        $this->getServer()->getPluginManager()->registerEvents(new KothListener($this),$this);

      

        

        $this->getServer()->getCommandMap()->register("koth", new KothCommand("koth",$this));



        $this->fac = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");

        if ($this->fac == null) $this->getLogger()->critical("FactionsPro Plugin not found... Disabled {faction} support!");

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

            //$this->setStarted(false);

        }

    }

    public function getRewards() : array {

        $all = $this->msg->getAll();

        return isset($all["rewards"]) ? $all["rewards"] : [];

    }

    public function setPoint(Player $player, $type){

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