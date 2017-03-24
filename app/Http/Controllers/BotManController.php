<?php

namespace App\Http\Controllers;

use App\Conversations\ExampleConversation;
use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Mpociot\BotMan\Facebook\Element;
use Mpociot\BotMan\Facebook\GenericTemplate;

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
            $data = json_decode($res->getBody());
            $count = $data->hits->found;

            if($count == 0){
                $bot->reply('No Doctor Found with Name or Registration Number '.$name);
            }
            else{
                $elements = [];
                foreach ($data->hits->hit as $doctor){
                    array_push($elements,
                        Element::create($doctor->fields->name)
                            ->subtitle($doctor->fields->qualification.chr(10).'Test')
                            ->image('http://www.lifeline.ae/lifeline-hospital/wp-content/uploads/2015/02/LLH-Doctors-Male-Avatar-300x300.png')
                        );
                }
                $bot->reply(GenericTemplate::create()
                    ->addElements($elements)
                );
            }
        });

        $botman->listen();
    }

    public function test(Request $request)
    {
        $name = $request->input('name');
        $client = new Client();
        $res = $client->request('GET', 'https://6ujyvhcwe6.execute-api.eu-west-1.amazonaws.com/prod?q='.$name);
        $data = json_decode($res->getBody());
        $count = $data->hits->found;
        dd($data);


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
