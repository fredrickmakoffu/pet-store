<?php

namespace App\Services;

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Hmac\Sha384;
use Lcobucci\JWT\Signer\Blake2b;

class SelectHashingAlgorithm
{
    public static function getAlgorithm($algo)
    {
        switch ($algo) {
            case 'HS256':
                return new Sha256();
            case 'HS384':
                return new Sha384();
            case 'HS512':
                return new Sha512();
            case 'BLAKE2B':
                return new Blake2b();
            default: 
                return new Sha256();
        }
    }
}
