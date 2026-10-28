<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserInvitation extends Model
{
    protected $fillable = [
        'user_id', 'token_hash', 'expires_at', 'accepted_at', 'created_by',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Cria um convite e devolve o token em claro em ->plain_token
     * (o banco guarda só o hash).
     */
    public static function issueFor(User $user, ?int $createdBy = null, int $hours = 48): self
    {
        $token = Str::random(64);

        $invitation = static::create([
            'user_id'    => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => Carbon::now()->addHours($hours),
            'created_by' => $createdBy,
        ]);

        $invitation->plain_token = $token;

        return $invitation;
    }

    public function isValid(): bool
    {
        return $this->accepted_at === null && $this->expires_at->isFuture();
    }

    public static function findValidByToken(string $token): ?self
    {
        return static::with('user')
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}