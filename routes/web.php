<?php

use App\Models\Pelanggan;
use App\Models\PembayaranPelanggan;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(route("filament.admin.pages.dashboard"));
    // return view("welcome");
});

Route::get("/test", function () {
    $tagihan = Tagihan::first();
    $data = Pelanggan::whereDoesntHave(["pembayaran" => function ($query) use ($tagihan){
        $query->where("tagihan_id", $tagihan->id);
    }])->get()->each(function($data) {
        $secret = $data->profil->secret;
        if(str($secret->name)->endsWith("@STAFF.PECANGAANKULON.ID")){
            return;
        }elseif(str($secret->name)->startsWith("ALFIANSYAH")){
            return;
        }
        $secret->disable();
        $secret->active?->dropConnection();
    });
})->middleware("auth");

Route::get("/invoice/{pembayaran}", function(PembayaranPelanggan $pembayaran) {

    return view("invoice", [
        "pembayaran" => $pembayaran
    ]);
})->name("invoice");

Route::get("promo-akhir-tahun", function (){
    return redirect("https://api.whatsapp.com/send/?phone=6285157180664");
});