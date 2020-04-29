<?php 
namespace koth;
use pocketmine\Player;

class KothPlayer{
	
public $kothingame = [];

    public function isInGame(Player $player): bool{
	    return $this->kothingame[strtolower($player->getName())];
    }
    public function setInGame(Player $player, bool $kothingame){
        $this->kothingame[strtolower($player->getName())] = $kothingame;
    }
}
