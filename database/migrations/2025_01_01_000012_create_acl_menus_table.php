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
                $table->unsignedInteger('system_id');           // foreing key
                $table->unsignedInteger('menu_id');             // foreing key
                $table->string('name', 50);
                $table->string('icon', 50)->nullable();
                $table->string('route', 50)->nullable();
                $table->unsignedInteger('position')->default(0);  
                $table->enum('active', ['Y', 'N'])->default('Y');
                // $table->timestamps();  

                // $table->unique("route", 'route_ukey');
                $table->unique(["system_id", "name"], 'system_id_name_ukey');
                $table->unique(["system_id", "route"], 'system_id_route_ukey');
                
                $table->foreign('system_id')
                    ->references('id')->on('acl_systems')->onDelete('restrict')->onUpdate('cascade');

                // autorelacionamento    
                // $table->foreign('menu_id')
                //     ->references('id')->on('acl_menus')->onDelete('cascade')->onUpdate('cascade');
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
