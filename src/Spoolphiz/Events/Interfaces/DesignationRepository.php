<?php
namespace Spoolphiz\Events\Interfaces;

interface DesignationRepository {
	
	public function find($id);
	
	public function all();

	public function filtered($filters = array() );
	
} 