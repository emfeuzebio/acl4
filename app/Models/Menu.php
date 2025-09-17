<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'acl_menus';
    protected $fillable = [
            'id', 
            'system_id',
            'menu_id',
            'name',
            'icon',
            'route', 
            'position', 
            'active',
        ];

    // Many-to-Many relationship - lowercase plural - "Many Menus belongs to a Many Menus"
    /**
     * Relação: Um menu pode pertencer a vários perfis (roles)
     */
    public function profiles(): BelongsToMany
    {
        // return $this->belongsToMany(Profile::class, 'acl_menu_profile')
        //             ->withPivot('position')
        //             ->withTimestamps();

        return $this->belongsToMany(Profile::class, 'acl_menu_profile')
                    ->withPivot('position', 'active')
                    ->withTimestamps();                    
    }


    // Many-to-One relationship - lowercase plural - "One Menus has a Parent"
    public function parent()
    {
        return $this->belongsToMany(Menu::class, 'menu_id');
    }    

    // Many-to-one relationship - lowercase singular - "Many Menus belongs to a System"
    // Menu is CHILD of System
    public function system()
    {
        return $this->belongsTo(System::class);
    }

    // One-to-Many relationship - lowercase plural - "Many Menus belongs to a Parent"
    /**
     * Relação: Um menu pode ter vários submenus (filhos)
     */    
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'menu_id')->orderBy('position');
    }  
    
    // checks whether the entity can be deleted.
    public function canDelete(): bool
    {
        return true;
    }
    
}
