<?php
namespace App\Helpers;

use App\Models\System\Resource;

class Helper
{
    public static function parseSecretData(array $formData)
    {
        $secretData = [
            "name" => data_get($formData, ""),
            "password" => $formData["password"],
            "profile" => $formData["profile"],
            "local-address" => $formData["local_address"],
            "remote-address" => $formData["remote_address"],
        ];
    }
    public static function server_checkup()
    {
        $resource = Resource::first();
        if($resource) {
            return true;
        }
        return false;
    }
}
