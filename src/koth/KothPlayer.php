<?php 
namespace koth;
use pocketmine\Player;

class KothPlayer{
	
public $kothingame = [];

    public function isInGame(Player $player): bool{
        if(isset($this->kothingame[strtolower($player->getName())])){
	    return $this->kothingame[strtolower($player->getName())];
    }
}
    public function setInGame(Player $player, bool $kothingame){
        $this->kothingame[strtolower($player->getName())] = $kothingame;
    }
}
