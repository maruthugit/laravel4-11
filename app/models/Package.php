<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Package extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $fillable = ['prod_name', 'product_desc', 'product_category', 'image', 'product_video', 'delivery_time', 'lid', 'qty', 'related_product'];

	public static $rules = [
		'prod_name' => 'required|min:5',
		// 'product_desc' => 'required|min:10',
		'product_category' => 'required',
		// 'image' => 'mimes:jpeg,jpg,png',
		'product_video' => 'url',
		'delivery_time' => 'required',
		'lid'			=> 'required',
		'related_product'	=> 'regex:/^[A-Za-z0-9 ,]+$/',
	];

	public static $message = [
		'lid.required'	=> 'The product is required.'
    	// 'product_desc.required' => 'The product description is required.',
    ];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_product_package';

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
		$validation = Validator::make($this->attributes, static::$rules, static::$message);

		if($validation->passes()) return true;

		$this->errors = $validation->messages();

		return false;
	}

	// File Name must in png
	public function generateQR($text='', $dir='', $file_name='') {
		include app_path('library/phpqrcode/qrlib.php');

		if (!is_dir($dir))
			mkdir($dir);

		$filename = $dir . '/' . $file_name;
		$errorCorrectionLevel = 'H'; // 'L','M','Q','H'
		$matrixPointSize = 8; // 1 - 10

		QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
	}
}
