<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dropar a tabela, se existir, antes de criar uma nova
        // Schema::dropIfExists('acl_sistemas');
        
        if (!Schema::hasTable('acl_entities')) {
            Schema::create('acl_entities', function (Blueprint $table) {
                $table->increments('id');      
                // $table->unsignedInteger('system_id');               // chave estrangeira
                $table->string('model', 30);
                $table->string('table', 50);
                $table->text('description');
                $table->enum('active', ['Y', 'N'])->default('Y');
                $table->timestamps();                               // Campos created_at e updated_at

                $table->unique(["model"], 'model_ukey');
                $table->unique(["table"], 'table_ukey');

                // $table->unique(["system_id", "model"], 'system_id_model_ukey');
                // $table->unique(["system_id", "table"], 'system_id_table_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_sistema_id_foreign
                // $table->foreign('system_id')
                //       ->references('id')->on('acl_systems')->onDelete('restrict')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_entities');
    }
};
