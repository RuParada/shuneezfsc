<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationssaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notificationssave', function (Blueprint $table) {
            $table->increments('id');
            $table->string('delivery_method', 255);
            $table->string('order_status', 50);
            $table->text('text');
            $table->timestamps();
            /*$table->integer('order_id')->unsigned()->default(1);
            $table->foreign('order_id')->references('id')->on('sh_order');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('sh_notificationssave', function (Blueprint $table) {
            $table->dropColumn('order_id');
        });*/
        Schema::dropIfExists('notificationssave');
        //CREATE TABLE sh_notificationssave ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY );
        //CREATE TABLE sh_notificationssand ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY );
    }
}
