<?php

use Illuminate\Database\Migrations\Migration;

class MakeEndDateNullableInEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('events', function($table)
		{
			DB::statement("ALTER TABLE `mng_events` MODIFY `end_date` DATE NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('events', function($table)
		{
			DB::statement("ALTER TABLE `mng_events` MODIFY `end_date` DATE NOT NULL;");
		});
	}

}