<?php

namespace Tests\Unit;

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Config;

class ExchangeTest extends TestCase
{
    public function testBasicTest()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    //測試讀取檔案的正確性
    public function testReadConfigJosn()
    {
        //要驗證的JSON 字串
        $file = config_path('rate.json');
        $data = json_decode(file_get_contents($file),true);
        //驗證JSON key的陣列
        $expectedKey = ["currencies"];
        foreach ($expectedKey as $key) {
            $this->assertArrayHasKey($key, $data);
        }
    }
    //測試轉換數據的正確性
    public function testConvert()
    {
        //讀取匯率資料
        $configFile = config_path('rate.json');
        $configData = json_decode(file_get_contents($configFile), true);
        $validData = array_keys($configData['currencies']);
        $testData = array_rand($validData , 2);
        $source = $validData[$testData[0]];
        $target = $validData[$testData[1]];
        $rate = $configData['currencies'][$source][$target];
        //使用 Faker 來產生隨機的數字
        $faker = Faker::create();
        //取得隨機的金額
        $amount = $faker->randomFloat(2, 0, 1000000);
        $responseAmount = $amount*$rate;
        $amount = number_format($amount, 2, '.', ',');
        $response = $this->get('/api/doExchange?source='.$source.'&target='.$target.'&amount=$'.$amount);
        $response->assertJson(['msg'=>"success",'amount' => '$'.number_format($responseAmount, 2)]);
    }

    //測試錯誤的訊息正確性
    public function testInvalidSource()
    {
        $source = uniqid();
        $response = $this->get('/api/doExchange?source='.$source.'&target=TWD&amount=$1.25');
        $response->assertJson(['errors'=>[ "source"=>['非法參數']]]);
    }
    public function testInvalidTarget()
    {
        $target = uniqid();
        $response = $this->get('/api/doExchange?source=TWD&target='.$target.'&amount=$1.25');
        $response->assertJson(['errors'=>[ "target"=>['非法參數']]]);
    }
    public function testInvalidAoumtNegativeNumber()
    {
        $amount = '$-1.23';
        $response = $this->get('/api/doExchange?source=TWD&target=USD&amount='.$amount);
        $response->assertJson(['errors'=>[ "amount"=>['非法參數']]]);
    }
    public function testInvalidAoumtFormatComma()
    {
        $amount = '$1253.23';
        $response = $this->get('/api/doExchange?source=TWD&target=USD&amount='.$amount);
        $response->assertJson(['errors'=>[ "amount"=>['非法參數']]]);
    }
    public function testInvalidAoumtFormatDollarSign()
    {
        $amount = '1,253.23';
        $response = $this->get('/api/doExchange?source=TWD&target=USD&amount='.$amount);
        $response->assertJson(['errors'=>[ "amount"=>['非法參數']]]);
    }
    public function testInvalidAoumtFormatCommaPostition()
    {
        $amount = '$125,56,463.23';
        $response = $this->get('/api/doExchange?source=TWD&target=USD&amount='.$amount);
        $response->assertJson(['errors'=>[ "amount"=>['非法參數']]]);
    }
}
