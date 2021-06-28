<?php

use App\Models\Gateway;
use Illuminate\Database\Migrations\Migration;

class MakeBraintreeProviderVisible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Gateway::where('id', 50)->update(['visible' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
