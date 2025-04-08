<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Database\QueryException;
use App\Http\Requests\VeiculoRequest;
use App\Http\Resources\VeiculoResource;
use Illuminate\Http\Response;
use App\Models\Veiculo;
use Exception;

class VeiculoController extends Controller
{
    /**
     * GET: Display a listing of the resource.
     */
    // public function index()
    public function index(HttpRequest $request)
    {
        // dd("VeiculoController()->index() = listar ");
        // abordagem 1: retorna all() de JSON SEM controle de erros
        // return Veiculo::all();
        // dd($request->has('descricao'));

        // abordagem 2: retorna all() em JSON COM controle de erros
        try {

            if (! $request->has('descricao')) {
                // $veiculos = Veiculo::all();
                $veiculos = VeiculoResource::collection(Veiculo::all());    // aplica um Resource sobre a coleção
            } else {

                // implementar para que o LIKE seja em todos os campos
                // $veiculos = Veiculo::where('descricao', 'LIKE', "%{$request->descricao}%")->get();
                $veiculos = VeiculoResource::collection(Veiculo::where('descricao', 'LIKE', "%{$request->descricao}%")->get());
            }

            if ($veiculos->isEmpty()) {
                return response()->json(['message' => 'Nenhum veículo encontrado'], Response::HTTP_NOT_FOUND);
            }

            // dd($request->descricao);
            // dd($veiculos);

            return response()->json($veiculos, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar veículos', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }

    /**
     * POST: Store a newly created resource in storage.
     */
    public function store(VeiculoRequest $request)
    // public function store(HttpRequest $request)
    {
        // dd($request->all());
        // return response()->json(Veiculo::Create($request->all()), 201);

        // ABORDAGEM usando um Validator local e específico
        // $validator = Validator::make($request->all(), [
        //     'descricao' => 'required|min:4',        
        //     'tipo' => ['required','in:"Automóvel","Van","Micrônibus","Ônibus"'],
        //     'marca_modelo' => 'required|min:4',
        //     'capacidade' => 'required',
        //     'motorista' => '',
        //     'telefone' => 'required',
        //     'observacao' => '',            
        //     'ativo' => ['required','in:"SIM","NÃO"'],
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        

        // ABORDAGEM usando o Validator do VeiculoRequest é mais simplificado, transparente e não precisa ficar repetindo
        try {
            $veiculo = Veiculo::Create($request->all());

            return response()->json($veiculo, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar o veículo', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }

    /**
     * GET: Display the specified resource.
     */
    public function show($id)
    // public function show($id, Authenticatable $user)
    // public function show(Veiculo $veiculo)
    {

        /**
         * FUNCIONA para ver todas as habilidades do Token se preciso 
         */
        // $user = Auth::user();
        // $token = $user->tokens->first();
        // dd($token->abilities);        

        /**
         * FUNCIONA retorna true se o token (can-pode) tem uma habilidade mas tem que pegar o User do Auth antes
         */
        // $user = Auth::user();
        // dd($user->tokenCan('veiculo.index'));

        /**
         * FUNCIONA retorna true se o token (can-pode) tem uma habilidade direto
         */
        // dd(auth()->user()->tokenCan('veiculo.index'));

        
        // dd("show ($id)");
        // ABORDAGEM 1: usa o findOrFail() de forma simplificada SEM controle de erros
        // return Veiculo::findOrFail($id);

        // dd("show ($veiculo)");
        // ABORDAGEM 2: ja recebe o objeto do Model de forma simplificada SEM controle de erros
        // return response()->json($veiculo, Response::HTTP_OK); 

        // ABORDAGEM 3: ja recebe o objeto do Model, passa no Resource de forma simplificada SEM controle de erros
        // $veiculo = new VeiculoResource($veiculo); 
        // return response()->json($veiculo, Response::HTTP_OK); 

        // ABORDAGEM 4: usa o findOrFail() e relacionamentos e depois retorna JSON com resposta COM controle de erros
        try {
            // $veiculo = Veiculo::with("viagens")->findOrFail($id);

            // aplicando um Resource sobre a coleção COM dados do relacionamento
            // $veiculo = new VeiculoResource(Veiculo::with("viagens")->findOrFail($id)); 
            $veiculo = new VeiculoResource(Veiculo::findOrFail($id)); 

            return response()->json($veiculo, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Veículo não encontrado'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar o veículo', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }

    /**
     * PUT|PATCH: Update the specified resource in storage.
     */
    public function update(VeiculoRequest $request, $id)
    {
        // dd("update ($id)");
        // dd($request->all());

        // abordagem 1: usa o update() e depois retorna JSON SEM controle de erros
        // $veiculo = Veiculo::findOrFail($id);       // recupera o veículo a ser atualizado
        // $veiculo->update($request->all());          // atualiza o veículo com todos os dados vindos no request aplicanco as rules
        // return response()->json($veiculo);

        // abordagem 2: usa o update() e depois retorna JSON COM controle de erros
        try {
            $veiculo = Veiculo::findOrFail($id);
            $veiculo->update($request->all());
            // $veiculo->update($request->ativo);

            return response()->json($veiculo, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Veículo não encontrado'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar o veículo', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }

    /**
     * DELETE: Remove the specified resource from storage.
     */
    // public function destroy(string $id, Authenticatable $user)
    public function destroy(string $id)
    {
        // dd($user);
        // dd($user->tokenCan('veiculo.index'));
        // dd($user->tokenCan('*'));

        // dd("destroy ($id)");

        // abordagem 1: usa o delete e depois retorna resposta vazia status 204-No Content
        // $veiculo = Veiculo::where('id',$id)->delete();
        // return response()->json(null, 204);

        // abordagem 2: usa o destroy() e depois retorna resposta vazia status 204-No Content
        // $veiculo = Veiculo::destroy($id);
        // return response()->json(null, 204);

        // abordagem 3: usa o destroy() e depois retorna resposta vazia status 204-No Content do Laravel
        // $veiculo = Veiculo::destroy($id);
        // return response()->noContent();

        // abordagem 4: usa o delete() e depois retorna JSON com resposta e controle de erros
        try {

            $veiculo = Veiculo::findOrFail($id);
            $veiculo->delete();

            // nota: padrão é Response::HTTP_NO_CONTENT porém a resposta seria em branco, como quero retornar um message usei: HTTP_OK
            return response()->json(['message' => 'Veículo excluído com sucesso'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {

            return response()->json(['error' => 'Veículo não encontrado'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {

            // Verifica se o erro é do tipo violação de restrição de integridade
            if ($e->getCode() == '23000') {
                return response()->json([
                    'error' => 'Não é possível excluir o Veículo, pois existem Viagens associadas.'
                ], Response::HTTP_BAD_REQUEST); // 400 Bad Request
            }            
        } catch (Exception $e) {

            return response()->json(['error' => 'Erro ao deletar o veículo', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }
}
