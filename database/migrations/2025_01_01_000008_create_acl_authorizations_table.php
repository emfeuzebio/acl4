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
        // if (!Schema::hasTable('acl_roles_user')) {
        if (!Schema::hasTable('acl_authorizations')) {
            Schema::create('acl_authorizations', function (Blueprint $table) {
                $table->increments('id');      
                $table->unsignedInteger('profile_id');              // foreign key
                // $table->unsignedInteger('entity_id');               // foreign key
                $table->unsignedInteger('action_id');               // foreign key
                $table->enum('active', ['Y', 'N'])->default('Y');
                $table->timestamps();                               

                $table->unique(["profile_id", "action_id"], 'profile_id_action_id_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                $table->foreign('profile_id')
                      ->references('id')->on('acl_profiles')->onDelete('cascade')->onUpdate('cascade');

                // $table->foreign('entity_id')
                //       ->references('id')->on('acl_entities')->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('action_id')
                      ->references('id')->on('acl_actions')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_authorizations');
    }
};
