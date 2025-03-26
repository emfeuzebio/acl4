<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'acl_menus';
    protected $fillable = [
            'id', 
            'menu_id',
            'name',
            'link', 
            'position', 
            'active',
        ];

    // Many-to-Many relationship - lowercase plural - "Many Menus belongs to a Many Menus"
    public function profiles()
    {
        return $this->belongsToMany(Profile::class,'acl_menu_profile');
    }    

    // Many-to-One relationship - lowercase plural - "One Menus has a Parent"
    public function parent()
    {
        return $this->belongsToMany(Menu::class, 'menu_id');
    }    

    // One-to-Many relationship - lowercase plural - "Many Menus belongs to a Parent"
    public function children()
    {
        return $this->hasMany(Menu::class, 'menu_id');
    }    
}
