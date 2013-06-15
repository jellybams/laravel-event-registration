<?php
namespace Spoolphiz\Events\Interfaces;

interface AttendeeRepository {
	
	public function find($id);
	
	public function all();
	
	public function delete($id);
	
}