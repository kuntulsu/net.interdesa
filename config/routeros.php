<?php

return [
    "host" => env("ROUTEROS_HOST"),
    "username" => env("ROUTEROS_USERNAME"),
    "password" => env("ROUTEROS_PASSWORD"),
    "port" => env("ROUTEROS_PORT"),
    "ssl" => env("ROUTEROS_SECURE", false)
];