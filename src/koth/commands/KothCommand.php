<?php
namespace koth\commands;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\Server;

use koth\KothMain;
use koth\lang\KothLanguage;

class KothCommand extends Command{
    private $plugin;
    public function __construct(string $name, KothMain $main)
    {
        parent::__construct($name, "");
        $this->plugin = $main;
        $this->arena = $main->arena;
        $this->player = $main->kothplayer;
    }
    public function execute(CommandSender $sender, $commandLabel, array $args){
		if(isset($args[0])){
				if(strtolower($args[0]) === "help"){
					$this->sendPlayerHelp($sender);
					return true;
				}elseif(strtolower($args[0]) === "reload"){
					$this->plugin->msg->reload();
					$sender->sendMessage(KothLanguage::getMessage("KOTH_RELOAD_MESSAGE"));
					
				
                }elseif (strtolower($args[0]) === "leave"){
                    if($sender instanceof Player){
                    if($this->plugin->isRunning()){
                           if(!$this->player->isInGame($sender)){
                        
                        $sender->sendMessage(KothLanguage::getMessage("KOTH_NOT_INGAME"));   
                        }else{
                     
                       $this->plugin->sentToKoth($sender, true);
                     
                     $sender->sendMessage(KothLanguage::getMessage("KOTH_LEFT_SUCCESS")); 
                     $this->player->setInGame($sender, false);
						 }
                        }
                        }else{
                   
                   $sender->sendMessage(KothLanguage::getMessage("NO_KOTH_EVENT", [
                       "{timer}" => gmdate("H:i:s", $this->plugin->getKothTimer())
                   ]));
                   return true;
}
 }elseif (strtolower($args[0]) === "join"){
    if($sender instanceof Player){
                    if($this->plugin->isRunning()){
                           if($this->player->isInGame($sender)){
                     
                                $sender->sendMessage(KothLanguage::getMessage("KOTH_ALREADY_INGAME"));
                    }else{
                         $this->plugin->sentToKoth($sender);
                    
                     $sender->sendMessage(KothLanguage::getMessage("KOTH_JOINED_SUCCESS"));  
                     $sender->setInGame(true);
                            }
                        }
                        }else{
                     
                      $sender->sendMessage(KothLanguage::getMessage("NO_KOTH_EVENT", [
                        "{timer}" => gmdate("H:i:s", $this->plugin->getKothTimer())
                    ]));
                    return true;
                        }
				}else if(strtolower($args[0]) === "seteventtime"){
                    if($sender instanceof Player){
					if(!isset($args[1])){
					
                    $sender->sendMessage(KothLanguage::getMessage("EVENTTIME_COMMAND_USAGE"));
                    return true;
					}
					if(!is_numeric($args[1])){
					
                    $sender->sendMessage(KothLanguage::getMessage("MUST_BE_NUMBER"));
                    return true;
					}
					
					$this->plugin->setEventTime((int)$args[1]);
                $sender->sendMessage(KothLanguage::getMessage("EVENT_SET", [
                    "{time}" => $args[1]
                ]));
                return true;

				}elseif(strtolower($args[0]) === "setgametime"){
					if(!isset($args[1])){
					
                    $sender->sendMessage(KothLanguage::getMessage("GAME_COMMAND_USAGE"));
                    return true;
					}
					if(!is_numeric($args[1])){
				
                $sender->sendMessage(KothLanguage::getMessage("MUST_BE_NUMBER"));
                return true;
					}
					$this->plugin->setGameTime((int)$args[1]);
				
					$sender->sendMessage(KothLanguage::getMessage("GAME_SET", [
                        "{time}" => $args[1]]));
                                          
					return true;

                } else if (strtolower($args[0]) === "setspawn"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    $this->plugin->setPoint($sender,"spawn");
               
               $sender->sendMessage(KothLanguage::getMessage("SET_SPAWN_SUCCESS"));  
               return true;

                } else if (strtolower($args[0]) === "pos1"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    $this->plugin->setPoint($sender,"p1");
                    $sender->sendMessage(KothLanguage::getMessage("SET_POINT_1"));
                        

                } else if (strtolower($args[0]) === "pos2"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    $this->plugin->setPoint($sender,"p2");
                    $sender->sendMessage(KothLanguage::getMessage("SET_POINT_2"));
                        
                 
                } else if (strtolower($args[0]) === "start"){
                    if (!$sender->hasPermission("koth.start")) return true;
                             if(!$this->plugin->hasStartedKoth() && !$this->plugin->isRunning()){
                        $this->plugin->startKoth();
                         $this->plugin->setStartKoth(true);
                         $this->plugin->setStopKoth(false);
                   $sender->sendMessage(KothLanguage::getMessage("KOTH_START_EXECUTED"));
            
                             }else{
                                $sender->sendMessage(KothLanguage::getMessage("ALREADY_STARTED"));
                                    
                             }     

                } else if (strtolower($args[0]) === "stop"){
                    if (!$sender->hasPermission("koth.stop")) return true;
                             if(!$this->plugin->hasStoppedKoth() && $this->plugin->isRunning()){
                         $this->plugin->stopKoth();
                         $this->plugin->setStopKoth(true);
                         $this->plugin->setStartKoth(false);
                  
                $sender->sendMessage(KothLanguage::getMessage("KOTH_STOPPED_EXECUTED"));     
                }else{
                                 $sender->sendMessage(KothLanguage::getMessage("ALREADY_STOPPED"));
                                    
                             return true;
                             }
                } else{
                  if(!isset($args[0])) $this->sendPlayerHelp($sender);
                    if ($sender->isOp()) $this->sendAdminHelp($sender);
                    if (!$sender->isOp()) $sender->sendMessage(KothLanguage::getMessage("KOTH_JOIN_MESSAGE"));

        } 
            }else{
          		if(!isset($args[0])) $this->sendPlayerHelp($sender);
                if ($sender->isOp()) $this->sendAdminHelp($sender);
                if (!$sender->isOp()) $sender->sendMessage(KothLanguage::getMessage("KOTH_JOIN_MESSAGE"));
            }
        }else{
            if (isset($args[0])){
                if (strtolower($args[0]) === "start"){
                  
                             if(!$this->plugin->hasStartedKoth() && !$this->plugin->isRunning()){
                         $this->plugin->startKoth();
                         $this->plugin->setStartKoth(true);
                         $this->plugin->setStopKoth(false);
                         $sender->sendMessage(KothLanguage::getMessage("KOTH_START_EXECUTED"));
                             }else{
                                $sender->sendMessage(KothLanguage::getMessage("ALREADY_STARTED"));
                             }

                } else if (strtolower($args[0]) === "stop"){
                             if(!$this->plugin->hasStoppedKoth() && $this->plugin->isRunning()){
                         $this->plugin->stopKoth();
                        $this->plugin->setStopKoth(true);
                         $this->plugin->setStartKoth(false);
                         $sender->sendMessage(KothLanguage::getMessage("KOTH_STOPPED_EXECUTED"));     
                             }else{
                                $sender->sendMessage(KothLanguage::getMessage("ALREADY_STOPPED"));
                             }
                           return true;
}
}
            $sender->sendMessage(KothLanguage::getMessage("CONSOLE_MESSAGE"));
        }
      return true;
}
	public function sendPlayerHelp(CommandSender $sender){
       
        $sender->sendMessage(KothLanguage::getMessage("KOTH_PLAYER_HELP_TITLE"));
        $sender->sendMessage(KothLanguage::getMessage("KOTH_PLAYER_HELP", [
            "{line}" => "\n"]));
		}
    public function sendAdminHelp(CommandSender $sender){
        $sender->sendMessage(KothLanguage::getMessage("KOTH_ADMIN_HELP_TITLE"));
        $sender->sendMessage(KothLanguage::getMessage("KOTH_ADMIN_HELP", [
            "{line}" => "\n"]));
        
    }
}
