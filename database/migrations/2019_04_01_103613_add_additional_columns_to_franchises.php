<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnsToFranchises extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->string('logo_url');
            $table->text('database');
            $table->text('db_user');
            $table->text('db_password');
            $table->string('from_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn('logo_url');
            $table->dropColumn('database');
            $table->dropColumn('db_user');
            $table->dropColumn('db_password');
            $table->dropColumn('from_email');
        });
    }
}
