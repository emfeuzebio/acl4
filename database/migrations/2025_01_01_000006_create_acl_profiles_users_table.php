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
        if (!Schema::hasTable('acl_profile_user')) {
            Schema::create('acl_profile_user', function (Blueprint $table) {
                $table->increments('id');      
                $table->unsignedBigInteger('user_id');             // foreign key
                $table->unsignedInteger('profile_id');          // foreign key

                $table->unique(["user_id", "profile_id"], 'user_id_profile_id_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                $table->foreign('user_id')
                      ->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('profile_id')
                      ->references('id')->on('acl_profiles')->onDelete('restrict')->onUpdate('cascade');
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
