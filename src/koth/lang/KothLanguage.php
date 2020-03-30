<?php 
namespace koth\lang;

use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

use koth\KothMain;


class KothLanguage {
    private static $messages;
    
    public function __construct(KothMain $plugin){
        $this->plugin = $plugin;
        $plugin->saveResource("messages.yml");
        self::$messages = new Config($plugin->getDataFolder() . "messages.yml", Config::YAML);
    
    }
     /**
     * @param string $key
     * @param array $replacements
     * @return string
     */
    public static function getMessage(string $key, array $replacements = []): string{
    
        $str = self::$messages->get($key);
        foreach($replacements as $find => $replace){
            $str = str_replace($find, $replace, $str);
        }
        return TextFormat::colorize($str);
    }
}