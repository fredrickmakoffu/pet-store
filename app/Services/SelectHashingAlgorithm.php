<?php

namespace App\Services;

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Hmac\Sha512;

final class SelectHashingAlgorithm
{
    public static function getAlgorithm($algo)
    {
        switch ($algo) {
            case 'SHA256':
                return new Sha256();
            case 'shar516':
                return new Sha512();
            default: 
                return new Sha256();
        }
    }
}