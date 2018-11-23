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

Route::get('/polls/owned', 'PollController@owned')->name('polls.owned');
Route::get('/', 'PollController@create')->name('polls.create');
Route::get('/polls/{poll}/results', 'PollController@results')->name('polls.results');
Route::get('/polls/{poll}/resultsCSV', 'PollController@resultsCSV')->name('polls.resultsCSV');
Route::post('/polls/{poll}/open', 'PollController@open')->name('polls.open');
Route::post('/polls/{poll}/close', 'PollController@close')->name('polls.close');
Route::resource('polls', 'PollController')->except(['create']);

Route::post('/votes', 'VoteController@store')->name('votes.store');
Route::delete('/votes/{vote}', 'VoteController@destroy')->name('votes.destroy');

Auth::routes();
Route::get('/account', 'AccountController@index')->name('account');

Route::get('/about', 'PageController@about')->name('about');
Route::get('/admin', 'PageController@admin')->name('admin');
Route::post('/admin', 'PageController@adminRun')->name('admin.run');
