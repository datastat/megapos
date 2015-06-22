<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Config;

\Route::group(['prefix' => 'megapos', 'as' => 'megapos.'], function()
{

    /**
     *
     * routes for testing, disable for production
     *
     */

    if(Config::get('megapos.enable_test_routes')){

        \Route::get('test', ['as' => 'test', function()
        {
            return view('megapos::index');
        }]);

        \Route::post('init', ['as' => 'init', function()
        {
            $params = \Request::all();
            $megapos = App::make('megapos');
            $megapos->init($params);
        }]);

        \Route::post('cancel', ['as' => 'cancel', function()
        {
            $txId = \Request::get('transaction_id');
            $megapos = App::make('megapos');
            $megapos->cancel($txId);
        }]);

        \Route::post('process', ['as' => 'process', function()
        {
            $params = \Request::all();
            $megapos = App::make('megapos');
            $megapos->process($params);
        }]);

        \Route::get('list-gateways', ['as' => 'list', function()
        {
            $megapos = App::make('megapos');
            $megapos->listGateways();
        }]);

    }

    /**
     *
     * routes for production, always visible
     *
     */

    \Route::get('update', ['as' => 'update', function()
    {
        $megapos = App::make('megapos');
        $megapos->update(\Request::get('txId'));
    }]);

    \Route::get('status', ['as' => 'status', function()
    {
        $megapos = App::make('megapos');
        $megapos->status(\Request::get('txId'));
    }]);



});
