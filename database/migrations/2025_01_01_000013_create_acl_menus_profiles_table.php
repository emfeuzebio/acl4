<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

/* SUGESTÃO DE MELHORIA

Porque NÃO usar $table->increments('id') em Tabelas Pivot
📌 Razões Técnicas:
1. Chave Primária Composta é Melhor:
php

// ✅ CORRETO - Chave primária composta
$table->primary(['menu_id', 'profile_id']);

// ❌ EVITE - ID autoincrement desnecessário
$table->increments('id');
$table->unique(['menu_id', 'profile_id']);

// ❌ COM autoincrement - Permite duplicatas
menu_id | profile_id | id
1       | 1          | 1
1       | 1          | 2  // ← DUPLICATA PERMITIDA!

// ✅ SEM autoincrement - Bloqueia duplicatas
menu_id | profile_id  
1       | 1          
1       | 1          // ← ERRO: Chave duplicada


Estrutura Ideal:


    Schema::create('acl_menu_profile', function (Blueprint $table) {
        // Chaves estrangeiras
        $table->foreignId('menu_id')->constrained()->onDelete('cascade');
        $table->foreignId('profile_id')->constrained()->onDelete('cascade');
        
        // Campos extras
        $table->integer('position')->default(0);
        $table->enum('active', ['Y', 'N'])->default('Y');
        $table->timestamps();
        
        // Chave primária composta (IMPORTANTE)
        $table->primary(['menu_id', 'profile_id']);
    });



*/

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('acl_menu_profile')) {
            Schema::create('acl_menu_profile', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('menu_id');     // foreign key
                $table->unsignedInteger('profile_id');  // foreign key
                $table->enum('active', ['Y', 'N'])->default('Y');
                $table->timestamps();

                $table->unique(["menu_id","profile_id"], 'menu_id_profile_id_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                $table->foreign('menu_id')
                      ->references('id')->on('acl_menus')->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('profile_id')
                      ->references('id')->on('acl_profiles')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_menu_profile');
    }
};
