<?php
namespace Spoolphiz\Events\Interfaces;

interface GeoRepository {
	
	public function getCountries($filters);
	
	public function getCountry($id);
}