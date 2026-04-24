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
        if (! Schema::hasTable('clientes')) {
            return;
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'lgpd_consent_at')) {
                $table->timestamp('lgpd_consent_at')->nullable()->after('email');
            }

            if (! Schema::hasColumn('clientes', 'lgpd_purpose')) {
                $table->text('lgpd_purpose')->nullable()->after('lgpd_consent_at');
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
        if (! Schema::hasTable('clientes')) {
            return;
        }

        Schema::table('clientes', function (Blueprint $table) {
            $columns = ['lgpd_purpose', 'lgpd_consent_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('clientes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
