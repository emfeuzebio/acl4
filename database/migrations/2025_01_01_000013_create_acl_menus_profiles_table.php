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
