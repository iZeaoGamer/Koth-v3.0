## Koth 3.0.0-ALPHA

A customizable plugin + Mini-Game, made for Faction servers, which offers: Many configurable options, and some that have never been seen before.
![koth](media/koth.png)
## Features / Changes for KOTH-3.0.0-ALPHA / Todo list

- [x] Change how player classes generate.

- [x] A lot of configurable features you can play around with.

- [ ] Multi-Arena support

- [x] 3.0.0 brings in so many bug fixes, and new features added.

- [x] You can leave koth arenas! By typing /koth leave

- [x] Fixed teleportation bugs when using /koth join (An issue on 2.0.0)

- [x] Renamed /koth p1 to /koth pos1

- [x] Renamed /koth p2 to /koth pos2

- [x] Added Player help commands! Type /koth or /koth help to display those! (Will also include new administrative commands if the sender is opped.

- [x] Game timer is now editable in-game! Type /koth setgametime <game-time-in-seconds>

- [x] Added event timer - Will now automatically broadcast whenever a koth event is about to start.

- [x] Event Timer is now editable in-game! Type /koth seteventtime <game-in-seconds> 

- [x] Event timers + Game-timers can now be translated to seconds/ticks. You can type /koth seteventtime <game-in-seconds>, and it will tell you right away how many seconds that is in minutes. 

- [x] Configurable rewards

- [x] Add FactionsPro Support

- [x] Add Discord Support

- [x] Best customizable koth plugin ever to be made, free

- [x] Fixed koth teleportation from not teleporting them to exactly the correct world.

- [x] Fixed /koth setgametime from not updating properly.

- [x] Fixed /koth seteventtime from not updating.

- [x] Add discord virion intergration

- [x] No longer require any external plugins to run this plugin.

- [x] Add KothPlayer class, where it'll store all of the player data. At the moment, it only supports isInGame(), and setInGame(bool). We may add more to this in the future.

- [x] Allow koth worlds to load before the koth arena database get's loaded. This means you will no longer be required to use any external plugins that relate to multiworld.

- [x] Fixed Respawn position from becoming broken, and not teleporting you to the koth game upon death.

- [x] Add KothPlayer::class API documentation for developers. Done! You can check it out using this link here: https://github.com/iZeaoGamer/Koth-v3.0/wiki/KothPlayer-documentation

- [x] Revamp databases, including: Messages, and more configurable options. 

- [ ] ScoreBoard implementation

- + And so much more! Just look for yourself, it's more awesome than ever!


## Intergrations
# We support the following intergrations:
- WebHookAPI - This will allow discord support for this plugin. Author: CortexPE - This plugin doesn't require a download. It's already added to the plugin itself.
