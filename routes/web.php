<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CswdController;
use App\Http\Controllers\CdrrmoController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\DisasterController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ReportAccidentController;
use App\Http\Controllers\EvacuationCenterController;
use App\Http\Controllers\EvacueeController;
use App\Http\Controllers\UserAccountsController;

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('/', 'authUser')->name('login');
    Route::get('/logout', 'logout')->name('logout.user');

    Route::group(['middleware' => 'check.login'], function () {
        Route::view('/', 'authentication/authUser')->name('home');
    });
});

Route::group(['prefix' => 'resident', 'middleware' => 'guest'], function () {

    Route::group(['prefix' => 'reportAccident'], function () {
        Route::controller(ReportAccidentController::class)->group(function () {
            Route::get('/viewReport', 'displayGReport')->name('accident.report.resident');
            Route::post('/addReport', 'addAccidentReport')->name('report.accident.resident');
        });
    });

    Route::controller(ResidentController::class)->group(function () {
        Route::get('/eligtasGuideline', 'residentEligtasGuideline')->name('guideline.resident');
        Route::get('/eligtasGuideline/guideline/{guidelineId}', 'residentEligtasGuide')->name('guide.resident');
        Route::get('/evacuationCenter', 'residentEvacuationCenter')->name('evacuation.center.resident');
        Route::get('/reportAccident', 'residentReportAccident')->name('display.report.accident.resident');
        Route::get('/hotlineNumber', 'residentHotlineNumber')->name('hotline.number.resident');
        Route::get('/about', 'residentAbout')->name('about.resident');
    });
});

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'cdrrmo'], function () {
        Route::group(['prefix' => 'eligtasGuideline'], function () {
            Route::controller(GuidelineController::class)->group(function () {
                Route::get('/', 'eligtasGuideline')->name('guideline.cdrrmo');
                Route::post('/guide/addGuide{guidelineId}', 'addGuide')->name('add.guide.cdrrmo');
                Route::put('/guide/updateGuide/{guideId}', 'updateGuide')->name('update.guide.cdrrmo');
                Route::get('/guide/removeGuide/{guideId}', 'removeGuide')->name('remove.guide.cdrrmo');

                Route::get('/guide/{guidelineId}', 'guide')->name('guide.cdrrmo');
                Route::post('/guideline/addGuideline', 'addGuideline')->name('add.guideline.cdrrmo');
                Route::put('/guideline/updateGuideline/{guidelineId}', 'updateGuideline')->name('update.guideline.cdrrmo');
                Route::get('/guideline/removeGuideline/{guidelineId}', 'removeGuideline')->name('remove.guideline.cdrrmo');
            });
        });

        Route::group(['prefix' => 'reportAccident'], function () {
            Route::controller(ReportAccidentController::class)->group(function () {
                Route::get('/viewReport', 'displayCReport')->name('accident.report.cdrrmo');
                Route::post('/addReport', 'addAccidentReport')->name('report.accident.cdrrmo');
                Route::post('/approveReport/{reportId}', 'approveAccidentReport')->name('approve.accident.report.cdrrmo');
                Route::delete('/removeReport/{reportId}', 'removeAccidentReport')->name('remove.accident.report.cdrrmo');
            });
        });

        Route::controller(CdrrmoController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard.cdrrmo');
            Route::get('/hotlineNumbers', 'hotlineNumbers')->name('hotline.number.cdrrmo');
            Route::get('/reportAccident', 'reportAccident')->name('display.report.accident.cdrrmo');
            Route::get('/about', 'about')->name('about.cdrrmo');
        });
    });

    Route::group(['prefix' => 'cswd'], function () {

        Route::group(['prefix' => 'eligtasGuideline'], function () {
            Route::controller(GuidelineController::class)->group(function () {
                Route::get('/', 'eligtasGuideline')->name('guideline.cswd');
                Route::post('/guide/addGuide{guidelineId}', 'addGuide')->name('add.guide.cswd');
                Route::put('/guide/updateGuide/{guideId}', 'updateGuide')->name('update.guide.cswd');
                Route::get('/guide/removeGuide/{guideId}', 'removeGuide')->name('remove.guide.cswd');

                Route::get('/guide/{guidelineId}', 'guide')->name('guide.cswd');
                Route::post('/guideline/addGuideline', 'addGuideline')->name('add.guideline.cswd');
                Route::put('/guideline/updateGuideline/{guidelineId}', 'updateGuideline')->name('update.guideline.cswd');
                Route::get('/guideline/removeGuideline/{guidelineId}', 'removeGuideline')->name('remove.guideline.cswd');
            });
        });

        Route::group(['prefix' => 'evacuationCenter'], function () {
            Route::controller(EvacuationCenterController::class)->group(function () {
                Route::get('/viewEvacuationCenter', 'evacuationCenterList')->name('evacuation.center.cswd');
                Route::get('/evacuationCenterDetails/{evacuationId}', 'getEvacuationCenterDetails')->name('evacuation.center.detail.cswd');
                Route::post('/registerEvacuation', 'registerEvacuationCenter')->name('register.evacuation.center.cswd');
                Route::put('/updateEvacuation/{evacuationId}', 'updateEvacuationCenter')->name('update.evacuation.center.cswd');
                Route::delete('/removeEvacuation/{evacuationId}', 'removeEvacuationCenter')->name('remove.evacuation.center.cswd');
            });
        });
        
        Route::group(['prefix' => 'evacuee'], function () {
            Route::controller(EvacueeController::class)->group(function () {
                Route::get('/getEvacueeInfo', 'loadEvacueeTable')->name('get.evacuee.info.cswd');
                Route::post('/recordEvacueeInfo', 'recordEvacueeInfo')->name('record.evacuee.cswd');
                Route::put('/updateEvacueeInfo{evacueeId}', 'updateEvacueeInfo')->name('update.evacuee.info.cswd');
                Route::patch('/updateEvacueeDateOut', 'updateEvacueeDateOut')->name('update.evacuee.dateout.cswd');
            });
        });

        Route::controller(CswdController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard.cswd');
            Route::get('/evacuee', 'manageEvacueeInformation')->name('manage.evacuee.record.cswd');
            Route::get('/disaster', 'disaster')->name('disaster.cswd');
            Route::get('/evacuationManage', 'evacuationManage')->name('manage.evacuation.cswd');
            Route::get('/evacuationCenter', 'evacuationCenter')->name('evacuation.center.locator.cswd');
        });
    });

    Route::group(['prefix' => 'accounts'], function () {
        Route::controller(UserAccountsController::class)->group(function () {
            Route::get('/userProfile/{userId}', 'displayUserDetails')->name('user.details');
            Route::put('/restrictUser/{userId}', 'restrictUserAccount')->name('restrict.account');
            Route::put('/unRestrictUser/{userId}', 'unRestrictUserAccount')->name('unrestrict.account');
            Route::put('/editAccount/{userId}', 'updateUserAccount')->name('update.account');
            Route::get('/', 'userProfile')->name('display.user.profile');
            Route::get('/userAccounts', 'userAccounts')->name('display.user.accounts');
        });
    });
    
});
