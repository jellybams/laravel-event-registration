<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAmountToDecimal extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attendees', function($table)
		{
			DB::statement("ALTER TABLE `mng_attendees` CHANGE `total_amount` `total_amount` DECIMAL (10,2);");
			DB::statement("ALTER TABLE `mng_attendees` CHANGE `amount_paid` `amount_paid` DECIMAL (10,2);");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('attendees', function($table)
		{
			DB::statement("ALTER TABLE `mng_attendees` CHANGE `total_amount` `total_amount` FLOAT;");
			DB::statement("ALTER TABLE `mng_attendees` CHANGE `amount_paid` `amount_paid` FLOAT;");
		});
	}

}