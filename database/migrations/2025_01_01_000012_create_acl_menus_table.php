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
        if (!Schema::hasTable('acl_menus')) {
            Schema::create('acl_menus', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('menu_id')->nullable();          // forey key auto relationship acpted null
                // $table->foreignId('parent_id')->nullable()->constrained('acl_menus')->onDelete('cascade');
                $table->string('name', 50);
                $table->string('link', 50);
                $table->integer('position')->default(0);
                $table->enum('active', ['Y', 'N'])->default('Y');

                $table->unique(["name"], 'name_ukey');
                $table->unique(["link"], 'link_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                $table->foreign('menu_id')
                      ->references('id')->on('acl_menus')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_menus');
    }
};
