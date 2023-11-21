<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\EvacueeController;
use App\Http\Controllers\DisasterController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\AreaReportController;
use App\Http\Controllers\UserAccountsController;
use App\Http\Controllers\FamilyRecordController;
use App\Http\Controllers\HotlineNumberController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\ResidentReportController;
use App\Http\Controllers\EmergencyReportController;
use App\Http\Controllers\EvacuationCenterController;


Route::controller(AuthenticationController::class)->group(function () {
    Route::middleware('check.login')->group(function () {
        Route::view('/', 'authentication/authUser')->name('home');
    });

    Route::middleware('check.attempt')->group(function () {
        Route::post('/', 'authUser')->name('login');
    });

    Route::get('/logout', 'logout')->name('logout.user');
    Route::view('/recoverAccount', 'authentication.forgotPassword')->name('recoverAccount');
    Route::post('/findAccount', 'findAccount')->name('findAccount');
    Route::get('/resetPasswordForm/{token}', 'resetPasswordForm')->name('resetPasswordForm');
    Route::post('resetPassword', 'resetPassword')->name('resetPassword');
});

Route::prefix('resident')->middleware('guest')->group(function () {
    Route::name('resident.')->group(function () {
        Route::controller(MainController::class)->group(function () {
            Route::get('/eligtasGuideline', 'eligtasGuideline')->name('eligtas.guideline');
            Route::get('/searchGuideline', 'searchGuideline')->name('guideline.search');
            Route::get('/guide/{guidelineId}', 'guide')->name('eligtas.guide');
            Route::get('/evacuationCenterLocator', 'evacuationCenterLocator')->name('evacuation.center.locator');
            Route::get('/incidentReporting', 'incidentReporting')->name('reporting');
            Route::get('/hotlineNumber', 'hotlineNumbers')->name('hotline.number');
        });

        Route::name('area.')->controller(AreaReportController::class)->group(function () {
            Route::post('/createAreaReport', 'createAreaReport')->name('report');
            Route::get('/getAreaReport/{operation}/{year}/{type}', 'getAreaReport')->name('get');
        });

        Route::post('/createIncidentReport', IncidentReportController::class . '@createIncidentReport')->name('incident.report');
        Route::post('/createEmergencyReport', EmergencyReportController::class . '@createEmergencyReport')->name('emergency.report');
        Route::get('/viewEvacuationCenter/{operation}/{type}', EvacuationCenterController::class . '@getEvacuationData')->name('evacuation.center.get');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('cswd')->middleware('check.cswd')->group(function () {
        Route::controller(MainController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard.cswd');
            Route::get('/evacuee/{operation}', 'manageEvacueeInformation')->name('manage.evacuee.record');
            Route::get('/evacuationCenter/{operation}', 'evacuationCenter')->name('evacuation.center');
            Route::get('/evacuationCenterLocator', 'evacuationCenterLocator')->name('evacuation.center.locator');
            Route::get('/dangerAreaReport/{operation}', 'dangerAreaReport')->name('danger.area.report');
            Route::get('/userActivityLog', 'userActivityLog')->name('activity.log');
            Route::get('/disasterInformation/{operation}', 'disasterInformation')->name('disaster.information');
        });

        Route::prefix('disaster')->name('disaster.')->controller(DisasterController::class)->group(function () {
            Route::get('/disasterInformation/{operation}/{year}', 'displayDisasterInformation')->name('display');
            Route::post('/createDisasterData', 'createDisasterData')->name('create');
            Route::patch('/updateDisaster/{disasterId}', 'updateDisasterData')->name('update');
            Route::patch('/archiveDisasterData/{disasterId}/{operation}', 'archiveDisasterData')->name('archive');
            Route::patch('/changeDisasterStatus/{disasterId}', 'changeDisasterStatus')->name('change.status');
        });

        Route::prefix('evacuee')->name('evacuee.info.')->controller(EvacueeController::class)->group(function () {
            Route::get('/getEvacueeInfo/{operation}/{disasterId}/{status}', 'getEvacueeData')->name('get');
            Route::get('/getArchivedEvacueeInfo/{disasterInfo}', 'getArchivedEvacueeInfo')->name('get.archived');
            Route::post('/recordEvacueeInfo', 'recordEvacueeInfo')->name('record');
            Route::put('/updateEvacueeInfo/{evacueeId}', 'updateEvacueeInfo')->name('update');
            Route::patch('/archiveEvacueeInfo/{evacueeId}/{operation}', 'archiveEvacueeInfo')->name('archive');
            Route::patch('/updateEvacueeStatus', 'updateEvacueeStatus')->name('update.status');
        });

        Route::prefix('family')->name('family.record.')->controller(FamilyRecordController::class)->group(function () {
            Route::get('/getFamilyRecord/{data}/{operation}', 'getFamilyData')->name('get');
            Route::post('/recordFamilyRecord', 'recordFamilyRecord')->name('record');
            Route::put('/updateFamilyRecord', 'updateFamilyRecord')->name('update');
        });

        Route::prefix('evacuationCenter')->name('evacuation.center.')->controller(EvacuationCenterController::class)->group(function () {
            Route::get('/viewEvacuationCenter/{operation}/{type}', 'getEvacuationData')->name('get');
            Route::post('/createEvacuationCenter', 'createEvacuationCenter')->name('create');
            Route::put('/updateEvacuation/{evacuationId}', 'updateEvacuationCenter')->name('update');
            Route::patch('/archiveEvacuation/{evacuationId}/{operation}', 'archiveEvacuationCenter')->name('archive');
            Route::patch('/changeEvacuationStatus/{evacuationId}', 'changeEvacuationStatus')->name('change.status');
        });

        Route::get('/getAreaReport/{operation}/{year}/{type}', AreaReportController::class . '@getAreaReport')->name('cswd.area.get');
    });

    Route::prefix('cdrrmo')->middleware('check.cdrrmo')->group(function () {
        Route::controller(MainController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard.cdrrmo');
            Route::get('/fetchReportData', 'fetchReportData')->name('fetchReportData');
            Route::get('/manageReport/{operation}', 'manageReport')->name('manage.report');
        });

        Route::controller(ResidentReportController::class)->group(function () {
            Route::get('/getResidentReport/{year}', 'getResidentReport')->name('resident.report.get');
            Route::get('/getNotifications', 'getNotifications')->name('notifications.get');
            Route::patch('/changeNotificationStatus/{id}', 'changeNotificationStatus')->name('notification.remove');
        });

        Route::prefix('incidentReport')->name('incident.')->controller(IncidentReportController::class)->group(function () {
            Route::get('/getIncidentReport/{operation}/{year}/{type}', 'getIncidentReport')->name('get');
            Route::patch('/changeIncidentReportStatus/{reportId}', 'changeIncidentReportStatus')->name('change.status');
            Route::delete('/removeIncidentReport/{reportId}', 'removeIncidentReport')->name('remove');
            Route::patch('/archiveIncidentReport/{reportId}', 'archiveIncidentReport')->name('archive');
        });

        Route::prefix('emergencyReport')->name('emergency.')->controller(EmergencyReportController::class)->group(function () {
            Route::get('/getEmergencyReport/{operation}/{year}/{type}', 'getEmergencyReport')->name('get');
            Route::patch('/changeEmergencyReportStatus/{reportId}', 'changeEmergencyReportStatus')->name('change.status');
            Route::delete('/removeEmergencyReport/{reportId}', 'removeEmergencyReport')->name('remove');
            Route::post('/archiveEmergencyReport/{reportId}', 'archiveEmergencyReport')->name('archive');
        });

        Route::prefix('areaReport')->name('area.')->controller(AreaReportController::class)->group(function () {
            Route::get('/getAreaReport/{operation}/{year}/{type}', 'getAreaReport')->name('get');
            Route::patch('/approveAreaReport/{reportId}', 'approveAreaReport')->name('approve');
            Route::patch('/updateAreaReport/{reportId}', 'updateAreaReport')->name('update');
            Route::delete('/removeAreaReport/{reportId}', 'removeAreaReport')->name('remove');
            Route::patch('/archiveAreaReport/{reportId}', 'archiveAreaReport')->name('archive');
        });
    });

    Route::prefix('eligtasGuideline')->controller(GuidelineController::class)->group(function () {
        Route::name('guideline.')->group(function () {
            Route::post('/guideline/createGuideline', 'createGuideline')->name('create');
            Route::post('/guideline/updateGuideline/{guidelineId}', 'updateGuideline')->name('update');
            Route::delete('/guideline/removeGuideline/{guidelineId}', 'removeGuideline')->name('remove');
        });

        Route::name('guide.')->group(function () {
            Route::post('/guide/addGuide{guidelineId}', 'createGuide')->name('create');
            Route::post('/guide/updateGuide/{guideId}', 'updateGuide')->name('update');
            Route::delete('/guide/removeGuide/{guideId}', 'removeGuide')->name('remove');
        });
    });

    Route::controller(MainController::class)->group(function () {
        Route::get('/searchDisaster/{year}', 'searchDisaster')->name('searchDisaster');
        Route::get('/eligtasGuideline', 'eligtasGuideline')->name('eligtas.guideline');
        Route::get('/searchGuideline', 'searchGuideline')->name('guideline.search');
        Route::get('/guide/{guidelineId}', 'guide')->name('eligtas.guide');
        Route::post('/generateEvacueeData', 'generateExcelEvacueeData')->name('generate.evacuee.data');
        Route::get('/userAccounts/{operation}', 'userAccounts')->name('display.users.account')->middleware('check.position');
        Route::get('/userProfile', 'userProfile')->name('display.profile');
        Route::get('/hotlineNumber', 'hotlineNumbers')->name('hotline.number');
        Route::get('/fetchBarangayData', 'fetchBarangayData')->name('fetchBarangayData');
        Route::get('/fetchDisasterData', 'fetchDisasterData')->name('fetchDisasterData');
    });

    Route::controller(HotlineNumberController::class)->group(function () {
        Route::post('/addHotlineNumber', 'addHotlineNumber')->name('hotline.add');
        Route::post('/updateHotlineNumber/{hotlineId}', 'updateHotlineNumber')->name('hotline.update');
        Route::delete('/removeHotlineNumber/{hotlineId}', 'removeHotlineNumber')->name('hotline.remove');
    });

    Route::name('account.')->controller(UserAccountsController::class)->group(function () {
        Route::post('/createAccount', 'createAccount')->name('create');
        Route::put('/updateAccount/{userId}', 'updateAccount')->name('update');
        Route::get('/displayUserAccount/{operation}', 'userAccounts')->name('display.users');
        Route::patch('/toggleAccountStatus/{userId}/{operation}', 'toggleAccountStatus')->name('toggle.status');
        Route::put('/resetPassword/{userId}', 'resetPassword')->name('reset.password');
        Route::post('/checkPassword', 'checkPassword')->name('check.password');
        Route::patch('/archiveAccount/{userId}/{operation}', 'archiveAccount')->name('archive');
    });
});
