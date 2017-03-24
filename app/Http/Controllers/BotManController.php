<?php

namespace App\Http\Controllers;

use App\Conversations\ExampleConversation;
use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class BotManController extends Controller
{
	/**
	 * Place your BotMan logic here.
	 */
    public function handle()
    {
    	$botman = app('botman');
        $botman->verifyServices(env('TOKEN_VERIFY'));

        // Simple respond method
        $botman->hears('Hello', function (BotMan $bot) {
            $user = $bot->getUser();
            $bot->reply('Hello '.$user->getFirstName().' '.$user->getLastName() .':)');
        });


        $botman->hears('Who is {name}', function (BotMan $bot, $name) {
            $client = new Client();
            $res = $client->request('GET', 'https://6ujyvhcwe6.execute-api.eu-west-1.amazonaws.com/prod?q='.$name);
            $bot->reply($res);
        });

        $botman->listen();
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }
}
