<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use Carbon\Carbon;

class JwtToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'token',
        'uuid',
        'expiration_date'
    ];

    public function saveToken(User $user, object $token, string $uuid, DateTimeImmutable $expiration_date) : JwtToken {
        return $this->create([
            'user_id' => $user->id,
            'token' => $token->toString(),
            'uuid' => $uuid,
            'expiration_date' => Carbon::parse($expiration_date)->format('Y-m-d H:i:s')
        ]);
    }
}
