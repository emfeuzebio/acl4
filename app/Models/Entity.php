<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $table = 'acl_entities';
    protected $fillable = [
            'id', 
            'system_id', 
            'model',
            'table',
            'description', 
            'active',
        ];

    // Many-to-one relationship - lowercase singular - "Many Entity belongs to a System"
    // Entity is CHILD of System
    public function system()
    {
        return $this->belongsTo(System::class);
    }

    // One-to-many relationship - lowercase plural. "An Entity can have many Actions children"
    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    // checks whether the entity can be deleted.
    public function canDelete()
    {
        // Checks if there is any active authorization (whose active column = 'Y') associated with this entity
        return !$this->actions()->whereHas('authorizations', function ($query) {
            $query->where('active', 'Y');
        })->exists();
    }
}
