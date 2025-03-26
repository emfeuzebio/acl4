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
        if (!Schema::hasTable('acl_actions')) {
            Schema::create('acl_actions', function (Blueprint $table) {
                $table->increments('id');  
                $table->unsignedInteger('entity_id');          // foreing key
                $table->string('action', 50);
                $table->string('route', 30)->nullable();
                $table->text('description')->nullable();
                // $table->timestamps();  

                $table->unique("route", 'route_ukey');
                
                $table->foreign('entity_id')
                    ->references('id')->on('acl_entities')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acl_actions');
    }
};
