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
        // Schema::dropIfExists('acl_organizacaos');
        
        if (!Schema::hasTable('acl_organizations')) {
            Schema::create('acl_organizations', function (Blueprint $table) {
                $table->increments('id'); 
                $table->string('name', 128);
                $table->string('acronym', 30);
                $table->text('description')->nullable();  
                $table->enum('active', ['Y', 'N'])->default('Y');
                $table->timestamps();               
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_organizations');
    }
};
