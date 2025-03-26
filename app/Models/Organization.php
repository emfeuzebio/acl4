<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    // public $timestamps = false;                    
    protected $table = 'acl_organizations';
    protected $fillable = [
            'id', 
            'name',
            'acronym',
            'description', 
            'active',
        ];

    // One-to-many relationship - lowercase plural. "An Organization can have many Systems children"
    public function systems()
    {
        return $this->hasMany(System::class);
    }        

    // Many-to-Many relationship - lowercase plural - "Many Users belongs to a Many Organizations"    
    public function users()
    {
        return $this->belongsToMany(User::class, 'acl_organization_user');
    }    
}
