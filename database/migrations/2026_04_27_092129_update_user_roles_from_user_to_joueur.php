<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateUserRolesFromUserToJoueur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'joueur']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')
            ->where('role', 'joueur')
            ->whereNotIn('role', ['admin'])
            ->update(['role' => 'user']);
    }
}
