<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $table = 'acl_profiles';
    protected $fillable = [
            'id', 
            'system_id', 
            'name',
            'acronym',
            'description', 
            'active',
        ];

    // Sistema é FILHO de Organizacao.
    // Relacionamento "muitos para um": "Muitos Perfis pertencem a um Sistema"
    public function system()
    {
        return $this->belongsTo(System::class);
    }      

    // Many-to-many relationship - lowercase plural. "A Profile can have many Systems children"
    public function systems() {
        return $this->belongsToMany(System::class,'acl_system_profile');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class,'acl_profile_user');
    }    

    // One-to-many relationship - lowercase plural. "A Profile have many Authorizations children"
    public function authorizations()
    {
        return $this->hasMany(Authorization::class);
    }    

    // checks whether the entity can be deleted.
    public function canDelete()
    {
        // Checks if there is any active authorization (whose active column = 'Y') associated with this Profile
        return !$this->authorizations()->where('active', 'Y')->exists();

        // TODO TERMINAR NOVA REGRAS DE NEGOCIO
        // pode apagar mesmo com active = Y desde que sejam das Entidades Bases com ID <= 7

        // $authorizations = Profile::find($profileId)
        // ->authorizations()  // Relacionamento de Authorizations no Profile
        // ->whereHas('action.entity', function ($query) {
        //     // Filtra as Authorizations que são de Entities com ID > 7
        //     $query->where('id', '>', 7);
        // })
        // ->whereHas('action', function ($query) {
        //     // Filtra as Authorizations cujas Actions têm 'active' = 'Y'
        //     $query->where('active', 'Y');
        // })
        // ->get();        
    }    

    // Many-to-many relationship - lowercase plural. "A Profile can have many Actions children"
    public function actions()
    {
        // return $this->belongsToMany(Action::class, 'acl_authorizations');
        return $this->belongsToMany(Action::class, 'acl_authorizations', 'profile_id', 'action_id');        
    }

    // Relacionamento com a tabela de menus (muitos para muitos)
    // Many-to-many relationship - lowercase plural. "A Profile can have many Menus children"
    public function menus()
    {
        // return $this->belongsToMany(Menu::class, 'acl_menu_profile', 'profile_id', 'menu_id');
        return $this->belongsToMany(Menu::class, 'acl_menu_profile');
    }    
}
