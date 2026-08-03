<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('financeiros', function (Blueprint $table) {
            $table->decimal('valor_pago', 12, 2)->nullable()->after('reembolso');
        });

        Schema::table('financeiro_parcelas', function (Blueprint $table) {
            $table->decimal('valor_pago', 12, 2)->nullable()->after('valor');
        });
    }

    public function down()
    {
        Schema::table('financeiros', function (Blueprint $table) {
            $table->dropColumn('valor_pago');
        });

        Schema::table('financeiro_parcelas', function (Blueprint $table) {
            $table->dropColumn('valor_pago');
        });
    }
};
