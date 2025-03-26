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
        if (!Schema::hasTable('acl_profiles')) {
            Schema::create('acl_profiles', function (Blueprint $table) {
                $table->increments('id');      
                $table->unsignedInteger('system_id');          // chave estrangeira
                $table->string('name', 128);
                $table->string('acronym', 30);
                $table->text('description')->nullable();
                $table->enum('active', ['Y', 'N'])->default('Y');
                $table->timestamps();                                   // created_at e updated_at fields

                $table->unique(["system_id", "name"], 'system_id_name_ukey');
                $table->unique(["system_id", "acronym"], 'system_id_acronym_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                $table->foreign('system_id')
                      ->references('id')->on('acl_systems')->onDelete('restrict')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_profiles');
    }
};
