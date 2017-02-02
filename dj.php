<?php
ini_set('memory_limit', '200M');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include __DIR__.'/vendor/autoload.php';
use Discord\DiscordCommandClient;

$dj_config_file = "dj.ini";
$bot_token = "";
$connected_Users = array();
$voice_ids = array();

$config_file;
if(file_exists($dj_config_file)){
  $config_file = parse_ini_file($dj_config_file);
  $bot_token = $config_file["token"];
}else{
  if(!$file = fopen($dj_config_file, "w")){
    echo "I was unable to open: $dj_config_file";
    exit();
  }
  $fileData = "token='put your token here'\r\n";
  fwrite($file, $fileData);
  fclose($file);
  echo "ini file created please insert your bots token.\r\n";
  exit();
}

$discord = new DiscordCommandClient([
  'token' => $bot_token,
  'prefix' => '!dj ',
]);

$discord->on('ready', function($discord){
  echo "Ready to go", PHP_EOL;
	$discord->on('message', function($message, $discord){
    if($message->content == "test"){
      return "please ignore";
    }
  });
  $discord->on('VOICE_STATE_UPDATE', function($vs){
    global $connected_Users;

    echo "that was a voice state update", PHP_EOL;
    $userId = $vs->user_id;
    $channel = $vs->channel;
    $connected_Users[$userId] = $channel;
  });
});

$discord->registerCommand('ping', function($message){
  return "pong!";
});

$discord->registerCommand('summon', function($message){
  global $connected_Users;
  global $discord;

  $userId = $message->author->id;
  if($connected_Users[$userId] == null){
    echo "user is not in a voice channel";
    return "You are not in a voice channel";
  }else{
    echo "Joining Voice Channel...\r\n";
    $channel = $connected_Users[$userId];
    $discord->joinVoiceChannel($channel)->then(function (VoiceClient $vc) {
        global $voice_id;
        echo "Joined voice channel.\r\n";
        echo $channel->guild_id, PHP_EOL;
        $voice_ids[$channel->guild_id] = $vc->id;
        //$vc->playFile('exodia.ogg');
    }, function ($e) {
        echo "There was an error joining the voice channel: {$e->getMessage()}\r\n";
    });
    return "you called?";
  }
});

$discord->registerCommand("disconnect", function($message){
  global $discord;
  global $voice_ids;
  $vc_id = $voice_ids[$message->channel->guild_id];
  $vc = $discord->getVoiceClient($vc_id);
  $vc->close();
  return "Gnight";
});

$discord->run();
?>
