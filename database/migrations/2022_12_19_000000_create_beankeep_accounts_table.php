<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beankeep_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type');
            $table->string('number')->unique();

            // TODO(zmd): polymorphic: keepable_id, keepable_type (class
            //   string)
            //
            //  bootable trait
            //
            // Beankeep/Models/Account::find(1)->keepable;
            //    <-- App/Models/HomeownerAccount() // -> use Keepable;
            //
            // HomeownerAccount::find(1)->getMorphClass(); -> (keepable_type)
            //
            // beankeep:make:keepable HomeownerAccount
            // contract/interface, trait
            //   -> getBeankeepFields();
            // class User implements IsBeankeepAccount
            // {
            //    use Keepable;
            //
            //    public function getBeankeepFields(): array
            //    {}
            // }
            //
            // update event, automatically deal with audit log / block

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beankeep_accounts');
    }
};
