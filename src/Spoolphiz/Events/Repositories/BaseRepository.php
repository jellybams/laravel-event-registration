<?php
namespace Spoolphiz\Events\Repositories;
use Spoolphiz\Events\Interfaces\EventRepository;

abstract class BaseRepository
{
		
	public function allowedConditions()
	{
		return array('=', '!=', '<', '<=', '>', '>=', 'starts with', 'ends with', 'contains', 'not contains');
	}
	
	
	public function returnFields($filters)
	{
		return (isset($filters['fields'])) ? $filters['fields'] : array('*') ;
	}
	
	
	public function parseFilters($filters)
	{
		foreach( $filters as &$item )
		{
			$item = json_decode($item, true);
		}
		
		return $filters;
	}
	
	
	
	public function buildFilteredCollection($model, $filters, $currentUser, $aclUserRelationName, $object = null)
	{
		$filterFields = (isset($filters['filter']['fields'])) ? $filters['filter']['fields'] : array();
		$limit = (isset($filters['limit']) && is_numeric($filters['limit'])) ? $filters['limit'] : 0;
		$page = (isset($filters['page']) && is_numeric($filters['page'])) ? $filters['page'] : 0;

		$lastFilterFieldPos =  (count($filterFields)>0) ? count($filterFields)-1 : 0;
		
		// if filter fields exist...
		if( isset($filterFields[$lastFilterFieldPos]) )
		{
			//if the Collection object has not yet been instantiated...
			if( !is_object($object) )
			{
				//set up pagination of records
				if( $limit !== 0 )
				{
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$object = $model::take($limit);
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}()->take($limit);
					}
					
					
					if( $page !== 0 )
					{
						$object = $object->skip($limit*($page-1));
					}
					
					$object = $object->where($filterFields[$lastFilterFieldPos]['field_name'], 
											$filterFields[$lastFilterFieldPos]['operator'], 
											$filterFields[$lastFilterFieldPos]['value']);
				}
				else
				{	
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$object = $model::where($filterFields[$lastFilterFieldPos]['field_name'], 
												$filterFields[$lastFilterFieldPos]['operator'], 
												$filterFields[$lastFilterFieldPos]['value']);
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}()->where($filterFields[$lastFilterFieldPos]['field_name'], 
												$filterFields[$lastFilterFieldPos]['operator'], 
												$filterFields[$lastFilterFieldPos]['value']);
					}
					
					
				}
				
				unset($filterFields[$lastFilterFieldPos]);
				$filters['filter']['fields'] = $filterFields;
				
				return $this->buildFilteredCollection($model, $filters, $currentUser, $aclUserRelationName, $object);
			}
			//the collection object is already instatiatied, this is a recursive call
			else
			{	
				$filterMethod = 'where';
				
				if( isset($filters['filter']['type']) && strtoupper($filters['filter']['type'] == 'OR' ) )
				{	
					$filterMethod = 'orWhere';
				}
				
				$object->{$filterMethod}($filterFields[$lastFilterFieldPos]['field_name'], 
										$filterFields[$lastFilterFieldPos]['operator'], 
										$filterFields[$lastFilterFieldPos]['value']);
				
				unset($filterFields[$lastFilterFieldPos]);
				$filters['filter']['fields'] = $filterFields;

				return $this->buildFilteredCollection($model, $filters, $currentUser, $aclUserRelationName, $object);
			}
		}
		//if filter fields don't exist, check if pagination filters exist
		else
		{
			//if we don't have a collection object, filter fields never existed 
			//so no collection object was created yet... 
			//lets check if pagination needs to be set
			if( !is_object($object) )
			{
				if( $limit !== 0 )
				{
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$object = $model::take($limit);
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}()->take($limit);
					}
					
					if( $page !== 0 )
					{
						$object = $object->skip($limit*($page-1));
					}
				}
				else
				{
					//no pagination filters were passed, looks like we just want all records
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$instance = new $model;
						$object = $instance->newQuery();
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}();
					}
					
				}
			}
			
		}
		
		//set up return fields
		$returnFields = $this->returnFields($filters);
		
		return $object->get($returnFields);
	}
	
	
	
	
	
	
	/*
	public function buildFilteredCollection($model, $filters, $currentUser, $aclUserRelationName, $object = null)
	{
		$filterFields = (isset($filters['filter']['fields'])) ? $filters['filter']['fields'] : array();
		$limit = (isset($filters['limit']) && is_numeric($filters['limit'])) ? $filters['limit'] : 0;
		$page = (isset($filters['page']) && is_numeric($filters['page'])) ? $filters['page'] : 0;

		$lastFilterFieldPos =  (count($filterFields)>0) ? count($filterFields)-1 : 0;
		
		// if filter fields exist...
		if( isset($filterFields[$lastFilterFieldPos]) )
		{
			//if the Collection object has not yet been instantiated...
			if( !is_object($object) )
			{
				//set up pagination of records
				if( $limit !== 0 )
				{
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$object = $model::take($limit);
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}()->take($limit);
					}
					
					
					if( $page !== 0 )
					{
						$object = $object->skip($limit*($page-1));
					}
					
					$object = $object->where($filterFields[$lastFilterFieldPos]['field_name'], 
											$filterFields[$lastFilterFieldPos]['operator'], 
											$filterFields[$lastFilterFieldPos]['value']);
				}
				else
				{	
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$object = $model::where($filterFields[$lastFilterFieldPos]['field_name'], 
												$filterFields[$lastFilterFieldPos]['operator'], 
												$filterFields[$lastFilterFieldPos]['value']);
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}()->where($filterFields[$lastFilterFieldPos]['field_name'], 
												$filterFields[$lastFilterFieldPos]['operator'], 
												$filterFields[$lastFilterFieldPos]['value']);
					}
					
					
				}
				
				unset($filterFields[$lastFilterFieldPos]);
				$filters['filter']['fields'] = $filterFields;
				
				return $this->buildFilteredCollection($model, $filters, $currentUser, $aclUserRelationName, $object);
			}
			//the collection object is already instatiatied, this is a recursive call
			else
			{	
				$filterMethod = 'where';
				
				if( isset($filters['filter']['type']) && strtoupper($filters['filter']['type'] == 'OR' ) )
				{	
					$filterMethod = 'orWhere';
				}
				
				$object->{$filterMethod}($filterFields[$lastFilterFieldPos]['field_name'], 
										$filterFields[$lastFilterFieldPos]['operator'], 
										$filterFields[$lastFilterFieldPos]['value']);
				
				unset($filterFields[$lastFilterFieldPos]);
				$filters['filter']['fields'] = $filterFields;

				return $this->buildFilteredCollection($model, $filters, $currentUser, $aclUserRelationName, $object);
			}
		}
		//if filter fields don't exist, check if pagination filters exist
		else
		{
			//if we don't have a collection object, filter fields never existed 
			//so no collection object was created yet... 
			//lets check if pagination needs to be set
			if( !is_object($object) )
			{
				if( $limit !== 0 )
				{
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$object = $model::take($limit);
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}()->take($limit);
					}
					
					if( $page !== 0 )
					{
						$object = $object->skip($limit*($page-1));
					}
				}
				else
				{
					//no pagination filters were passed, looks like we just want all records
					if( $currentUser->isAdmin() || $currentUser->isSalesRep() )
					{
						$instance = new $model;
						$object = $instance->newQuery();
					}
					else
					{
						$object = $currentUser->{$aclUserRelationName}();
					}
					
				}
			}
			
		}
		
		//set up return fields
		$returnFields = $this->returnFields($filters);
		
		return $object->get($returnFields);
	}
	*/
	
}