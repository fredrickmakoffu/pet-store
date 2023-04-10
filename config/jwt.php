<?php

return [
    'secret' => env('JWT_SECRET'),
    'algo'   => env('JWT_ALGO', 'HS256'),
    'expiration_date'    => env('JWT_EXPIRATION', 60),
    'timezone' => env('JWT_TIMEZONE', 'UTC'),
];

// Path: config/jwt.php

