<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class ExchangeController extends Controller
{
    public function index()
    {
        return 'Hello';
    }

    public function currency_exchange()
    {
        $configFile = config_path('rate.json');
        $configData = json_decode(file_get_contents($configFile), true);
        // 獲取 GET 參數
        $source = request('source');
        $target = request('target');
        $amount = request('amount');

        $data = [
            'msg' => 'success',
            'configData' =>  $configData,
            'source' =>  $source,
            'target' =>  $target,
            'amount' =>  $amount,
        ];
        return $data;
    }
}
