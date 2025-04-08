<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FEB_VeiculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('eve_veiculos')->insert([
            [
                // 'tipo' => ['required','in:"Automóvel","Van","Micrônibus","Ônibus"'],
                'descricao' => 'City Hatch azul da ANA LUCIA 4 lugares',        
                'marca_modelo' => 'Honda/City Hatch/Azul',
                'capacidade' => '4',
                'motorista' => 'Ana Lucia',
                'telefone' => '(61) 90000-0000',
                'observacao' => 'Sem Observações',            
                // 'ativo' => ['required','in:"Y","N"'],
            ],
            [
                'descricao' => 'Fiorino branco da MARIA 2 lugares',        
                'marca_modelo' => 'Fiat/Fiorino/Branco',
                'capacidade' => '2',
                'motorista' => 'Maria',
                'telefone' => '(61) 90000-0001',
                'observacao' => 'Sem Observações',            
            ],
            [
                'descricao' => 'Caminhão Mercedes Benz 8 lugares do João Pedro',        
                'marca_modelo' => 'Mercedes Benz/Caminhão/Branco',
                'capacidade' => '8',
                'motorista' => 'João Pedro',
                'telefone' => '(61) 90000-0000',
                'observacao' => 'Sem Observações',            
            ],
            [
                'descricao' => 'Monza verde da Thamires 3 lugares',        
                'marca_modelo' => 'Chevrolet/Monza/Verde',
                'capacidade' => '3',
                'motorista' => 'Thamires',
                'telefone' => '(61) 90000-0001',
                'observacao' => 'Sem Observações',            
            ],
            [
                'descricao' => 'Palio Chumbo 3 lugares do João Dória',
                'marca_modelo' => 'Fiat/Palio/Chumbo',
                'capacidade' => '3',
                'motorista' => 'João Dória',
                'telefone' => '(61) 90000-0000',
                'observacao' => 'Sem Observações',            
            ],
            [
                'descricao' => 'Fiesta branco da Martha 2 lugares',
                'marca_modelo' => 'Ford/Fista/Branco',
                'capacidade' => '2',
                'motorista' => 'Martha',
                'telefone' => '(61) 90000-0001',
                'observacao' => 'Sem Observações',            
            ],
            [
                'descricao' => 'Civic preto do João 4 lugares',
                'marca_modelo' => 'Honda/Civic/Preto',
                'capacidade' => '4',
                'motorista' => 'João',
                'telefone' => '(61) 90000-0000',
                'observacao' => 'Sem Observações',            
            ],
            [
                'descricao' => 'Fusca azul da Maria 2 lugares',
                'marca_modelo' => 'Volkswagen/Fusca/Azul',
                'capacidade' => '2',
                'motorista' => 'Maria',
                'telefone' => '(61) 90000-0001',
                'observacao' => 'Sem Observações',            
            ],
        ]);
    }
}
