<?php

use Illuminate\Database\Migrations\Migration;

class CreateInitialTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('countries', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->smallInteger('num_code');
			$table->string('iso1_code', 2);
			$table->string('name', 80);
			$table->string('name_caps', 80);
			$table->string('iso3_code', 3);
		});

		Schema::table('venues', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('name', 50);
			$table->string('phone', 32);
			$table->string('address1', 50);
			$table->string('city', 50);
			$table->string('state', 50);
			$table->string('zip', 10);

			$table->integer('country_id')->unsigned();
			$table->float('lat', 10, 6);
			$table->float('long', 10, 6);

			//alex m: removed this foreign key constraint because data import 
			//sometimes has to import country id of 0 (ie not found in countries table)
			//$table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

			$table->timestamps();
		});

		Schema::table('categories', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('label', 40);
			$table->string('link', 250);
		});

		Schema::table('events', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('sku', 32)->nullable();
			$table->integer('category_id')->unsigned();
			$table->integer('venue_id')->unsigned();
			$table->date('start_date');
			$table->date('end_date');
			$table->string('title', 100);
			$table->string('contact_phone', 30);
			$table->decimal('seminar_price', 10, 2);
			$table->decimal('full_price', 10, 2);
			$table->integer('capacity');
			$table->integer('status');
			$table->timestamps();
			$table->boolean('create_seminaronly')->default(0);
			$table->boolean('create_fullevent')->default(1);

			//$table->foreign('category_id')->references('id')->on('categories'); //->onDelete('cascade');
			//$table->foreign('venue_id')->references('id')->on('venues'); //->onDelete('cascade');
		});

		Schema::table('attendees', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->integer('event_id')->unsigned();
			$table->integer('crm_contact_id')->unsigned();
			$table->string('name', 100);
			$table->string('email', 100);

			$table->date('registration_date');
			$table->float('amount_paid', 16, 2);
			$table->float('total_amount', 16, 2);
			$table->string('phone_number', 30);

			$table->boolean('seminar_only');
			$table->timestamps();

			//$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
		});

		Schema::table('attendee_comments', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->integer('attendee_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->text('comment');
			$table->timestamps();

			//$table->foreign('attendee_id')->references('id')->on('attendees')->onDelete('cascade');

		});

		Schema::table('users', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('username', 32);
			$table->string('email', 100);
			$table->unique('email');

			$table->string('name', 100);
			$table->string('password', 64);
			$table->integer('role_id')->unsigned()->default(1);
			$table->boolean('active')->default(1);
			$table->string('api_key', 32);
			$table->timestamps();
		});

		Schema::table('event_instructor', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->integer('event_id')->unsigned();
			$table->integer('user_id')->unsigned();

			//$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
			//$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::table('event_status', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('label', 64);
		});

		Schema::table('user_roles', function($table)
		{
			$table->create();
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('label', 25);
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_roles');
		Schema::drop('event_status');
		Schema::drop('event_instructor');
		Schema::drop('users');
		Schema::drop('attendee_comments');
		Schema::drop('attendees');
		Schema::drop('events');
		Schema::drop('categories');
		Schema::drop('venues');
		Schema::drop('countries');
	}

}