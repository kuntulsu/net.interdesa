<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Number;
use App\Models\System\Resource;
use App\Http\Controllers\Controller;
use App\Models\Interface\Monitoring;
use Telegram\Bot\Laravel\Facades\Telegram;

class ServerReportController extends Controller
{
    public static function serverReport()
    {
        $interface = config("monitoring.monitoring.interface");

        $resource = Resource::first();
        $googleLatencies = Monitoring::ping("8.8.8.8")[0]['time'];
        $cloudflareLatencies = Monitoring::ping("1.1.1.1")[0]['time'];
        $trafficMonitor = Monitoring::monitor($interface);
        $uploadTraffic = $trafficMonitor[0]['tx-bits-per-second'] / 1024 / 1024;
        $uploadTraffic = Number::format($uploadTraffic, 2);
        $downloadTraffic = $trafficMonitor[0]['rx-bits-per-second'] / 1024 / 1024;
        $downloadTraffic = Number::format($downloadTraffic, 2);
        $cpu_load = $resource['cpu-load'];
        $uptime = $resource->uptime;
        $memory_usg = $resource['total-memory'] - $resource['free-memory'];
        $memory_usg_percent = Number::format($memory_usg/$resource['total-memory']*100, 2);

        $message = "<b>🔔 Server Health Report</b>\n\n";
        $message .= "CPU Load: <code>{$cpu_load}%</code>\n";
        $message .= "Memory Usage: <code>{$memory_usg_percent}%</code>\n";
        $message .= "Uptime: <code>{$uptime}</code>\n";
        $message .= "Google Latencies: <code>{$googleLatencies}</code>\n";
        $message .= "Cloudflare Latencies: <code>{$cloudflareLatencies}</code>\n";
        $message .= "Download Traffic: <code>{$downloadTraffic} Mbps</code>\n";
        $message .= "Upload Traffic: <code>{$uploadTraffic} Mbps</code>\n";
        $message .= "Time: <code>" . now() . "</code>\n";

        
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
