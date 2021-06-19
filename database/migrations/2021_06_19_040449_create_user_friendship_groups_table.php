<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFriendshipGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friendship_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('friendship_id')->unsigned();
            $table->morphs('friend');
            $table->integer('group_id')->unsigned();

            $table->foreign('friendship_id')
                ->references('id')
                ->on('friendships')
                ->onDelete('cascade');

            $table->unique(['friendship_id', 'friend_id', 'friend_type', 'group_id'], 'unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_friendship_groups');
    }
}
