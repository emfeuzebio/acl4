<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Authorization;
use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Contracts\JWTSubject;

// class User extends Authenticatable
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 'organization_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Below are the methods to Admini LTE configuration
     * A view blade do Profiel do User está em:
     *  application/resources/views/vendor/adminlte/partials/navbar/menu-item-dropdown-user-menu.blade.php
     *  Nela implementar um modal para editar o perfil do User
     *       
     */

    /**
     * Retorna o nome do usuário para o menu do AdminLTE.
     */
    public function adminlte_profile_url()
    {
        // return 'Rota do User perfil';
        return route('user.updateProfile');   // Rota para a página de perfil
    }

    /**
     * Retorna a URL da imagem do usuário para o menu do AdminLTE.
     */
    public function adminlte_image()
    {
        return $this->profile_image 
            ? asset('storage/' . $this->profile_image) 
            : '/vendor/adminlte/dist/img/avatar-blue.png';
            // : '/vendor/adminlte/dist/img/avatar.png';
            // : 'https://adminlte.io/themes/v4/dist/img/user2-160x160.jpg';
    }

    /**
     * Retorna a descrição do usuário para o menu do AdminLTE.
     */
    public function adminlte_desc()
    {
        // $profile = Profile::with('system')->where('id','>', '7');
        // $query = Profile::with('system')->where('id','>=', '1');
        // $profiles = $query->get();
        // $roles = $profiles->pluck('name')->unique()->values();
        // return $roles->implode(',');

        // TODO carregar dados do User na session e não do banco
        $user = Auth::user();
        $membroDesde = "Membro desde " . $user->created_at->format('d/m/Y');        

        return $membroDesde; // Ajuste para a sua estrutura de banco
    }

    /**
     * Retorna a URL de logout para o AdminLTE.
     */
    public function adminlte_logout_url()
    {
        return route('logout');
    }

    /**
     * Retorna a rota da página de login após o logout.
     */
    public function adminlte_login_url()
    {
        return route('login');
    }
    


    /**
     * Below are the relationships this model has to
     * ACL 2 - Methos to get all authorizations of the User
     */

    // Many-to-one relationship - lowercase singular - "Many Profiles belongs to a User"
    public function profiles() {
        return $this->belongsToMany(Profile::class,'acl_profile_user');
    }

    public function authorizations()
    {

        // Aqui o User ainda não está logado
        // if (Auth::check()) {
        //     $userId = Auth::id();
        //     dd($userId); // Deve mostrar o ID do usuário
        // } else {
        //     dd("Usuário não está logado!");
        // }

        // $userId = Auth::id();
        // $userId =  User::first();
        // $userId =  3;
        // $userId = Auth::user()->id;

        // Como aqui o User ainda não está logado, por isso não podemos filtras a Authuz do User corrente
        // $authorizations = Authorization::whereIn('profile_id', $this->profiles()->pluck('acl_profiles.id'))
        //     ->where('active', 'Y')
        //     ->with('action')
        //     ->whereHas('profile.users', function ($q) use ($userId) {
        //         $q->where('users.id', $userId);
        //     })
        //     ->get();

        // Como aqui o User ainda não está logado o que podemos fazer é listas todas Authorizations ativas
        $authorizations = Authorization::whereIn('profile_id', $this->profiles()->pluck('acl_profiles.id'))
            ->where('active', 'Y')
            ->with('action')
            ->get();

        return $authorizations;            
    }  
    
    public function getAclRoutes()
    {
        return $this->authorizations()->pluck('action.route')->unique()->toArray();
    }    

    // One-to-many relationship - lowercase plural. "A User can have many Profiles children"
    // User is PARENT of Profiles
    // Perfis (Roles) do User 
    public function hasProfiles()
    {
        return $this->hasMany(Profile::class);
    }      

    // Many-to-one relationship - lowercase singular - "Many Systems belongs to a User"
    public function systems() {
        return $this->belongsToMany(System::class,'acl_system_user');
    }

    // One-to-many relationship - lowercase plural. "A User can have many Systems children"    
    public function hasSystems()
    {
        return $this->hasMany(System::class);
    }      

    // Many-to-one relationship - lowercase singular - "Many Users belongs to a Organization"    
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }    
    
    // Many-to-Many relationship - lowercase plural - "Many Users belongs to a Many Organizations"
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'acl_organization_user');
    }   

    // JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT JWT 

    /**
     * Return the identificator to JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Retorna um array de claims personalizadas para JWT.
     */
    public function getJWTCustomClaims()
    {
        // Carrega informações no payload do token
        return [
            'iss' => 'https://acl4.fazcomphp.com.br/',              // Emissor do token
            'aud' => 'http://apifeb.voluntary.com.br',              // Público-alvo (Audience) do token
            'user_id' => $this->id,                                 // ID do usuário
            'user_name' => $this->name,                                  // Nome do usuário
            'user_roles' => $this->profiles->pluck('name')->toArray(),   // Roles do usuário
            'user_abilities' => $this->getAclRoutes(),                   // "abilities" (Authorizaions) do usuário 
        ];
    }    
}
