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
        if (!Schema::hasTable('acl_systems')) {
            Schema::create('acl_systems', function (Blueprint $table) {
                $table->increments('id');      
                // $table->unsignedInteger('organization_id');          // chave estrangeira
                $table->string('name', 128);
                $table->string('acronym', 30);
                $table->text('description')->nullable();
                $table->enum('active', ['Y', 'N'])->default('Y');
                $table->timestamps();                                   // created_at e updated_at fields

                $table->unique(["name"], 'name_ukey');
                $table->unique(["acronym"], 'acronym_ukey');

                // $table->unique(["organization_id", "name"], 'organization_id_name_ukey');
                // $table->unique(["organization_id", "acronym"], 'organization_id_acronym_ukey');

                // $table->foreign('organizacao_id','<tabela>_<coluna_estrangeira>_foreign')
                // cria o relacionamento com a conveção de nome padrão: acl_sistemas_organizacao_id_foreign
                // $table->foreign('organization_id')
                //       ->references('id')->on('acl_organizations')->onDelete('restrict')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_systems');
    }
};
