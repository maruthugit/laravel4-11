<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class ProductUpdate extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

    public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_sp_group';


	/**
	 * Validation rules for creating a new user.
	 * @var array
	 */
	public static $rules = array(
		'seller'		=> 'required',
	);

	public static $messages = array(
		'seller.required'		=> "Seller is required.",
	);

	public static function get_sellers() {
		return DB::table('jocom_seller')
					->where('active_status', '!=', '2')->orderBy('company_name', 'asc')->lists('company_name','id');
	}

	public static function add_job(array $data) {
		return DB::table('jocom_job_queue')->insert($data);
	}

	public static function get_export_job($id, $name) {
		return DB::table('jocom_job_queue as q')
					->select('q.id', 'q.request_at', 'q.status', 'q.out_file', 's.company_name')
					// ->leftjoin('jocom_user as u', 'u.id', '=', 'q.request_by')
					->leftjoin('jocom_seller as s', 's.id', '=', 'q.ref_id')
					->where('request_by', '=', $id)
					->where('job_name', '=', $name)
					->get();
	}

	public static function get_import_job($id, $name) {
		return DB::table('jocom_job_queue as q')
					->select('q.id', 'q.request_at', 'q.status', 'q.in_file', 'q.out_file', 's.company_name')
					->leftjoin('jocom_seller as s', 's.id', '=', 'q.ref_id')
					->where('request_by', '=', $id)
					->where('job_name', '=', $name)
					->get();
	}

	public static function update_job_queue($id, array $data) {
		return DB::table('jocom_job_queue')
					->where('id', $id)
					->update($data);
	}

	public static function import_get_header() {
        return DB::table('jocom_products as p')
                    ->select('p.id as product_id', 'p.description', 'p.description_my', 'p.gst',
                    		'price.id as label_id', 'price.label', 'price.price', 'price.price_promo', 'price.qty', 'price.stock', 'price.p_referral_fees', 'price.p_referral_fees_type', 'price.p_weight','price.seller_sku'
                    	)
                    ->leftjoin('jocom_seller as seller', 'seller.id', '=', 'p.sell_id')
                    ->leftjoin('jocom_product_price as price', 'p.id', '=', 'product_id')
                    ->take(1)
                    ->get();
    }

	public static function import_get_csv_content($id) {
		return DB::table('jocom_products as p')
					->select('p.id as product_id', 'p.description', 'p.description_my', 'p.gst',
						'price.id as label_id', 'price.label', 'price.price', 'price.price_promo', 'price.qty', 'price.stock', 'price.p_referral_fees', 'price.p_referral_fees_type','price.p_weight', 'price.seller_sku'
						)
					->leftjoin('jocom_seller as seller', 'seller.id', '=', 'p.sell_id')
					->leftjoin('jocom_product_price as price', 'price.product_id', '=', 'p.id')
					->where('p.sell_id', '=', $id)
					->where('p.status', '<>', '2')
					->where('price.status', '=', '1') 
					->orderBy('p.status', 'DESC')
					->orderBy('p.id', 'ASC')
					->get();
	}

	public static function get_job($id, $job_name) {
		// echo "<br>[User ID: $id] [Job name: $job_name]";
		return DB::table('jocom_job_queue')
					->select('*')
					// ->where('request_by', '=', $id)
					->where('job_name', '=', $job_name)
					->where('status', '=', 0)
					->get();
	}

	public static function check_exists($id) {
		$records = DB::table('jocom_products')
					->select('id')
					->where('sell_id', '=', $id)
					->where('status', '<>', '2')
					->get();

		return count($records) > 0 ? true : false;
	}
	
	public static function export_get_header() {
        return DB::table('jocom_products as p')
                    ->select('seller.company_name', 'p.sku', 'p.name', 'p.status','p.id as product_id', 'p.description', 'p.description_my', 'p.gst',
                    		'price.id as label_id', 'price.label', 'price.price', 'price.price_promo', 'price.qty', 'price.stock', 'price.p_referral_fees', 'price.p_referral_fees_type','price.p_weight', 'price.seller_sku'
                    	)
                    ->leftjoin('jocom_seller as seller', 'seller.id', '=', 'p.sell_id')
                    ->leftjoin('jocom_product_price as price', 'p.id', '=', 'product_id')
                    ->take(1)
                    ->get();
    }

	public static function export_get_csv_content($id) {
		return DB::table('jocom_products as p')
					->select('seller2.company_name', 'p.sku', 'p.name', 'p.status', 'p.id as product_id', 'p.description', 'p.description_my', 'p.gst',
							'price.id as label_id', 'price.label', 'price.price', 'price.price_promo', 'price.qty', 'price.stock', 'price.p_referral_fees', 'price.p_referral_fees_type', 'price.p_weight','price.seller_sku'
						)
					->leftjoin('jocom_product_seller as seller', 'seller.product_id', '=', 'p.id')
					->leftjoin('jocom_product_price as price', 'price.product_id', '=', 'p.id')
					->leftjoin('jocom_seller as seller2', 'seller2.id', '=', 'seller.seller_id')
					->where('seller.seller_id', '=', $id)
					->where('seller.activation', '<>', 0)
					->where('p.status', '<>', '2')
					->where('price.status', '=', '1') 
					->orderBy('p.status', 'DESC')
					->orderBy('p.id', 'ASC')
					->get();
	}

}

?>