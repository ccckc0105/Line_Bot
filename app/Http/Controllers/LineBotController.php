<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

use LINE\LINEBot\Event\MessageEvent\TextMessage;



class LineBotController extends Controller
{
    public function index(){
        return view('LineBot.index');
    }

    public function parrot(Request $request){

        
        Log::debug($request->header());
        Log::debug($request->input());

        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        $signature =  $request->header('x-line-signature');
        if(!$lineBot->validateSignature($request->getContent(), $signature)){
            abort(400, 'Invalid signature');
        }

        $events = $lineBot->parseEventRequest($request->getContent(), $signature);

        Log::debug($events);

        foreach ($events as $event){
            if(!($event instanceof TextMessage)){
                Log::debug('Non Text message has come');
                continue;
            }

            $replyToken = $event->getReplyToken();
            $replyText = $event->getText();
            $lineBot->replyText($replyToken, $replyText);
            
        }

    }
}
