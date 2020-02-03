<?php 
namespace koth;
use CortexPE\DiscordWebHookAPI\Webhook;
use CortexPE\DiscordWebHookAPI\Message;

class DiscordIntergration {
	
	 public function sendToDiscord(string $message, $chat, string $username, $avatar = null){
        $webHook = new Webhook($chat);
        $msg = new Message();
        $msg->setUsername($username);
        if($avatar !== null){
            $msg->setAvatarURL($avatar);
            
        }
        $msg->setContent($message);
        $webHook->send($msg);
    }
}