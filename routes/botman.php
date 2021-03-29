<?php
use App\Http\Controllers\BotManController;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->hears('Hi', function ($bot) {
    $bot->reply("Hello!");
    $bot->ask('Whats your name?', function($answer, $bot) {
        $bot->say('Welcome '.$answer->getText());
    });
});

$botman->hears('weather in {location}',function($bot, $location){

    $url = "http://api.weatherstack.com/current?access_key=ab9ed78f39d845046c4a1290e3e35c3b&query=".urlencode($location);

    $res = json_decode(file_get_contents($url));

    $attachment = new Image($res->current->weather_icons[0], [
        'custom_payload' => true,
    ]);
    
    $message = OutgoingMessage::create("Weather condiction: ")
                ->withAttachment($attachment);
    
    $city = 'The weather in '.$res->location->name.' , '.$res->location->country;
    $time = ' The local time is '.$res->location->localtime;
    $temp = 'The temp is: '.$res->current->temperature.' celcius';

    $bot->reply($city);
    $bot->reply($time);
    $bot->reply($temp);
    $bot->reply($message);

});


$botman->hears('news in {location}',function($bot, $location){
    $url = "https://newsapi.org/v2/top-headlines?country=".urlencode($location)."&apiKey=86b36a55ea644d25b22d9b0893febd88";

    $res = json_decode(file_get_contents($url));
    $artical = $res->articles;

    foreach ($artical as $value) {
        $bot->reply("Title: ".$value -> title." link: ".$value->url);
    }
});
