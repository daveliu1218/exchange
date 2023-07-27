# 匯率轉換
一個簡單的API，用於轉換貨幣匯率

## 功能
- 使用者可以輸入金額和特定貨幣代碼，然後選擇要轉換的目標貨幣
- 系統會根據系統預設的匯率進行轉換，並將結果以JSON方式回傳

## 安裝、配置、啟動
1. 確認 PHP 版本是 PHP 8.1.0 或更高版本，因為本專案使用 Laravel 10 的框架
2. 確認是否安裝 Composer,因為安裝依賴項需要用到
3. 複製這個專案到本地機器,並在該專案底下執行指令
4. 安裝所需的依賴項執行 
- 指令 `composer install`
5. 自動加載
- 指令 `composer dump-autoload`
6. 緩存清除
- 指令 `php artisan cache:clear`
- 指令 `php artisan config:clear`
- 指令 `php artisan route:clear`
7. 配置.env檔案
- 指令 `cp .env.example .env`
8. 設定專案的key
- 指令 `php artisan key:generate`
9. 啟動專案
- 指令 `php artisan serve`


## 單元測試
- 單獨跑exchange的單元測試
    - 指令 `php artisan test --filter ExchangeTest`
- 全部的單元測試
    - 指令 `php artisan test` 

## 使用範例
以下是一個使用範例：
{URL}/api/doExchange?source=USD&target=TWD&amount=$12.5

## 代碼結構說明
1. 新增檔案
- 匯率轉換API實作的地方
    - app\Http\Controllers\api\ExchangeController.php
- Json檔案讀取的地方
    - config\rate.json
- UnitTest實作的地方
    - tests\Unit\ExchangeTest.php

2. 修改檔案
- API路由
    - routes\api.php
