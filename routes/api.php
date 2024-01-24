<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Public Routes
Route::get('/ping', function () {
    phpinfo();
});

Route::get('/dbConnect', function (Request  $request) {
    $connection = DB::connection('mongodb');
    $msg = 'mongodb is accessible!';
    try {
        $connection->command(['ping' => 1]);
    } catch (\Exception  $e) {
        $msg = 'mongodb is not accessible. Error: ' . $e->getMessage();
    }
    return ['msg' => $msg];
});
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::put('/users/update', [AuthController::class, 'updateUser']);
Route::get('/users/findAll', [AuthController::class, 'findAll']);
Route::get('/users/findOne', [AuthController::class, 'findOne']);
Route::delete('/users/deleteUser', [AuthController::class, 'deleteUser']);
Route::delete('/users/deleteAll ', [AuthController::class, 'deleteAllUser']);
Route::post('/users/add', [AuthController::class, 'addUser']);
Route::post('/users/logout', [AuthController::class, 'logout']);
Route::get('/users/detail', [AuthController::class, 'loginUserDetail']);
// Protected Routes
// Route::group(['middleware' => 'auth:api'], function () {
//     Route::prefix('users')->group(function () {
//         Route::put('update', [AuthController::class, 'update']);
//         Route::get('findAll', [AuthController::class, 'findAll']);
//         Route::get('detail', [AuthController::class, 'loginUserDetail']);
//         Route::post('deleteUser', [AuthController::class, 'destory']);
//         Route::delete('deleteAll ', [AuthController::class, 'deleteAll']);
//         Route::post('logout', [AuthController::class, 'logout']);
//         Route::post('add', [AuthController::class, 'addUser']);
//     });
// });
// Route::group(['middleware' => 'auth:api'], function () {
//     Route::prefix('users')->group(function () {
//         Route::get('findOne', [AuthController::class, 'findOne']);
//     });
// });


Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'findOne']);
    Route::delete('/', [BookController::class, 'deleteBook']);
    Route::delete('all', [BookController::class, 'deleteAllBook']);
    Route::get('all', [BookController::class, 'findAll']);
    Route::post('add', [BookController::class, 'addBook']);
    Route::put('update', [BookController::class, 'updateBook']);
});

// routes/web.php or routes/api.php

Route::get('/testDB', [TestController::class, 'testDatabaseConnection']);
