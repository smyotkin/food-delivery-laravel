<?php

use App\Notifications\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return view('welcome');
});

Route::post('/telegram-webhook', function () {
    $command = 'start';
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data) || !isset($data['message']['text'])) {
        return;
    }

    if (@$data['message']['from']['is_bot']) {
        return;
    }

    if (!empty($command) && !preg_match("/^\/{$command}/i", $data['message']['text']) && $data['message']['entities']['type'] == 'bot_command') {
        return;
    }

    $chatId = $data['message']['chat']['id'] ?? false;

    if ($chatId) {
        Notification::route('telegram', $chatId)
            ->notify(new Telegram([
                'msg' => 'Ваш ID чата: ' . $chatId,
            ]));
    }
});
