<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inotifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('delivery_method', 255);
            $table->char('order_status', 4)->nullable();
            $table->boolean('auto');
            $table->text('message');
            $table->timestamps();
        });

    }

/*    public function keyOrder()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->integer('order_id')->unsigned()->default(1);
            $table->foreign('order_id')->references('id')->on('sh_order');
        });
        //order_status: a, d, c, 
    }*/

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inotifications');

        /*Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });*/
    }
}
