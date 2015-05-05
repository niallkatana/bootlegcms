<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//TODO: sort this out a bit - we need to use these globals or get rid.
if(@$_SERVER['HTTP_HOST']){
    $applicationurl = ApplicationUrl::getApplicationUrl();
    if (!@($applicationurl->application)) {
        App::abort(404, "No Application found at url");   //chuck 404 - we can't find the app
    }

    $application = $applicationurl->application;

    $GLOBALS['applicationurl'] = serialize($applicationurl);
    $GLOBALS['application'] = serialize($application);
    //I don't like doing this but meh. Laravel likes it's
    //collections serialized for some reason.
}
else{
    $application = '';
    $applicationurl = '';
}

Route::group(array('prefix'=>@$applicationurl->folder), function () use ($application, $applicationurl) {
//dd(Request::path());
    $languages = array('en'); //TODO <<
    $locale = null;


    //we need to hunt down the right bit of the url to use for language.
    $pathArr = explode('/', Request::path());
    foreach ($pathArr as $segment) {
        if (in_array($segment, $languages)) {

            //this is our language!
            $locale = $segment;
            break;
        }
    }
    //this doesn't exists in artisan thus we have to if it here.

    if (@$_SERVER['HTTP_HOST']) {
        foreach($application->plugins as $plugin){
            App::register($plugin->service_provider);
        }
    }

    Event::fire('routes.before');

    App::setLocale($locale);

    Route::get('/upload', function () {
        return Redirect::action('PagesController@getUpload');
    });

    Route::get(Utils::cmsRoute, array('as' => 'dash', function () {
        return Redirect::action('UsersController@anyDashboard');
    }));

    Route::any(Utils::cmsRoute.'login', array('uses'=>'UsersController@anyLogin'));

    Route::group(array('prefix'=>Utils::cmsRoute), function () use ($locale) {

        Route::group(array('prefix'=>$locale), function () {


            Route::any('/', array('uses'=>'UsersController@anyDashboard'));

            Route::controller('content', 'ContentsController');

            Route::controller('template', 'TemplateController');

            Route::controller('application', 'ApplicationController');

            Route::controller('users', 'UsersController');

            Route::controller('reminders', 'RemindersController');
        });
    });

    Route::pattern('upl', '(.*)');
    Route::get('/uploads/{upl?}', function($filename = null){
        //TODO: security on this file.

        //$filename = stripslashes(str_replace('/','',$filename));

        $filename = storage_path() . '/uploads/'. $filename;
        $file = File::get($filename);
        $fileData = new \Symfony\Component\HttpFoundation\File\File($filename);
        $response = Response::make($file, 200);
        $response->headers->set('Content-Type', $fileData->getMimeType());
        return($response);
    });


    Route::any('/{slug?}', function ($slug = '/') use ($application, $applicationurl) {
        //TODO: we should really move this into PageController at some point.
        return PageController::page($slug, $application, $applicationurl);
    })->where('slug', '(.*)');
    //});

    \Event::fire('routes.after');
//    Route::controller('/', 'PageController');
});