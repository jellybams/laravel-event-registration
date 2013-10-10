<?php
namespace Spoolphiz\Events\Repositories;
use \App;
use Spoolphiz\Events\Interfaces\EventRepository;

abstract class BaseRepository
{
		
	/**
	 * allowed operators for filtering data
	 *
	 * @return array
	 */
	public function allowedConditions()
	{
		return array( 'simple' => array('=', '!=', '<', '<=', '>', '>=', 'in', 'not in'), 
						'translate' => array('starts with', 'ends with', 'contains', 'not contains')
					);
	}
	
	
	/**
	 * extracts fields to return from a given model
	 *
	 * @param array		filters
	 *
	 * @return array
	 */
	public function returnFields($filters, $baseTable)
	{
		return (isset($filters['fields'])) ? $filters['fields'] : array("$baseTable.*") ;
	}
	
	
	/**
	 * translates non-basic filtering conditions into strings that can be passed
	 * the the query builder using where() or orWhere()
	 *
	 * @param array 	filter field data (contains keys of name, operator, value)
	 *
	 * @return array
	 */
	public function translateCondition($filterField)
	{
		switch( strtolower($filterField['operator']) )
		{
			case 'starts with':
				$filterField['operator'] = 'LIKE';
				$filterField['value'] = $filterField['value'].'%';
				break;
			case 'ends with':
				$filterField['operator'] = 'LIKE';
				$filterField['value'] = $filterField['value'].'%';
				break;
			case 'contains':
				$filterField['operator'] = 'LIKE';
				$filterField['value'] = '%'.$filterField['value'].'%';
				break;
			case 'not contains':
				$filterField['operator'] = 'NOT LIKE';
				$filterField['value'] = '%'.$filterField['value'].'%';
				break;
			/*case 'in':
				$filterField['operator'] = 'IN';
				$filterField['value'] = '('.implode(',', $filterField['value']).')';
				break;*/
		}
		
		return $filterField;
	}
	
	
	/**
	 * applies all filters to a collection object and queries the data
	 *
	 * @param filters 		array - conditions to apply to query
	 * @param collection	Illuminate\Database\Eloquent\Builder instance
	 * @param baseTable		the main table this query is acting on, for use in return fields if none specified
	 *
	 * @return Illuminate\Database\Eloquent\Collection instance
	 */	
	public function buildFilteredCollection($filters, $collection, $baseTable)
	{	
		$filterFields = (isset($filters['filter']['fields'])) ? $filters['filter']['fields'] : array();
		$allowedConditions = $this->allowedConditions();
		$limit = (isset($filters['limit']) && is_numeric($filters['limit'])) ? $filters['limit'] : 0;
		$page = (isset($filters['page']) && is_numeric($filters['page'])) ? $filters['page'] : 0;
		$orderBy = (isset($filters['sort']) && is_array($filters['sort']) ) ? $filters['sort'] : array();
		
		$lastFilterFieldPos =  (count($filterFields)>0) ? count($filterFields)-1 : 0;
		
		// if filter fields exist...
		if( isset($filterFields[$lastFilterFieldPos]) )
		{
			//if the operator for the where clause is not a simple operator, transate the operator and value fields
			if( in_array($filterFields[$lastFilterFieldPos]['operator'], $allowedConditions['translate'] ) )
			{
				$filterFields[$lastFilterFieldPos] = $this->translateCondition($filterFields[$lastFilterFieldPos]);
			}
			
			$operator = strtoupper($filterFields[$lastFilterFieldPos]['operator']);
			
			//decide what type of WHERE to use with this field
			if( $operator == 'IN' || $operator == 'NOT IN' )
			{
				if( !is_array($filterFields[$lastFilterFieldPos]['value']) )
				{
					App::abort(400, 'The '.$operator.' operator should be accompanied by an array of ids as the value.');
				}
				
				$filterMethod = 'whereIn';
				
				if( $operator == 'NOT IN' )
				{	
					$filterMethod = 'whereNotIn';
				}
				
				$collection = $collection->{$filterMethod}($filterFields[$lastFilterFieldPos]['name'],
															$filterFields[$lastFilterFieldPos]['value']);
			}
			else
			{
				$filterMethod = 'where';

				if( isset($filters['filter']['type']) && strtoupper($filters['filter']['type'] == 'OR' ) )
				{	
					$filterMethod = 'orWhere';
				}

				$collection = $collection->{$filterMethod}($filterFields[$lastFilterFieldPos]['name'], 
															$filterFields[$lastFilterFieldPos]['operator'], 
															$filterFields[$lastFilterFieldPos]['value']);
			}
			
			unset($filterFields[$lastFilterFieldPos]);
			$filters['filter']['fields'] = $filterFields;

			return $this->buildFilteredCollection($filters, $collection, $baseTable);
		}
			
		//do we want the total number of records with the criteria so far?
		if( isset($filters['total']) && $filters['total'] == 1 )
		{
			return array( "total" => $collection->count() );
		}
		else
		{
			//at this point all filter fields were recursively applied to $collection
			//or there weren't any filter field to begin with... either way lets
			//set up pagination of records
			if( $limit !== 0 )
			{
				$collection = $collection->take($limit);
				$collection = $collection->skip($limit*$page);
			}
		}
			
		//check if there needs to be an ordering by any field
		foreach( $orderBy as $item )
		{			
			$collection = $collection->orderBy($item['field'], $item['dir']);
		}
		
		//set up return fields
		$returnFields = $this->returnFields($filters, $baseTable);
		
		try
		{
			$result = $collection->get($returnFields);
		}
		catch( \Exception $e )
		{
			App::abort(400, 'Error applying filters, please check your syntax.');
		}
		
		return $result;
	}
	
	
	
	
	/**
	 * Reloads requested relationships on the given Eloquent $model
	 *
	 * This function is needed because:
	 * For some off reason when saving related models, the corresponding keys
	 * on the model instance do not automatically update. 
	 *
	 * @param model 		Eloquent model
	 * @param relations		array containing relationship names
	 *
	 * @return Eloquent model
	 */
	public function reloadRelationships( $model, $relations )
	{	
		foreach($relations as $relation)
		{
			unset($model->$relation);
			$model->$relation = $model->{$relation}()->getResults();
		}
		
		return $model;
	}
	
	
	
}
