<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationssandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notificationssand', function (Blueprint $table) {
            $table->increments('id');
            $table->string('delivery_method', 255);
            $table->text('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('sh_notificationssand', function (Blueprint $table) {
            //
        });*/

        Schema::dropIfExists('notificationssand');
    }
}
