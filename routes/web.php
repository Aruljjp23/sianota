<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LegalisasiController;
use App\Http\Controllers\SkmhtController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\HomepageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::get('/', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'loginProcess'])
    ->name('login.process');

Route::get('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/register', [AuthController::class, 'registerProcess'])
    ->name('register.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


Route::middleware('auth')->group(function () {

    Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])
        ->name('admin.dashboard');


    Route::prefix('users')->name('users.')->group(function () {

        Route::get('/data_user', [UserController::class, 'dataUser'])
            ->name('data_user');

        Route::post('/', [UserController::class, 'store'])
            ->name('store');

        Route::put('/{user}', [UserController::class, 'update'])
            ->name('update');

        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->name('destroy');
    });


    Route::prefix('skmht')->name('skmht.')->group(function () {

        Route::get('/data_skmht', [SkmhtController::class, 'data_skmht'])->name('data_skmht');

        Route::get('/laporan_pdf', [SkmhtController::class, 'pdf'])->name('pdf');
    });


    Route::prefix('legalisasi')->name('legalisasi.')->group(function () {

        Route::get('/data_legalisasi', [LegalisasiController::class, 'data_legalisasi'])->name('data_legalisasi');

        Route::get('/kelola', [LegalisasiController::class, 'kelola'])->name('kelola');

        Route::post('/', [LegalisasiController::class, 'store'])
            ->name('store');

        Route::put('/{legalisasi}', [LegalisasiController::class, 'update'])
            ->name('update');

        Route::delete('/{legalisasi}', [LegalisasiController::class, 'destroy'])
            ->name('destroy');
    });


    Route::get('/admin/laporan', [LaporanController::class, 'data_laporan'])
        ->name('laporan.data_laporan');

    Route::get('/admin/laporan/pdf', [LaporanController::class, 'pdf'])
        ->name('laporan.pdf');


    Route::prefix('karyawan')->name('karyawan.')->group(function () {

        Route::get('/homepage', [HomepageController::class, 'homepage'])->name('homepage');

        Route::prefix('skmht')->name('skmht.')->group(function () {

            Route::get('/kelola_skmht', [SkmhtController::class, 'kelola_skmht'])
                ->name('kelola_skmht');

            Route::post('/kelola_skmht', [SkmhtController::class, 'storeKaryawan'])
                ->name('store');

            Route::get('/kelola_skmht/{skmht}', [SkmhtController::class, 'showKaryawan'])
                ->name('show');

            Route::put('/kelola_skmht/{skmht}', [SkmhtController::class, 'updateKaryawan'])
                ->name('update');

            Route::delete('/kelola_skmht/{skmht}', [SkmhtController::class, 'destroyKaryawan'])
                ->name('destroy');
        });

        
        Route::prefix('legalisasi')->name('legalisasi.')->group(function () {

            Route::get('/kelola_legalisasi', [LegalisasiController::class, 'kelolaKaryawan'])
                ->name('kelola');

            Route::post('/kelola_legalisasi', [LegalisasiController::class, 'storeKaryawan'])
                ->name('store');

            Route::put('/kelola_legalisasi/{legalisasi}', [LegalisasiController::class, 'updateKaryawan'])
                ->name('update');

            Route::delete('/kelola_legalisasi/{legalisasi}', [LegalisasiController::class, 'destroyKaryawan'])
                ->name('destroy');
        });
    });


    Route::get('/notifications', function () {

        $userId = Auth::id();

        $notifications = DB::table('notifications')
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $unread = DB::table('notifications')
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {

                $data = json_decode($notification->data, true) ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notifikasi',
                    'message' => $data['message'] ?? '',
                    'icon' => $data['icon'] ?? 'fa-bell',
                    'url' => $data['url'] ?? '#',
                    'read' => !is_null($notification->read_at),
                    'time' => \Carbon\Carbon::parse(
                        $notification->created_at
                    )->diffForHumans(),
                ];
            }),

            'unread' => $unread,
        ]);
    })->name('notifications.index');


    Route::post('/notifications/{notification}/read', function ($notification) {

        $userId = Auth::id();

        DB::table('notifications')
            ->where('id', $notification)
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    })->name('notifications.read');


    Route::post('/notifications/read-all', function () {

        $userId = Auth::id();

        DB::table('notifications')
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    })->name('notifications.read_all');
});