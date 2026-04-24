<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'failed_attempts')) {
                $table->unsignedInteger('failed_attempts')->default(0)->after('password');
            }

            if (! Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_attempts');
            }

            if (! Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('locked_until');
            }

            if (! Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
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
            $columns = [
                'two_factor_secret',
                'two_factor_enabled',
                'locked_until',
                'failed_attempts',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
