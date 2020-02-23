<?php 
namespace koth;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Message;

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
