<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique();
            }
    
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable();
            }
    
            if (!Schema::hasColumn('users', 'user_image')) {
                $table->string('user_image')->nullable();
            }
    
            if (!Schema::hasColumn('users', 'phone_no')) {
                $table->string('phone_no')->nullable();
            }
    
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
    
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
    
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
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
            $table->dropColumn([
                'username',
                'role',
                'user_image',
                'phone_no',
                'address',
                'first_name',
                'last_name',
            ]);
        });
    }
}
