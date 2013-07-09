<?php
namespace Spoolphiz\Events\Interfaces;

interface UserRepository {
	
	public function find($id);
	
	public function all();
	
	public function newUser();
	
}