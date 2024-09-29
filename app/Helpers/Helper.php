<?php

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
}
