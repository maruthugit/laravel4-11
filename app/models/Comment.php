<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Comment extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;

    protected $fillable = ['comment_date', 'user', 'user_id', 'product', 'product_id', 'comment', 'rating'];

    public static $rules = [
        'comment_date' => 'required',
        'user' => 'required',
        'product' => 'required',
        'comment' => 'required',
        'rating' => 'required',
    ];

    public static $message = [
        'comment_date.required' => 'The product date is required.',
        'product_desc.required' => 'The product description is required.',
    ];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_comments';

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
    
    public function scopeCommentsRating($id) {

        $comments = DB::table('jocom_comments')->where('product_id','=', $id)->lists('rating');
        $sum = array_sum($comments);
        $count = count($comments);
        if($count > 0){
            $average = $sum / $count;
            $points = ceil($average);

            // if ($average - floor($average) > 0.99) {
            //     $points = ceil($average) + 1;
            // } else {
            //     $points = floor($average);
            // }
        }else{
            $points = '';
        }

       return $points;
       
    }
		 
}