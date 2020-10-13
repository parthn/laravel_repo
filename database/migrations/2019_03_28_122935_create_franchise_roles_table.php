<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFranchiseRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franchise_roles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('role_name')->unique();

            $table->unsignedInteger('franchise_id')
                ->nullable();

            $table->foreign('franchise_id')
                ->references('id')
                ->on('franchises')
                ->onDelete('cascade');



            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('franchise_roles');
    }
}
