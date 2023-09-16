<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Chatapi\UserController;
use App\Http\Controllers\Admin\Chatapi\Category\CategoryController;
use App\Http\Controllers\Admin\Chatapi\Website\WebSiteController;
use App\Http\Controllers\Admin\Chatapi\Department\DepartMentController;
use App\Http\Controllers\Admin\Chatapi\Shift\ShiftController;
use App\Http\Controllers\Admin\Chatapi\Dashboard\DashboardController;
// use App\Http\Controllers\admin\chatapi\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('add-category',function(){
//   dd(1);
// });
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::Post('login',[UserController::class,'userLogin']);
Route::Post('registation', [UserController::class, 'userRegistation']);
Route::Post('user-list', [UserController::class, 'registerUserList']);
Route::get('get-manager',[UserController::class, 'getManagerList']);
Route::Post('single-record-user',[UserController::class, 'deleteSingleRecordUser']);
Route::Post('status-update-user',[UserController::class, 'statusUpdateUser']);
Route::Post('user-deatils',[UserController::class, 'userDeatils']);
Route::get('web-list',[UserController::class, 'webSiteList']);
Route::post('get-website-details',[UserController::class, 'getWebsiteDetails']);
Route::post('get-agent-details',[UserController::class, 'getAgentDetails']);




//   Start Category Controller   //
 Route::Post('add-category',[CategoryController::class , 'AddCategory']);
 Route::Post('cat-list',[CategoryController::class , 'getCategoryList']);
 Route::Post('filter-list',[CategoryController::class , 'getFilterList']);
 Route::Post('delete-category',[CategoryController::class ,'deleteCategory']);
 Route::Post('multiple-delete-records',[CategoryController::class , 'multipleDeleteRecords']);


 Route::get('replace-data',[CategoryController::class , 'getBlogReplaceData']);


 //    Start Website Controller    //
 Route::Post('website-list',[WebSiteController::class , 'websiteList']);
 Route::Post('add-website',[WebSiteController::class , 'AddWebSite']);
 Route::Post('get-filter-website',[WebSiteController::class , 'getFilterWebsiteList']);
 Route::Post('single-record',[WebSiteController::class, 'deleteWebSite']);
 Route::Post('multiple-rc-delete-web',[WebSiteController::class, 'multipleDeleteRecordsWebSite']);
 // Route::get('web-list',[WebSiteController::class, 'getWebsiteList']);
 Route::Post('status-update',[WebSiteController::class, 'statusUpdateWebsite']);

 //   Start Department Controllers //
 Route::Post('add-department',[DepartMentController::class , 'AddDepartment']);
 Route::Post('dep-list',[DepartMentController::class, 'getdepartmentList']);
 Route::Post('updete-status-dep',[DepartMentController::class ,'statusUpdateDepartMent']);
 Route::Post('multiple-del-dep',[DepartMentController::class, 'multipleDeleteRecordsDdep']);
 Route::Post('single-record-dep',[DepartMentController::class ,'deleteSingleRecordDep']);
 Route::Post('dep-filter-records',[DepartMentController::class,'getFilterListDepartment']);

 // Start Shift Controller  //
 Route::get('user-agent-list',[ShiftController::class , 'userAgentList']);
 Route::Post('add-user-shift',[ShiftController::class , 'addShiftUser']);
 Route::post('shift-user-list',[ShiftController::class , 'shiftUserList']);
 Route::Post('status-update-user-shift',[ShiftController::class, 'statusUpdateUserShift']);
 Route::Post('status-filter-user-shift',[ShiftController::class, 'getFilterShiftList']);
 Route::Post('delete-single-shif-record',[ShiftController::class, 'deleteSingleShiftRecord']);

 // Start Dashboard Controller //
 Route::get('dashboard',[DashboardController::class, 'index']);
