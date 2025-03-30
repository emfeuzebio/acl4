<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $table = 'acl_tokens';
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'status',
        'ip',
        'browser',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relacionamento: um token pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
