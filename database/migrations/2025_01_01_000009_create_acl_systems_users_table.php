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
        if (!Schema::hasTable('acl_system_user')) {
            Schema::create('acl_system_user', function (Blueprint $table) {
                $table->increments('id');      
                $table->unsignedInteger('system_id');               // foreign key
                $table->unsignedBigInteger('user_id');              // foreign key

                $table->unique(["system_id","user_id"], 'system_id_user_id_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                $table->foreign('system_id')
                      ->references('id')->on('acl_systems')->onDelete('restrict')->onUpdate('cascade');
                      $table->foreign('user_id')
                      ->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_system_user');
    }
};
