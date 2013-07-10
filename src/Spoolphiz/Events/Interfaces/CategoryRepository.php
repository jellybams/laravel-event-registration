<?php
namespace Spoolphiz\Events\Interfaces;

interface CategoryRepository {
	
	public function find($id);
	
	public function all();
	
	public function newCategory();
	
}