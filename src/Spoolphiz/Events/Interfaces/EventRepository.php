<?php
namespace Spoolphiz\Events\Interfaces;

interface EventRepository {
	
	public function findWithAccess($id, $user);
	
	public function all($user);
	
	public function newEvent();
	
	public function delete($id);
	
}