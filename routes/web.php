<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/test", function () {
    $data = \App\Models\PPPoE\Secret::where(
        "name",
        "ILHAM@STAFF.PECANGAANKULON.ID"
    )
        ->with("active")
        ->first();
    dd($data);
});
