<?php
namespace Spoolphiz\Events\Interfaces;

interface CategoryRepository {
	
	/**
	 * get a single category by id
	 *
	 * @param $categoryId  The id of the category
	 *
	 * @return Spoolphiz\Events\Models\Eloquent\Category;
	 */
	public function find($id);
	

	/**
	 * get all categories
	 *
	 * @param $categoryId  The id of the category
	 *
	 * @return Collection
	 */
	public function all();
	

	/**
	 * creates new Spoolphiz/Events/Models/Eloquent/Category
	 *
	 *
	 * @return Spoolphiz/Events/Models/Eloquent/Category
	 */
	public function newCategory();
	
}