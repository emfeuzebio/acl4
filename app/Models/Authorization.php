<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'acl_authorizations';
    protected $fillable = [
            'id', 
            'profile_id',
            'action_id',
            'active',
        ];

    // Many-to-one relationship - lowercase singular - "Many Authorizations belongs to a Profile"
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    // Many-to-one relationship - lowercase singular - "Many Authorizations belongs to a Entity"
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    // Many-to-one relationship - lowercase singular - "Many Authorizations belongs to a Action"
    public function action()
    {
        // return $this->belongsTo(Action::class,'acl_actions');
        return $this->belongsTo(Action::class);
    }

}
