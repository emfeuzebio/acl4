<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    protected $table = 'acl_audit';

    public $timestamps = false;

    protected $fillable = [
        'system_id',
        'actor_user_id',
        'action',
        'target_user_id',
        'target_profile_id',
        'payload_before',
        'payload_after',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'payload_before' => 'array',
        'payload_after'  => 'array',
        'created_at'     => 'datetime',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function targetProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'target_profile_id');
    }
}