<?php 
namespace koth;
use pocketmine\Player;

class KothPlayer{
	
public $kothingame = [];

    public function isInGame(Player $player): bool{

	    return (isset($this->kothingame[strtolower($player->getName())]) ? $this->kothingame[strtolower($player->getName())] : false);
    }
    public function setInGame(Player $player, bool $kothingame){
        $this->kothingame[strtolower($player->getName())] = $kothingame;
    }
}
