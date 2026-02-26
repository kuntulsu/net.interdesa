<?php

namespace App\Http\Controllers\API\v1;

use App\Models\PPPoE\Secret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\ClientDownNotification;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class ClientReportController extends Controller
{
    
    public function down(Request $request)
    {
        $secret_id = $request->get("secId");
        $pelanggan = Pelanggan::with("profil")->whereHas("profil", fn($query) => $query->where("secret_id", $secret_id))->first();
        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton([
                    'text' => 'Lihat Profil',
                    'url' => route("filament.admin.resources.pelanggan.view", $pelanggan->id),
                ]),
                Keyboard::inlineButton([
                    'text' => 'Contact',
                    'url' => "https://wa.me/{$pelanggan->telp}",
                ]),
                Keyboard::inlineButton([
                    'text' => 'Lokasi ODP',
                    'url' => "https://maps.google.com/?q={$pelanggan->odp?->coordinate}"
                ])
            ]);
        Telegram::sendMessage([
            'chat_id' => config("telegram.default_chat_id"),
            'parse_mode' => 'HTML',
            'text' => 
                "❌ <b>Client Disconnected</b>\n\n" .
                "Nama: <code>{$pelanggan->nama}</code>\n" .
                "Alamat: <code>{$pelanggan->alamat}</code>\n" .
                "ODP: <code>{$pelanggan->odp?->nama}</code>\n".
                "ODP Deskripsi: <code>{$pelanggan->odp?->description}</code>\n".
                "Telp: <code>{$pelanggan->telp}</code>\n" .
                "Event: <code>down</code>\n" .
                "Time: <code>" . now() . "</code>\n",
            'reply_markup' => $keyboard,
        ]);
    }
    public function up(Request $request)
    {
        $secret_id = $request->get("secId");
        $pelanggan = Pelanggan::with("profil")->whereHas("profil", fn($query) => $query->where("secret_id", $secret_id))->first();
        Telegram::sendMessage([
            'chat_id' => config('telegram.default_chat_id'),
            'parse_mode' => 'HTML',
            'text' => "✅ <b>Client Connected</b>\n\n" .
                "Nama: <code>{$pelanggan->nama}</code>\n" .
                "Event: <code>up</code>\n" .
                "Time: <code>" . now() . "</code>\n",
        ]);
    }
    private static function generatePercentage(float $part, float $total): string
    {
        if ($total == 0) {
            return "0%";
        }
        $percentage = ($part / $total) * 100;
        return number_format($percentage, 2) . "%";
    }
    public static function health_report()
    {
        $pelanggan = Pelanggan::with("profil.secret.active")->get();

        $pelangganCount = $pelanggan->count();
        $activeCount = $pelanggan->where("profil.secret.active", "!=", null)->count();
        $isolirCount = $pelanggan->where("profil.secret.disabled", "=", true)->count();
        $offlineCount = $pelanggan->count() - $activeCount - $isolirCount;
        
        $activePercent = static::generatePercentage($activeCount, $pelangganCount);
        $isolirPercent = static::generatePercentage($isolirCount, $pelangganCount);
        $offlinePercent = static::generatePercentage($offlineCount, $pelangganCount);

        $activeMsg = "{$activeCount} ({$activePercent})";
        $isolirMsg = "{$isolirCount} ({$isolirPercent})";
        $offlineMsg = "{$offlineCount} ({$offlinePercent})";

        Telegram::sendMessage([
            'chat_id' => config('telegram.default_chat_id'),
            'text' => "<b>🔔 Client Health Report</b>\n\n" .
                "Total P<code>elanggan: {$pelangganCount}</code>\n" .
                "Active: <code>{$activeMsg}</code>\n" .
                "Isolir: <code>{$isolirMsg}</code>\n" .
                "Offline: <code>{$offlineMsg}</code>\n".
                "Time: <code>" . now() . "</code>\n",
            'parse_mode' => 'HTML',
        ]);
    }

}
