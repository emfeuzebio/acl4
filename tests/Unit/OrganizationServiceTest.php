<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use App\Services\OrganizationService;
use App\Models\Organization;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Mockery;

/**
 * PARA EXECUTAR os teste
 *  Edit o .env, # DB_HOST=db por DB_HOST=127.0.0.1
 *  acesse o terminal: Projeto/acl2/application
 *  Use um dos comandos:
 *      1 - php artisan test    // mostra a execução mais detalhada
 *      2 - vendor/bin/phpunit  // mostra a execução em pecentual
 * 
 * PARA reconstruir o Banco de Dados no terminal
 *  php artisan migrate:fresh --force
 *  php artisan db:seed --force
 * 
 * PARA reconstruir o Banco de Dados numa classe PHP
 *  Artisan::call('migrate:fresh --force');
 *  Artisan::call('db:seed --force');
 * 
 */
class OrganizationServiceTest extends TestCase
{
    use RefreshDatabase;    // Para garantir que cada teste seja isolado e o banco seja limpo entre as execuções.

    protected $organizationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->organizationService = new OrganizationService();
    }

    public function testInsertSuccess()
    {
        // Definindo o usuário fictício
        $user = User::factory()->create();
        
        // Criando um mock para o Request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('only')->with(['name', 'acronym', 'description', 'active'])->andReturn([
            'name' => 'Org Test',
            'acronym' => 'ORG',
            'description' => 'A test organization',
            'active' => true
        ]);

        // Testando o fluxo de sucesso
        DB::beginTransaction();
        
        try {
            $organization = $this->organizationService->insert($request);
            $this->assertNotNull($organization);
            $this->assertEquals('Org Test', $organization->name);
        } catch (Exception $e) {
            $this->fail('Não deveria lançar exceção: ' . $e->getMessage());
        }

        DB::commit();
    }   
    
    public function testCheckUserThrowsExceptionWhenUserNotFound()
    {
        // Tentando verificar um usuário inexistente
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Usuário NÃO existe.');

        $this->organizationService->checkUser(999); // ID de usuário que não existe
    }

    public function testCheckProfileThrowsExceptionWhenNoProfile()
    {
        // Criando um usuário sem perfil
        $user = User::factory()->create();
        $user->profiles()->detach(); // Garantindo que o usuário não tenha perfil.

        // Espera-se que seja lançada a exceção
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('O Usuário NÃO tem Perfil de Acesso necessário.');

        $this->organizationService->checkProfile($user);
    }

    public function testCreateOrganizationThrowsExceptionWhenFailsToCreate()
    {
        // Criando um mock para o Request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('only')->with(['name', 'acronym', 'description', 'active'])->andReturn([
            'name' => 'Invalid Org',
            'acronym' => 'ORG',
            'description' => 'Failing organization creation',
            'active' => true
        ]);

        // Mockando a criação da Organization
        Mockery::mock(Organization::class)
            ->shouldReceive('create')
            ->andReturnNull();      // Isso simula uma falha na criação, que deve lançar a exceção

        // Espera-se que seja lançada a exceção
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('A Organization NÃO foi inserida.');

        $this->organizationService->createOrganization($request);
    }

    public function testInsertThrowsExceptionWhenTransactionFails()
    {
        // Definindo o ID de usuário que não existe
        $userId = 999;

        // Criando um mock para o Request
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('only')->with(['name', 'acronym', 'description', 'active'])->andReturn([
            'name' => 'Org Test',
            'acronym' => 'ORG',
            'description' => 'A test organization',
            'active' => true
        ]);

        // Espera-se que o método checkUser lance uma exceção
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Usuário NÃO existe.');

        $this->organizationService->insert($request);  // Tentando inserir com um ID de usuário inválido
    }    

    
}
