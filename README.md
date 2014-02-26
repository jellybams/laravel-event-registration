#Event Registration System for Laravel 4

A package that provides a way of creating events, venues, attendees and instructors. This documentation is still a work in progress. 

## Set up

Install the [package](https://packagist.org/packages/spoolphiz/events) using composer and add the following entry to the array of service providers found in app/config/app.php:

`'Spoolphiz\Events\EventsServiceProvider',`

Then, update the autoload file: 

`composer.phar dump-autoload`

Finally, run all the migrations to create the necessary database tables:

`php artisan migrate --package=spoolphiz/events`

##Usage

This package was written with the intention of being used as part of a RESTFul API. Here are some examples:

###API resource routes



###Working with venues

It's reocmmended that the models are interacted with via their corresponding repository classes. This means injecting the Venue repository into your controller:

```php
use \Input;
use \Response;
use Spoolphiz\Events\Interfaces\VenueRepository;

class ApiVenuesController extends ApiBaseController {

	protected $venue;
	
	public function __construct(VenueRepository $venue)
	{
		$this->venue = $venue;
	}
...
}
```

Now lets create a controller function for adding a new venue:

```php
/**
 * Create a new venue
 *
 * @return Response (json)
 */
public function postCreate()
{	
	$venue = $this->venue->newVenue();
	$inputData = Input::get();
	$venue->fill($inputData);
	$venue->validate();
	
	//since validation passed, lets geocode this address
	$venue->geocode();

	if (!$venue->save())
	{
		App::abort(422, 'Resource failed to create');
	}

	return Response::json($venue->toArray());
}

```

Updating the venue is very similar:

```php
/**
 * Update a venue's info
 *
 * @return Response (json)
 */
public function putSingle($id)
{	
	$venue = $this->venue->find($id);
	$venue->fill(Input::get());
	$venue->validate();

	$venue->geocode();

	if (!$venue->save())
	{
		App::abort(422, 'Resource failed to updated');
	}

	return Response::json($venue->toArray());
}
```

...how about getting a list of all venues?

```php
/**
 * Get a listing of venues
 *
 * @return Response (json)
 */
public function getList()
{		
	$filters = Input::get();
	
	if( empty($filters) )
	{
		$data = $this->venue->all();
	}
	else
	{
		$data = $this->venue->filtered($this->parseFilters($filters));
	}
	
	
	if( empty($data) )
	{
		$data = array();
	}
	elseif( is_object($data) ) {
		$data = $data->toArray();
	}
	
	return Response::json($data);
}
```

Notice there is a check for Input data at the beginning of getList(), searching/filtering data is covered below in the section "Searching/Filtering Data"


###Searching/Filtering Data

The following functionality is available to be passed in as filter fields. IF working in a single page app, you should JSON.stringify() each of the filter variables. This means you'll also need to decode the filter fields on the server side. If you're not using this package as part of an API you can simply create an associative array with the same structure as below and pass that to the filtered() function of each resource repository (venues, events, users, attendees).

```javascript
{
	total : 0|1 //returns the count of events matching the rest of the filter criteria
	filter : { 
				type : "AND"|"OR", //it is currently not possible to mix AND and OR in the same query
				filter : [
							{
								name : "events.start_date",
								operator: ">=" //possible operators are: =, !=, <, <=, >, >=, in, not in, starts with, ends with, contains, not contains, search
								value : "2014-02-02"
							}
							//add more field queries here
						]
			},
	sort : [
				{field : "venues.city", dir : "ASC"} //you can add more fields to sort by, format is table_name.field_name
			],
	fields : ["events.*", "venues.city"], //this should contain an array of fields to return in format of table_name.field_name or table_name.*
	limit : 10, //number of results you want
	page : 0 //0 based page number for results that contain more records than requested in the 'limit' field above
}
```


