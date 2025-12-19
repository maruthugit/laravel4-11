<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Category extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $fillable = ['category_name', 'category_name_cn', 'category_name_my', 'category_descriptions', 'category_parent', 'weight']; // column name

	public static $rules = [
		'category_name' => 'required|between:1,50',
	];

	public $errors;

	/**
	 * Caution: Not to be confused with table `jocom_categories` or ProductsCategory model
	 */
	protected $table = 'jocom_products_category';

	// protected $primaryKey = 'category_id';

	/**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'insert_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modify_date';


	public function isValid() {
		$validation = Validator::make($this->attributes, static::$rules);

		if($validation->passes()) return true;

		$this->errors = $validation->messages();

		return false;
	}

	public function scopeGetCategory($query, $id) {
		return Category::find($id);
	}

	public static function getByParent($parentId, $permission = 0, array $categoryList = [])
	{
		$categories = self::select('id')
			->orderBy('weight', 'desc')
			->where('category_parent', '=', $parentId)
			->where('permission', '=', $permission)
			->where('status', '=', 1);

		if ( ! empty($categoryList))
		{
			$categories = $categories->whereIn('id', $categoryList);
		}

		return $categories->orderBy('category_name', 'asc')
			->get();
	}

	public static function getByParentIgnoreStatus($parentId, $permission = 0, array $categoryList = [])
	{
		$categories = self::select('id')
			->orderBy('weight', 'desc')
			->where('category_parent', '=', $parentId)
			->where('permission', '=', $permission);

		if ( ! empty($categoryList))
		{
			$categories = $categories->whereIn('id', $categoryList);
		}

		return $categories->orderBy('category_name', 'asc')
			->get();
	}

	public function sortByParent()
	{
		return $this->orderBy('category_parent', 'asc')
			->orderBy('category_name', 'asc')
			->where('status', '!=', 2)
			->get();
	}

	public function sortByWeight()
	{
		return $this->orderBy('weight', 'desc')
			->orderBy('category_parent', 'asc')
			->orderBy('category_name', 'asc')
			->where('status', '!=', 2)
			->get();
	}

}
