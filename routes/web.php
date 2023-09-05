<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\EvacueeController;
use App\Http\Controllers\DisasterController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\UserAccountsController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\EvacuationCenterController;

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('/', 'authUser')->name('login');
    Route::get('/logout', 'logout')->name('logout.user');
    Route::view('/recoverAccount', 'authentication.forgotPassword')->name('recoverAccount');
    Route::post('/findAccount', 'findAccount')->name('findAccount');
    Route::get('/resetPasswordForm/{token}', 'resetPasswordForm')->name('resetPasswordForm');
    Route::post('resetPassword', 'resetPassword')->name('resetPassword');

    Route::middleware('check.login')->group(function () {
        Route::view('/', 'authentication/authUser')->name('home');
    });
});

Route::prefix('resident')->middleware('guest')->group(function () {
    Route::name('resident.')->group(function () {
        Route::name('report.')->prefix('reportIncident')->controller(IncidentReportController::class)->group(function () {
            Route::get('/displayPendingIncidentReport', 'displayPendingIncidentReport')->name('pending');
            Route::get('/displayIncidentReport', 'displayIncidentReport')->name('display');
            Route::delete('/revertIncidentReport/{reportId}', 'revertIncidentReport')->name('revert');
            Route::patch('/updateAttempt', 'updateUserAttempt')->name('update');
            Route::post('/createIncidentReport', 'createIncidentReport')->name('accident');
            Route::post('/updateIncidentReport/{reportId}', 'updateIncidentReport')->name('incident.update');
            Route::get('/displayDangerousAreasReport', 'displayDangerousAreasReport')->name('danger.areas');
            Route::post('/reportDangerousArea', 'reportDangerousArea')->name('dangerous.area');
            Route::put('/updateDangerousArea/{reportId}', 'updateDangerousAreaReport')->name('update.danger.area');
            Route::delete('/revertDangerousAreaReport/{reportId}', 'revertDangerousAreaReport')->name('revert.danger.area.report');
        });

        Route::prefix('eligtasGuideline')->controller(GuidelineController::class)->group(function () {
            Route::get('/', 'eligtasGuideline')->name('guideline');
            Route::get('/guide/{guidelineId}', 'guide')->name('guide');
        });

        Route::controller(MainController::class)->group(function () {
            Route::get('/evacuationCenter', 'evacuationCenterLocator')->name('evacuation.center.locator');
            Route::get('/incidentReport', 'incidentReport')->name('display.incident.report');
            Route::view('/hotlineNumber', 'userpage.hotlineNumbers')->name('hotline.number');
            Route::view('/about', 'userpage.about')->name('about');
        });

        Route::controller(EvacuationCenterController::class)->group(function () {
            Route::get('/viewEvacuationCenter/{operation}', 'getEvacuationData')->name('evacuation.center.get');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('cswd')->middleware('check.cswd')->group(function () {
        Route::controller(MainController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard.cswd');
            Route::get('/evacuee', 'manageEvacueeInformation')->name('manage.evacuee.record');
            Route::view('/manageEvacuation', 'userpage.evacuationCenter.manageEvacuation')->name('manage.evacuation');
            Route::get('/evacuationCenter', 'evacuationCenterLocator')->name('evacuation.center.locator');
        });

        Route::prefix('disaster')->name('disaster.')->controller(DisasterController::class)->group(function () {
            Route::get('/disasterInformation', 'displayDisasterInformation')->name('display');
            Route::post('/createDisasterData', 'createDisasterData')->name('create');
            Route::patch('/updateDisaster/{disasterId}', 'updateDisasterData')->name('update');
            Route::patch('/archiveDisasterData/{disasterId}', 'archiveDisasterData')->name('archive');
            Route::patch('/changeDisasterStatus/{disasterId}', 'changeDisasterStatus')->name('change.status');
        });

        Route::prefix('evacuee')->name('evacuee.info.')->controller(EvacueeController::class)->group(function () {
            Route::get('/getEvacueeInfo', 'getEvacueeData')->name('get');
            Route::get('/getArchivedEvacueeInfo/{disasterInfo}', 'getArchivedEvacueeInfo')->name('get.archived');
            Route::post('/recordEvacueeInfo', 'recordEvacueeInfo')->name('record');
            Route::put('/updateEvacueeInfo/{evacueeId}', 'updateEvacueeInfo')->name('update');
        });

        Route::prefix('evacuationCenter')->name('evacuation.center.')->controller(EvacuationCenterController::class)->group(function () {
            Route::get('/viewEvacuationCenter/{operation}', 'getEvacuationData')->name('get');
            Route::post('/createEvacuationCenter', 'createEvacuationCenter')->name('create');
            Route::put('/updateEvacuation/{evacuationId}', 'updateEvacuationCenter')->name('update');
            Route::patch('/archiveEvacuation/{evacuationId}', 'archiveEvacuationCenter')->name('archive');
            Route::patch('/changeEvacuationStatus/{evacuationId}', 'changeEvacuationStatus')->name('change.status');
        });

        Route::prefix('incidentReport')->name('report.dangerous.areas.')->controller(IncidentReportController::class)->group(function () {
            Route::get('/displayDangerousAreasReport', 'displayDangerousAreasReport')->name('cswd');
            Route::post('/confirmDangerAreaReport/{dangerAreaId}', 'confirmDangerAreaReport')->name('confirm');
            Route::delete('/rejectDangerAreaReport/{dangerAreaId}', 'rejectDangerAreaReport')->name('reject');
            Route::patch('/archiveDangerAreaReport/{dangerAreaId}', 'archiveDangerAreaReport')->name('archive');
        });
    });

    Route::prefix('cdrrmo')->middleware('check.cdrrmo')->group(function () {
        Route::controller(MainController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard.cdrrmo');
        });

        Route::prefix('incidentReport')->name('report.')->controller(IncidentReportController::class)->group(function () {
            Route::get('/displayPendingIndcidentReport', 'displayPendingIncidentReport')->name('pending');
            Route::get('/displayIncidentReport', 'displayIncidentReport')->name('accident');
            Route::post('/approveIncidentReport/{reportId}', 'approveIncidentReport')->name('approve');
            Route::delete('/declineIncidentReport/{reportId}', 'declineIncidentReport')->name('decline');
            Route::patch('/archiveIncidentReport/{reportId}', 'archiveIncidentReport')->name('archive');
        });
    });

    Route::prefix('eligtasGuideline')->controller(GuidelineController::class)->group(function () {
        Route::name('guideline.')->group(function () {
            Route::get('/', 'eligtasGuideline')->name('display');
            Route::post('/guideline/createGuideline', 'createGuideline')->name('create');
            Route::post('/guideline/updateGuideline/{guidelineId}', 'updateGuideline')->name('update');
            Route::delete('/guideline/removeGuideline/{guidelineId}', 'removeGuideline')->name('remove');
        });

        Route::name('guide.')->group(function () {
            Route::get('/guide/{guidelineId}', 'guide')->name('display');
            Route::post('/guide/addGuide{guidelineId}', 'createGuide')->name('create');
            Route::post('/guide/updateGuide/{guideId}', 'updateGuide')->name('update');
            Route::delete('/guide/removeGuide/{guideId}', 'removeGuide')->name('remove');
        });
    });

    Route::controller(MainController::class)->group(function () {
        Route::post('/generateEvacueeData', 'generateExcelEvacueeData')->name('generate.evacuee.data');
        Route::get('/incidentReport', 'incidentReport')->name('display.incident.report');
        Route::view('/hotlineNumber', 'userpage.hotlineNumbers')->name('hotline.number');
        Route::view('/about', 'userpage.about')->name('about');
        Route::get('/fetchDisasterData', 'fetchDisasterData')->name('fetchDisasterData');
    });

    Route::name('account.')->controller(UserAccountsController::class)->group(function () {
        Route::post('/createAccount', 'createAccount')->name('create');
        Route::view('/userProfile', 'userpage.userAccount.userProfile')->name('display.profile');
        Route::put('/updateAccount/{userId}', 'updateAccount')->name('update');
        Route::get('/userAccount', 'userAccounts')->name('display.users');
        Route::patch('/disableAccount/{userId}', 'disableAccount')->name('disable');
        Route::patch('/enableAccount/{userId}', 'enableAccount')->name('enable');
        Route::put('/suspendAccount/{userId}', 'suspendAccount')->name('suspend');
        Route::patch('/openAccount/{userId}', 'openAccount')->name('open');
        Route::put('/resetPassword/{userId}', 'resetPassword')->name('reset.password');
        Route::post('/checkPassword', 'checkPassword')->name('check.password');
        Route::patch('/archiveAccount/{userId}', 'archiveAccount')->name('archive');
    });
});
