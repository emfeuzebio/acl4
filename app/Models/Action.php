<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'acl_actions';
    protected $fillable = [
            'id', 
            'entity_id',
            'action',
            'route',
            'description', 
        ];

    // Many-to-one relationship - lowercase singular - "Many Action belongs to a Entity"
    // Action is CHILD of Entity
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    // One-to-many relationship - lowercase plural. "An Action can have many Authorizations children"
    public function authorizations()
    {
        return $this->hasMany(Authorization::class);
    }    

    // Many-to-many relationship - lowercase plural. "An Action can have many Profiles children"
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'acl_authorizations');
    }

    // checks whether the Action can be deleted
    public function canDelete()
    {
        // Checks if there is any active authorization (whose active column = 'Y') associated with this Action
        return !$this->authorizations()->where('active', 'Y')->exists();
    }    
}
