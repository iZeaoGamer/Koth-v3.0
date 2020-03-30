<?php 
namespace koth;

use pocketmine\Player;

class KothPlayer extends Player{
public $kothingame = false;
	public function isInGame(): bool{
		return $this->kothingame;
    }
    public function setInGame(bool $kothingame){
        $this->kothingame = $kothingame;
    }
}
