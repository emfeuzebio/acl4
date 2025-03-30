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
        Schema::create('acl_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');               // Relacionamento com o usuário
            $table->unsignedInteger('system_id')->default(1);                                           // foreign key            
            $table->enum('status', ['active', 'refreshed', 'expired','invalidated','revoked'])->default('active');  // Status do token
            $table->string('ip')->nullable();                                               // IP do usuário
            $table->string('browser')->nullable();                                          // Navegador do usuário            
            $table->text('token');
            $table->timestamp('expires_at');                                                // Data de expiração
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_tokens');
    }
};
