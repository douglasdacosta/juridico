<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('perfil_acesso')->default(2);
            $table->date('plan_expiration_date')->nullable();
            $table->string('status')->default('A');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'perfil_acesso' => 1,
                'password' => '$2y$10$W8aQ3AC1YCe4lg0bvio1AOQkBK4xRjLyeH0SvxkyqZcFFhjVq9Gxi'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
