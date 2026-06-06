<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\GeneralSettingController;

require __DIR__ . "/auth.php";

Route::get("/", function () {
    return to_route("login");
});

Route::group(["middleware" => ["auth", "verified"]], function () {
    // Dashboards
    Route::get("dashboard", [HomeController::class, "index"])->name(
        "dashboard.index",
    );
    Route::get("dashboard-refresh", [HomeController::class, "refresh"])->name(
        "dashboard.refresh",
    );
    Route::get("dashboard-detail/{id}", [
        HomeController::class,
        "detail",
    ])->name("dashboard.detail");
    Route::get("dashboard-pegawai/{nip}", [
        HomeController::class,
        "showPegawai",
    ])->name("dashboard.pegawai");

    Route::post("export/opd", [HomeController::class, "exportOpd"])->name(
        "export.opd",
    );
    Route::post("export/data", [HomeController::class, "exportData"])->name(
        "export.data",
    );

    Route::get("data/pd/{id}/pegawai", [
        HomeController::class,
        "getPegawai",
    ])->name("data.pd.pegawai");

    Route::post("/update-tahun", function (Request $request) {
        $request->validate(["tahun" => "required"]);
        session(["apps_tahun" => $request->tahun]);
        return back(); // Kembali ke halaman sebelumnya
    })->name("update.tahun");

    // Locale
    Route::get("setlocale/{locale}", SetLocaleController::class)->name(
        "setlocale",
    );

    // User
    Route::resource("users", UserController::class);
    // Permission
    Route::resource("permissions", PermissionController::class)->except([
        "show",
    ]);
    // Roles
    Route::resource("roles", RoleController::class);
    // Profiles
    Route::resource("profiles", ProfileController::class)
        ->only(["index", "update"])
        ->parameter("profiles", "user");
    // Env
    Route::singleton("general-settings", GeneralSettingController::class);
    Route::post("general-settings-logo", [
        GeneralSettingController::class,
        "logoUpdate",
    ])->name("general-settings.logo");

    // Database Backup
    Route::resource("database-backups", DatabaseBackupController::class);
    Route::get("database-backups-download/{fileName}", [
        DatabaseBackupController::class,
        "databaseBackupDownload",
    ])->name("database-backups.download");
});
