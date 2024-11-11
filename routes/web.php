<?php

use App\Models\PembayaranPelanggan;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(route("filament.admin.pages.dashboard"));
    // return view("welcome");
});

// Route::get("/test", function () {
//     $data = \App\Models\PPPoE\Secret::where(
//         "name",
//         "ILHAM@STAFF.PECANGAANKULON.ID"
//     )
//         ->with("active")
//         ->first();
//     dd($data);
// });

Route::get("/invoice/{pembayaran}", function(PembayaranPelanggan $pembayaran) {

    return view("invoice", [
        "pembayaran" => $pembayaran
    ]);
})->name("invoice");
