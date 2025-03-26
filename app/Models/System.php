<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $table = 'acl_systems';
    protected $fillable = [
            'id', 
            'organization_id', 
            'name',
            'acronym',
            'description', 
            'active',
        ];

    // Sistema é FILHO de Organizacao.
    // Relacionamento "muitos para um": "Muitos Sistemas pertencem a uma Organização"
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }           

    // One-to-many relationship - lowercase plural. "A System have many Entity children"
    // System is PARENT of Entities
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }   
    
    // One-to-many relationship - lowercase plural. "A System have many Profiles children"
    // System is PARENT of Profiles
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }    
    
    public function users()
    {
        return $this->belongsToMany(User::class,'acl_system_user');
    }    

    public function roles()
    {
        return $this->belongsToMany(Profile::class,'acl_system_profile');
    }    
    
}
