<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Models\Organization;
use App\Models\User;

class OrganizationService
{
    protected $organizationRepository;

    public function __construct(OrganizationRepositoryInterface $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }    

    /*
        Este Service implements SOLID principles
        1 - Single Responsibility Principle (SRP) - princípio de responsabilidade única (SRP)
            Actions (métodos) distintas para cada uma das operações
        2 - Open/Closed Principle (OCP): 
            O OrganizationService pode ser estendido para adicionar mais funcionalidades 
            (ex. processamento de pagamento ou envio de notificação) sem modificar o código existente. 
            Ele pode ser aberto para expansão e fechado para modificação.
        3 - Testabilidade: Agora você pode facilmente testar cada método de forma isolada. 
        4 - Manutenção: Se a lógica de uma das operações precisar ser alterada, 
            como a atualização de estoque ou de saldo do cliente, você pode fazer isso facilmente sem impactar as outras operações.
    */
    public function insert(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. Verifica a disponibilidade do produto
                $userId = 1;

                /**
                 * Implementação com passos internos nesse mesmo método
                 */
                // $user = User::findOrFail($userId);   // generate default exception: 'results for model [App\Models\User] 10'
                // $user = User::find($userId);         // so I can customize the Exception

                // if (! $user) {
                //     throw new Exception('Usuário NÃO existe.');
                // }     

                    /**
                     * Implementação aqui no Service em passos métodos segmentados únicos
                     */
                    // $user = $this->checkUser($userId);    

                        /**
                         * Implementação via Repository
                         */
                        $user = $this->organizationRepository->checkUser($userId);    

            // 2. Cria a ordem de venda
                /**
                 * Implementação com passos internos nesse mesmo método
                 */
                // $userProfiles = $user->hasProfiles();
                // if (! $userProfiles) {
                //     throw new Exception('O Usuário NÃO tem Perfil de Acesso necessário.');
                // }

                    /**
                     * Implementação aqui no Service em passos métodos segmentados únicos
                    */
                    // $userProfiles = $this->checkProfile($user);

                        /**
                         * Implementação via Repository
                         */
                        $userProfiles = $this->organizationRepository->checkProfile($user);

            // 2. Cria a ordem de venda
                // $organization = Organization::Create(
                //     $request->only(['name', 'acronym', 'description', 'active'])
                // );  

                    /**
                     * Implementação aqui no Service em passos métodos segmentados únicos
                    */
                    // $organization = $this->createOrganization($request);

                        /**
                         * Implementação via Repository
                         */
                        $organization = $this->organizationRepository->create($request);


                // $order = new Order();
                // $order->product_id = $productId;
                // $order->customer_id = $customerId;
                // $order->quantity = $quantity;
                // $order->total_price = $product->price * $quantity;
                // $order->save();

            // 3. Atualiza o estoque do produto
                // $product->stock -= $quantity;
                // $product->save();

            // 4. (Opcional) Atualiza o saldo do cliente, se necessário
                // $customer = Customer::findOrFail($customerId);
                // $customer->balance -= $order->total_price;
                // $customer->save();

            // Se tudo ocorrer bem, commit a transação e retorna TRUE
            DB::commit();               // basta este se quiser devolver TRUE
            return $organization;       // assim se quiser devolver um objeto, valor ou algo que deseje
        } catch (Exception $e) {
            DB::rollBack();     // Se qualquer parte do processo falhar, reverte a transação
            throw $e;           // Relança o erro para ser tratado no controller
        }
    }

    public function checkUser($userId) 
    {
        $user = User::find($userId);    

        if (! $user) {
            throw new Exception('Usuário NÃO existe.');
        }   
        
        return $user;
    }

    public function checkProfile(User $user) 
    {
        $userProfiles = $user->hasProfiles();

        if (! $userProfiles) {
            throw new Exception('O Usuário NÃO tem Perfil de Acesso necessário.');
        }
        
        return $userProfiles;
    }

    public function createOrganization(Request $request) 
    {
        $organization = Organization::Create(
            $request->only(['name', 'acronym', 'description', 'active'])
        );  

        if (! $organization) {
            throw new Exception('A Organization NÃO foi inserida.');
        }
        
        return $organization;
    }
}