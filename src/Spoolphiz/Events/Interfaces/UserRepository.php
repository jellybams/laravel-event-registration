<?php
namespace Spoolphiz\Events\Interfaces;

interface UserRepository {
	
	public function find($id);
	
	public function softFind($id);
	
	public function all();
	
	public function newUser();
	
	public function makeApiKey();
	
}