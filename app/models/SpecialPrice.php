<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class SpecialPrice extends Eloquent implements UserInterface, RemindableInterface {

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
		'group_name'	=> 'required|min:3|unique:jocom_sp_group,name',
		// 'seller'		=> 'required',
		'min_purchase'	=> 'required|numeric',
	);

	public static $messages = array(
		'group_name.required' 	=> "Group name is required.",
		// 'seller.required'		=> "Seller is required.",
		'min_purchase.required'	=> "Minimum Purchase is required.",				
	);

	public static $disc_rules = array(
        'group_id'  => 'required',
    );

    public static $disc_message = array(
        'group_id'  => 'Please select a Group.',
    );


	public static function get_sellers() {
		return DB::table('jocom_seller')
					->where('active_status', '!=', '2')->orderBy('company_name', 'asc')->lists('company_name','id');
	}

	public static function get_groups() {
		return SpecialPrice::all();
	}

	public static function get_group_list() {
		return SpecialPrice::lists('name', 'id');
	}

	public static function update_group($id, array $inputs) {
		return SpecialPrice::where('id', '=', $id)->update($inputs);
		
	}

	public static function get_update_rules(array $inputs) {
		$validate_rule = array();

		foreach ($inputs as $key => $value) {
			switch ($key) {
				case 'group_name'	: $validate_rule[$key] = 'required|min:3';
						break;

				// case 'seller'		: $validate_rule[$key] = '';	//'required';
				// 		break;
			}
		}

		return $validate_rule;
	}


	public static function get_sp_customer($id) {
		$result = DB::table('jocom_sp_customer as c')
					->select('u.id', 'u.username', 'u.firstname', 'u.lastname', 'u.email', 'c.status')
					->leftjoin('jocom_user as u', 'u.id', '=', 'c.user_id')
					// ->leftjoin('jocom_sp_group as g', 'g.sp_cust_id', '=', 'c.id')
					->where('c.id', '=', $id)
					->first();

		return $result;
	}

	public static function get_sp_customer_group($id) {
		return DB::table('jocom_sp_customer_group as cg')
					->select('g.id', 'g.name', 's.company_name')
					->leftjoin('jocom_sp_group as g', 'g.id', '=', 'cg.sp_group_id')
					->leftjoin('jocom_seller as s', 's.id', '=', 'g.seller_id')
					->where('cg.sp_cust_id', '=', $id)
					->get();
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
					->where('status', '<>', '3')
					->get();
	}

	public static function get_import_job($id, $name) {
		return DB::table('jocom_job_queue as q')
					->select('q.id', 'q.request_at', 'q.status', 'q.in_file', 'q.out_file', 'g.name')
					->leftjoin('jocom_sp_group as g', 'g.id', '=', 'q.ref_id')
					->where('request_by', '=', $id)
					->where('job_name', '=', $name)
					->get();
	}	

	public static function get_group_customer($id) {
		return DB::table('jocom_sp_customer_group as cg')
					->select('u.id', 'u.username', 'u.firstname', 'u.lastname', 'u.email', 'c.status')
					->leftjoin('jocom_sp_customer as c', 'c.id', '=', 'cg.sp_cust_id')
					->leftjoin('jocom_user as u', 'u.id', '=', 'c.user_id')
					->where('cg.sp_group_id', '=', $id)
					->get();
	}

	public static function get_cust_sp_id($id) {
		return DB::table('jocom_sp_customer')
					->select('id')
					->where('user_id', '=', $id)
					->first();
	}
	
	public static function get_settings() {
		return DB::table('jocom_sp_settings')
					->select('*')
					->first();
	}

	public static function update_setting(array $data) {
		return DB::table('jocom_sp_settings')
					->where('id', '=', 1)
					->update($data);
	}

	public static function get_default_qty() {
		return DB::table('jocom_sp_settings')
					->select('default_qty')
					->first();
	}
	
	public static function get_cust_group($id) {
		return DB::table('jocom_sp_customer_group as cg')
					->select('s.id', 's.company_name', 'cg.sp_group_id', 'g.name', 'cg.sp_cust_id')
					->leftjoin('jocom_sp_group as g', 'g.id', '=', 'cg.sp_group_id')
					->leftjoin('jocom_seller as s', 's.id', '=', 'g.seller_id')
					->where('cg.sp_cust_id', '=', $id)
					->get();
	}

	public static function insert_special_price(array $inputs) {
		$result = DB::table('jocom_sp_product_price')
					->insert($inputs);

		return $result;
	}

	public static function update_special_price($group_id, $label_id, array $inputs) {
		return DB::table('jocom_sp_product_price')
					->where('sp_group_id', '=', $group_id)
					->where('label_id', '=', $label_id)
					->update($inputs);
	}

	public static function get_sp_price_list($id) {
		return DB::table('jocom_sp_product_price as price')
					->leftjoin('jocom_products as product', 'product.id', '=', 'price.product_id')
					->where('price.sp_group_id', '=', $id)
					->get();

	}

	public static function get_group_name($id) {
		return DB::table('jocom_sp_group')
					->select('name')
					->where('id', '=', $id)
					->first();
	}

	public static function update_discount($id, $amount, $type) {
        return DB::table('jocom_sp_product_price')
                    ->where('id', '=', $id)
                    ->update(array(
                    		'disc_amount' 	=> $amount,
                    		'disc_type' 	=> $type
                    	));
    }

    public static function get_job_by_id($id) {
		return DB::table('jocom_job_queue')
					->select('*')
					->where('id', '=', $id)
					->first();
	}

	public static function update_job_queue($id, array $data) {
		return DB::table('jocom_job_queue')
					->where('id', $id)
					->update($data);
	}

	public static function export_get_header() {
		return DB::select(DB::raw('
					SELECT p.sku, p.name, price.label, price.label_cn, price.label_my, price.seller_sku, price.id, price.price, price.price_promo, price.qty,
						price.p_referral_fees, price.p_referral_fees_type,
							(CASE price.price WHEN price > 1 THEN "0" ELSE "0" END) as disc_amount,
							(CASE price.price WHEN price > 1 THEN "0" ELSE "0" END) as disc_type,
						price.default, price.product_id, price.status
					FROM jocom_products as p
					LEFT JOIN jocom_product_price as price ON price.product_id = p.id
					LIMIT 1'));
	}

	public static function export_get_csv_content($id) {
		return DB::table('jocom_products as p')
				->select(DB::raw('
					p.sku, p.name, price.label, price.label_cn, price.label_my, price.seller_sku, price.id, price.price, price.price_promo, price.qty,
						price.p_referral_fees, price.p_referral_fees_type,
							(CASE price.price WHEN price > 1 THEN "0" ELSE "0" END) as disc_amount,
							(CASE price.price WHEN price > 1 THEN "" ELSE "" END) as disc_type,
						price.default, price.product_id, price.status'))
				->leftJoin('jocom_product_price as price', 'price.product_id', '=', 'p.id')
				->where('p.status', '<>', '2')
				->where('price.status', '=', '1')
				->where('p.sell_id', '=', $id)
				->get();
	}

	public static function import_get_header() {
        return DB::table('jocom_sp_product_price as price')
                    ->select('p.sku', 'p.name', 'price.label', 'price.label_id', 'price.price', 'price.price_promo', 'price.qty', 'price.p_referral_fees', 'price.p_referral_fees_type', 
                    		'price.price as disc_amount', 'price.price as disc_type', 'price.default', 'price.product_id', 'price.status'
                    	)
                    ->leftjoin('jocom_products as p', 'p.id', '=', 'product_id')
                    ->take(1)
                    ->get();
    }

	public static function import_get_csv_content($id) {
		return DB::table('jocom_sp_product_price as price')
					->select('p.sku', 'p.name', 'price.label', 'price.label_id', 'price.price', 'price.price_promo', 'price.qty', 'price.p_referral_fees', 'price.p_referral_fees_type', 
                    		'price.price as disc_amount', 'price.price as disc_type', 'price.default', 'price.product_id', 'price.status'
                    	)
					->leftjoin('jocom_products as p', 'price.product_id', '=', 'p.id')
					->where('price.sp_group_id', '=', $id) 
					->get();
	}

	public static function check_exists($id) {
		$records = DB::table('jocom_sp_product_price')
					->select('id')
					->where('sp_group_id', '=', $id)
					->get();

		return count($records) > 0 ? true : false;
	}

	public static function check_record($label_id, $id) {
		$records = DB::table('jocom_sp_product_price')
					->select('*')
					->where('label_id', '=', $label_id)
					->where('sp_group_id', '=', $id)
					->get();
		// var_dump($records);
		return $records;
	}	

}

?>