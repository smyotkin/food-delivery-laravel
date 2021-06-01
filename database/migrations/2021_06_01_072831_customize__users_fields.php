<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomizeUsersFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->after('name');

            $table->integer('city_id')->unsigned()->after('id');
            $table->integer('position_id')->nullable()->default(0)->unsigned()->after('city_id');

            $table->char('phone', 11);

            $table->timestamp('last_seen')->nullable();
            $table->tinyInteger('is_active')->unsigned();

            $table->renameColumn('name', 'first_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
