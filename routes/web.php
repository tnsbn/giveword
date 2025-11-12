<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home');

Route::get('/takeword', 'App\Http\Controllers\TakeWordController@index')->name('takeword');
Route::post('/takeword', 'App\Http\Controllers\TakeWordController@search')->name('search_takeword');
Route::get('/more-word', 'App\Http\Controllers\TakeWordController@loadMore')->name('more_takeword');
Route::post('/take-this-word', 'App\Http\Controllers\TakeWordController@takeThisWord')->name('take_this_word');
Route::get('/ajax-delete-word-item/{id}', 'App\Http\Controllers\TakeWordController@ajaxDeleteItem')->name('ajax_delete_takeword_item');
Route::get('/ajax-get-word-item/{id}', 'App\Http\Controllers\TakeWordController@ajaxGetItem')->name('ajax_get_takeword_item');
Route::get('/ajax-update-word-item', 'App\Http\Controllers\TakeWordController@ajaxUpdateItem')->name('ajax_update_takeword_item');

Route::get('/handbook', 'App\Http\Controllers\HandbookController@index')->name('handbook');
Route::get('/taken-words', 'App\Http\Controllers\HandbookController@takenWords')->name('taken_words');
Route::delete('/ajax-delete-handbook-item/{id}', 'App\Http\Controllers\HandbookController@ajaxDeleteItem')->name('ajax_delete_handbook_item');
Route::get('/ajax-get-handbook-item/{id}', 'App\Http\Controllers\HandbookController@ajaxGetItem')->name('ajax_get_handbook_item');
Route::get('/ajax-update-handbook-item', 'App\Http\Controllers\HandbookController@ajaxUpdateItem')->name('ajax_update_handbook_item');

Route::get('/writing', 'App\Http\Controllers\WritingController@index')->name('writing_index');
Route::post('/writing', 'App\Http\Controllers\WritingController@writing')->name('writing');

Route::get('/tag/{tag_name}', 'App\Http\Controllers\TagController@index')->name('tag');

Route::post('/profile/change-password', 'App\Http\Controllers\ProfileController@changePassword')->name('profile_password');
Route::get('/profile/change-name', 'App\Http\Controllers\ProfileController@changeName')->name('profile_name');
Route::get('/profile/update', 'App\Http\Controllers\ProfileController@updateProfile')->name('update_profile');
Route::get('/profile', 'App\Http\Controllers\ProfileController@showProfile')->name('show_profile');

Route::get('/search-fulltext', 'App\Http\Controllers\SearchController@indexFulltext')->name('index_fulltext');
Route::post('/search-fulltext', 'App\Http\Controllers\SearchController@indexFulltext')->name('search_fulltext');
Route::get('/search-scout', 'App\Http\Controllers\SearchController@indexScout')->name('index_scout');
Route::post('/search-scout', 'App\Http\Controllers\SearchController@indexScout')->name('search_scout');
Route::get('/search-elastic', 'App\Http\Controllers\SearchController@indexElastic')->name('index_elastic');
Route::post('/search-elastic', 'App\Http\Controllers\SearchController@indexElastic')->name('search_elastic');
//Route::post('/search', 'App\Http\Controllers\SearchController@search')->name('search_result');
//Route::get('/ajax-delete-search-item/{id}', 'App\Http\Controllers\SearchController@ajaxDeleteItem')->name('ajax_delete_search_item');
//Route::get('/ajax-get-search-item/{id}', 'App\Http\Controllers\SearchController@ajaxGetItem')->name('ajax_get_search_item');
//Route::get('/ajax-update-search-item', 'App\Http\Controllers\SearchController@ajaxUpdateItem')->name('ajax_update_search_item');

Route::get('/ajax-show-modal-chat', 'App\Http\Controllers\ChatController@ajaxStartChat')->name('ajax_show_modal_chat');
Route::get('/chat/load-name', 'App\Http\Controllers\ChatController@loadName')->name('chat_load_name');
Route::post('/chat/send', 'App\Http\Controllers\ChatController@send')->name('chat_send');
Route::post('/chat/receive', 'App\Http\Controllers\ChatController@receive')->name('chat_receive');

Route::get('/doc', function () {
    return view('swagger');
});

//Route::get('/run-airflow-pipe', 'App\Http\Controllers\Auth\ForgotPasswordController@runAir')->name('run_air');
Route::get('/portfolio', function () {
    return view('portfolio');
});

Route::post('/admax/set-online', 'App\Http\Controllers\Admin\AdminController@setChatOnlineStatus')->name('admin.set-chat-online-status');
Route::get('/admax/user-status', 'App\Http\Controllers\Admin\AdminController@userOnlineStatus')->name('admin.user-online-status');
Route::get('/admax', 'App\Http\Controllers\Admin\AdminController@index')->name('admin.index');

/**
 * Routes of Authentication are here.
 * If you want to override any route, write the new one below this
 */
Auth::routes();

Route::prefix('api')->group(function () {
    Route::post('/random-word', 'App\Http\Controllers\Api\HomeController@randomWord')->name('api.random-word');
    Route::post('/word-by-draw', 'App\Http\Controllers\Api\HomeController@wordByDraw')->name('api.word-by-draw');
});
