<?php
ini_set('memory_limit', '200M');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include __DIR__.'/vendor/autoload.php';
use Discord\DiscordCommandClient;

$bot_token = "put your token here";
$connected_Users = array();

$discord = new DiscordCommandClient([
  'token' => $bot_token,
  'prefix' => '!dj ',
]);

$discord->on('ready', function($discord){
  echo "Ready to go", PHP_EOL;
	$discord->on('message', function($message, $discord){

  });
  $discord->on('VOICE_STATE_UPDATE', function($vs, $discord){
    global $connected_Users;
    echo "that was a voice state update", PHP_EOL;
    $userId = $vs->user_id;
    $channel = $vs->channel;
    echo $userId, PHP_EOL;
    $connected_Users[$userId] = $channel;
  });
});

$discord->registerCommand('ping', function($message){
  return "pong!";
});

$discord->registerCommand('summon', function($message, $discord){
  global $connected_Users;
  $userId = $message->author->id;
  if($connected_Users[$userId] == null){
    echo "user is not in a voice channel";
    return "You are not in a voice channel";
  }else{
    echo $discord, PHP_EOL;
    echo "Joining Voice Channel...\r\n";
    $channel = $connected_Users[$userId];
    $discord->joinVoiceChannel($channel)->then(function (VoiceClient $vc) {
        echo "Joined voice channel.\r\n";
    }, function ($e) {
        echo "There was an error joining the voice channel: {$e->getMessage()}\r\n";
    });
    return "you called?";
  }
});

$discord->run();

//use this link to add the bot
//https://discordapp.com/api/oauth2/authorize?&client_id=265335306293608449&scope=bot&permissions=3279936
?>
