<?php
namespace Spoolphiz\Events\Interfaces;

interface GeoRepository {
	
	/**
	 * get a country list
	 *
	 * @param $filters  Filters to use when querying countries
	 *
	 * @return collection
	 */
	public function getCountries($filters);
	

	/**
	 * get a single country by id
	 *
	 * @param $id  The country id to get
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Country
	 */
	public function getCountry($id);
}