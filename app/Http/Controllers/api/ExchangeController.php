<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class ExchangeController extends Controller
{
    public function index()
    {
        return 'Hello';
    }

    public function currency_exchange(Request $request)
    {
        //讀取系統資料
        $configFile = config_path('rate.json');
        $configData = json_decode(file_get_contents($configFile), true);
        //驗證讀資料是否正確
        if(!is_array($configData))
        {
            return response()->json(['errors' => '讀取json檔異常'], 500);
        }

        $validData = array_keys($configData['currencies']);
        //驗證資料
        $verifyStatue = $this->checkData($request,$validData);
        if($verifyStatue !== true)
        {
            return response()->json(['errors' => $verifyStatue], 400);
        }

        //獲取GET參數
        $exchangeData = ['source'=>request('source'), 'target'=>request('target'), 'amount'=>request('amount')];
        $amount = $this->exchamge($exchangeData,$configData);
        //轉成需要輸出的樣式
        $amount = $this->formatAmount($amount);
        $data = [
            'msg' => 'success',
            'amount' => $amount,
        ];
        return $data;
    }

    /**
     * 驗證輸入資料
     *
     * @param object 輸入的參數
     * @param array  驗證的範圍
     * @return bool|array  true:驗證通過|array:驗證失敗
     */
    private function checkData(Request $request,$validData=[])
    {
        // 定義驗證規則
        $rules = [
            'source' => ['required',Rule::in($validData)],
            'target' => ['required',Rule::in($validData)],
            'amount' => ['required','regex:/^\$\d{1,3}(,\d{3})*(\.\d+)?$/'],
        ];
        
        // 定義自定義錯誤訊息
        $messages = [
            'source' => '非法參數',
            'target' => '非法參數',
            'amount' => '非法參數',
        ];
        // 驗證 GET 參數
        $validator = Validator::make($request->all(), $rules, $messages);
        // 驗證失敗
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        //驗證負數 
        $amount = str_replace(['$',','], "", request('amount'));
        if($amount <= 0)
        {
            return ['amount'=>['非法參數1']];
        }
        return true;
    }

    /**
     * 金額轉換
     *
     * @param array  exchangeData 換匯資料
     * @param array  rateData     匯率資料
     * @return double 轉換後的金額
     */
    private function exchamge($exchangeData=[],$rateData=[])
    {
        $source = $exchangeData['source'];
        $target = $exchangeData['target'];
        $amount = str_replace(['$',','], "", $exchangeData['amount']);
        $rate = $rateData['currencies'][$source][$target];
        return $amount*$rate;
    }

    /**
     * 金額格式化
     * 
     * @param  double amount  轉換後的金額
     * @return string         輸出要的金額格式字串
     */
    private function formatAmount($amount=0)
    {
        $formattedNumber = '$' . number_format($amount, 2);
        return $formattedNumber;
    }    
}
