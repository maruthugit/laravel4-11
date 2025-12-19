<?php

class JocomMyController extends BaseController {
	/**
	 * Comment: list of the [hero-banner, special sales, plateform partners promo, category image]
	 *
	 * @api FALSE
	 * @author YEE HAO
	 * @since 25 MAY 2021
	 * @method private BannerFlashTypeCheck "Checking Input are match flashsales format"
	 * @method private BannerContentCheck "Checking Input content does it match"
	 * @method private BannerPositionSort "Check and Sorting banner position, if sorting happend update multiple row that involving in sorting"
	 * @method private BannerDurationCheck "Check the banner duration is set correct?"
	 * @method private BannerImageValid "Check the banner image is valid and move the image on storage path"
	 * @method private BannerType "Check and return Banner type that use for reference in Banner and event module"
	 * @method private BannerCheckValid "Callback function that use (BannerFlashTypeCheck, BannerContentCheck, BannerPositionSort, BannerDurationCheck, BannerImageValid)"
	 * @method private BannerFetchDBformat "Generate Banner database input format for (anyCreate, anyUpdate) insert or update DB"
	 * @method private ClearCache
	 * @method private BannerDataReturnFormat
	 * @method private EventExampleImageValid "Check upload image is valid? for the Event Banner internal use refence"
	 * @method private EventDurationValid "Check Event Duration is Valid"
	 * @method private EventCheckValid "Callback function that use (EventExampleImageValid, EventDurationValid)"
	 * @method private InsertEventFormat
	 * @method private TemplateImageMoveNSet "Move Image to specified Directory and return filename"
	 * @method private TemplateImageValid "Check image is valid before store and move"
	 * @method private TemplateArrayPattern "Check pattern is in array do parse"
	 * @method private TemplateAnonymousFunction "Check pattern is under special define function"
	 * @method private TemplateFetchDBFormat "Generate Template database input format for (anyTemplatenew, anyTemplatedit) insert or update DB"
	 * @method private TemplateContentParser "Callback function that use (TemplateArrayPattern, TemplateAnonymousFunction)"
	 * @version 3.01
	 * @used-by jocom.my
	 * @used-by my.ofis.jocom.my
	 * @used-by crossborder.jocom.my
	 * @used-by jocom.my/wavpay webview
	 *
	 * Last Update: 25 MAY 2022
	 */
	private $banner_config = [
		'content_type'		=> [
			'empty' => 'No content',
			'url' => 'Url Link', 
			'qrcode' => 'QR code', 
			'html' => 'TnC Text Content',
			'search_name' => 'Search Name', // multi lang
			'category_id' => 'Category ID',
			'template' => 'Template', // multi lang
			'mix' => 'Mix Type',
			'flashsales_id' => 'Flashsales ID',
		],
		'logic_operation'	=> [
			0 => 'No Duration', 
			1 => 'Duration', 
			2 => 'Scheduler',
			3 => 'Flashsales Period',
		],
		'status'			=> [
			0 => 'Inactive', 
			1 => 'Active'
		],
		'scheduler_type'	=> [
			'DAY' => 'Run once per Day',
			'WEEK' => 'Run once per Week',
			'MONTH' => 'Run once per Month',
			'YEAR' => 'Run once per Year',
		],
		'scheduler_week'	=> [
			'SUN' => 'Sunday',
			'MON' => 'Monday',
			'TUE' => 'Tuesday',
			'WED' => 'Wednesday',
			'THU' => 'Thursday',
			'FRI' => 'Friday',
			'SAT' => 'Saturday',
		],
		'scheduler_month'	=> [
			'JAN' => 'January',
			'FEB' => 'February',
			'MAR' => 'March',
			'APR' => 'April',
			'MAY' => 'May',
			'JUN' => 'June',
			'JUL' => 'July',
			'AUG' => 'August',
			'SEP' => 'September',
			'OCT' => 'October',
			'NOV' => 'November',
			'DEC' => 'December',
		],
		'platform'			=> [
			'ALL' => 'Web and Mobile App',
			'WEB' => 'Just Browser Web',
			'MOBILE' => 'Both IOS & Android App',
			'IOS' => 'Just iOS Mobile App',
			'ANDROID' => 'Just Android Mobile App',
		]
	];
	private $template_imgno = [];
	private static $headerhtml = false;
	private $html;
	private $result;
	private $multilang = false;

	public function anyIndex(){
		$banner_type = $this->BannerType(0);
		$regionlist = DB::table('jocom_region')->lists('region', 'id');

		if(Request::isMethod('post')){
			$jocom_banner = DB::table('jocommy_banner_manage');

			if (Input::get('banner_type') && strtolower(Input::get('banner_type')) !== 'any') $jocom_banner->where('type', Input::get('banner_type'));
			if (strtolower(Input::get('status')) !== 'any' && (Input::get('status') || Input::get('status') === "0") && (int)Input::get('status') >= 0) $jocom_banner->where('status', Input::get('status'));
			if (Input::get('lang') && strtolower(Input::get('lang')) !== 'any') $jocom_banner->whereRaw("LOCATE('" . Input::get('lang') . "', lang) > 0");
			if (Input::get('region')) $jocom_banner->where('region', (int)Input::get('region'));
			if (Input::get('platform')) $jocom_banner->where('platform', Input::get('platform'));
			$pc = ['ALL' => 'primary', 'WEB' => 'info', 'MOBILE' => 'success', 'IOS' => 'danger', 'ANDRIOD' => 'warning'];

			$jocom_banner->select(['id', 'type', 'content_type', 'content_data', 'image', 'image_m', 'title', 'status', 'position', 'logic_operation', 'begin_at', 'duration', 'insert_at', 'insert_by', 'modify_at', 'modify_by', 'allow_multilang', 'lang', 'region', 'platform']);

			$base_url = url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH');
			
			return Datatables::of($jocom_banner)
				->edit_column('type', function($row) use ($banner_type){
					return $banner_type[$row->type];
				})->edit_column('image', function($row) use ($base_url){
					$first_img = $row->image;
					$firstmimg = $row->image_m;
					if($row->allow_multilang){
						$first_img = json_decode($first_img, true);
						$key = key($first_img);
						$first_img = $first_img[$key];
						$firstmimg = json_decode($firstmimg, true)[$key];
					}
					return '<img src="' . $base_url . $first_img . '" style="width: auto; max-width: 100%; height: auto;">' . 
						($row->image_m ? '<img src="' . $base_url . $firstmimg . '" style="width: auto; max-width: 100%; height: auto;">' : '');
				})->edit_column('title', function($row){
					if($row->allow_multilang){
						$title = json_decode($row->title, true);
						array_walk($title, function(&$v, $k){ $v = $k . ': ' . $v; });
						return implode(',<br>', $title);
					}
					return $row->title;
				})->edit_column('status', function($row) use ($pc){
					return '<span class="label ' . ($row->status == 0 ? 'label-default">Inactive' : ($row->status == 1 ? 'label-warning">Active' : 'label-danger">Delete')) . '</span><br><span class="label label-' . $pc[$row->platform] . '">Platform: ' . $row->platform . '</span>';
				})->edit_column('content_data', function($row){
					if($row->allow_multilang && in_array($row->content_type, ['template', 'search_name'])){
						$cd = json_decode($row->content_data, true);
						array_walk($cd, function(&$v, $k, $type){ $v = $k . ': ' . ($type === 'template' ? $v['name'] : $v); }, $row->content_type);
						$cd = '<br>' . implode(',<br>', $cd);
					}else{
						$cd = ($row->content_type === 'template' ? json_decode($row->content_data, true)['name'] : false);
					}
					return $row->content_type . ': ' . ($cd ? $cd : str_split($row->content_data, 200)[0] . (strlen($row->content_data) > 200 ? '...' : ''));
				})->edit_column('duration', function($row){
					if($row->logic_operation == 1)
						return '<p>Duration:</p><p>start at: ' . ($row->begin_at ? $row->begin_at : 'Date of this banner created') . '</p><p>end at: ' . date('Y-m-d h:i:s', (strtotime($row->begin_at) + $row->duration)) . '</p>';
					else if($row->logic_operation == 2)
						return '<p>Scheduler:</p><p>start at: ' . $row->begin_at . '</p><p>Duration: ' . $row->duration . '</p>';
					else if($row->logic_operation == 3)
						return '<p>Flash Sales Period</p>';
					else
						return '<p>Not expire</p><p>start at: ' . ($row->begin_at ? $row->begin_at : 'Date of this banner created') . '</p>';
				})->edit_column('region', function($row) use ($regionlist){
					return ($row->region ? $regionlist[$row->region] : 'Worldwide All Region');
				})->edit_column('lang', function($row) {
					return ($row->lang ? strtoupper($row->lang) : 'EN');
				})->add_column('last_action', function($row){
					return '<p>' . $row->insert_at . '</p><p>' . $row->insert_by . '</p><p>' . $row->modify_at . '</p><p>' . $row->modify_by . '</p>';
				})
				->remove_column('content_type')->remove_column('logic_operation')->remove_column('begin_at')->remove_column('allow_multilang')->remove_column('platform')
				->remove_column('insert_at')->remove_column('insert_by')->remove_column('modify_at')->remove_column('modify_by')->remove_column('image_m')
				->add_column('Action', function($row){
					$edit = '';
					if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
						$edit .= '<a class="btn btn-primary btn-sm" data-toggle="tooltip" href="'.url('jocommy/update/'.$row->id).'"><i class="fa fa-pencil"></i></a> ';

					if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 9, 'AND'))
						$edit .= '<a id="deleteItem" class="btn btn-danger btn-sm" data-toggle="tooltip" data-value="{{ $id }}" href="'.url('jocommy/delete/'.$row->id).'"><i class="fa fa-remove"></i></a>';

					return $edit;
				})->make();
		}
		return View::make('jocommy.index', [
			'banner_type' => $banner_type, 
			'lang' => DB::table('language')->lists('name', 'code'),
			'region' => $regionlist
		]);
	}

	private function BannerFlashTypeCheck(){
		if(Input::get('banner_type') === 'banner_flashsales' && (Input::get('content_type') !== 'flashsales_id' || Input::get('logic_operation') !== 3))
			return ['match' => false, 'msg' => 'When select up as flash sales banner type, content type must use the flashslaes id, logic operation field must select the Flashsales Period'];
		else if(Input::get('banner_type') !== 'banner_flashsales' && (Input::get('content_type') === 'flashsales_id' || Input::get('logic_operation') === 3))
			return ['match' => false, 'msg' => 'Only can select content by flashslaes id, when banner type is Flashsales'];

		return ['match' => true, 'msg' => ''];
	}

	private function BannerContentTemplateCheck($id, $encodetoJSON = true){
		$result = DB::table('jocommy_template')->select('id', 'name')->where('id', $id)->first();
		if($result){
			$result->name = htmlentities($result->name, ENT_QUOTES);
			return ($encodetoJSON ? json_encode($result) : $result);
		}
		return false;
	}

	private function BannerMultilangCheck(){
		$this->multilang = Input::get('multilang', false);
		if($this->multilang){
			$this->multilang = explode(',', Input::get('lang', 'en'));
			$lang_count = count($this->multilang);
			$range = ['ltitle'];
			if(Input::get('content_type') === 'search_name') $range[] = 'lcd_search_name';
			if(Input::get('content_type') === 'template') $range[] = 'lcd_template';
			foreach ($range as $val) if(count(Input::get($val)) !== $lang_count) return false;
		}
		return true;
	}

	private function BannerContentCheck(){
		$msg = 'Content must not empty'; $match = false;
		$content_data = '';

		if(Input::get('content_type') && Input::get('content_type') !== 'empty'){
			$key = array_search(Input::get('content_type'), ['catid' => 'category_id', 'searchname' => 'search_name', 'flashid' => 'flashsales_id']);
			$content_data = $match = (
				$this->multilang && in_array(Input::get('content_type'), ['search_name', 'template']) 
				? array_combine($this->multilang, Input::get('lcd_' . Input::get('content_type'))) 
				: Input::get('contentdata_' . ($key !== false ? $key : Input::get('content_type')))
			);

			if($match){
				if(Input::get('content_type') === 'url'){
					$match = filter_var($content_data, FILTER_VALIDATE_URL);
					$msg = 'Content must be valid url.';
				}
				if(Input::get('content_type') === 'qrcode'){
					foreach (explode(',', $content_data) as $key => $value) {
						$value = trim($value);
						if(!(substr($value, 0, 2) === "TM" && filter_var(substr($value, 2), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== FALSE)){
							$match = false;
							$msg = 'Content must be QR Code format that show on example.';
							break;
						}
					}
				}
				if(Input::get('content_type') === 'html') $content_data = htmlentities(strip_tags($content_data)); // remove all html tag + convert it into html entities for safty purpose
				if(Input::get('content_type') === 'search_name'){
					if($this->multilang){
						foreach (Input::get('lcd_search_name') as $value) {
							if(!trim($value)){
								$match = false;
								break;
							}
						}
						$content_data = json_encode($content_data);
					}else if(!trim($content_data)){ $match = false; }
				}
				if(Input::get('content_type') === 'category_id'){
					foreach (explode(',', $content_data) as $key => $value) {
						if(!(filter_var(trim($value), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== FALSE)){
							$match = false;
							$msg = 'Content must be category id format that show on example.';
							break;
						}
					}
				}
				if(Input::get('content_type') === 'template'){
					if($this->multilang){
						$content_data = []; // reset the data cuz it will use for stire json string format
						foreach (Input::get('lcd_template') as $idx => $val) {
							$cd = $this->BannerContentTemplateCheck((int)$val, false);
							if(!$cd){
								$match = false;
								$msg = 'Please select the active banner template';
								break;
							}
							$content_data[] = $cd;
						}
						$content_data = json_encode(array_combine($this->multilang, $content_data));
					}else{
						$content_data = $this->BannerContentTemplateCheck((int)$content_data);
						if(!$content_data){
							$match = false;
							$msg = 'Please select the active banner template';
						}
					}
				}
				if(Input::get('content_type') === 'mix'){
					foreach (explode(',', $content_data) as $key => $value) {
						$value = trim($value);

						$local_match = false;
						if(substr($value, 0, 2) === "TM" && filter_var(substr($value, 2), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== FALSE) $local_match = true;

						if(substr($value, 0, 4) === "CAT_" && filter_var(substr($value, 4), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== FALSE) $local_match = true;

						if(substr($value, 0, 6) === "FLASH_" && filter_var(substr($value, 6), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== FALSE) $local_match = true;

						if(!$local_match){
							$match = false;
							$msg = 'Only QR code (TMXXX), category id (CAT_XXX) and flash sales id (FLASH_XXX) format can be accepted.';
							break;
						}
					}
				}
				if(Input::get('content_type') === 'flashsales_id'){
					foreach (explode(',', $content_data) as $key => $value) {
						if(!(filter_var(trim($value), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== FALSE)){
							$match = false;
							$msg = 'Content must be Flash Sales id format that show on example.';
							break;
						}
					}
				}
			}
		}else if(Input::get('content_type') === 'empty'){
			$match = true;
		}

		return ['match' => $match, 'msg' => $msg, 'data' => $content_data];
	}

	private static function PositionDiffResult($position, $org_pos){
		// reset negative -1 index due to above logic
		array_splice($position, 0, 0, '-');
		unset($position[0]);

		$diff_result = array_diff_assoc($position, $org_pos);
		$new_key = array_search('new', $diff_result);
		unset($diff_result[$new_key]);
		return array_filter($diff_result);
	}

	public function BannerPositionSort($id = null, $query = false, $table_name = "jocommy_banner_manage", $pos_key = "position"){
		$org_pos = $query;
		$org_pos = ($id ? $org_pos->where('id', '!=', $id)->lists('id', $pos_key) : $org_pos->lists('id', $pos_key));
		ksort($org_pos);
		$position = $org_pos;
		if(!isset($position[Input::get($pos_key)])){
			$position[Input::get($pos_key)] = 'new';
			ksort($position);
		}else{
			array_splice($position, Input::get($pos_key), 0, 'new');

			$key_0 = (int)Input::get($pos_key) - 1;
			$val_0 = $position[$key_0];
			$key_1 = (int)Input::get($pos_key);
			$val_1 = $position[$key_1];

			$position[$key_0] = $val_1;
			$position[$key_1] = $val_0;
			ksort($position);
		}
		$diff_result = self::PositionDiffResult($position, $org_pos);

		if($diff_result && count($diff_result > 0)){
			$sql = str_replace('=', ' THEN ', ' WHEN ' . http_build_query(array_flip($diff_result), '', ' WHEN '));
			$ids = implode(', ', array_values($diff_result));
			DB::statement("UPDATE $table_name SET $pos_key = (CASE id $sql END) WHERE id IN($ids)");
		}
	}

	private function BannerDurationCheck(){
		// 0: no duration, 1: duration, 2: scheduler, 3: flashsales period
		$begin_at = ''; $duration = ''; // When flashsales period do nothing
		if((int)Input::get('logic_operation') === 0) $begin_at = Input::get('beign_date');

		if((int)Input::get('logic_operation') === 1){
			if(!Input::get('beign_date') || !Input::get('end_date')) return ['error' => 1, 'msg' => 'Both Duration must not be empty'];

			$begin_at = Input::get('beign_date');
			$duration = strtotime(Input::get('end_date')) - strtotime(Input::get('beign_date'));

			if(!$duration || $duration <= 0) return ['error' => 1, 'msg' => 'Banner End At duration must be greater than Beign at date'];
		}

		if((int)Input::get('logic_operation') === 2){
			if(!Input::get('beign_schedule') || !Input::get('end_time')) return ['error' => 1, 'msg' => 'Both Duration and Schedule must have value'];
			if((int)Input::get('end_time') != Input::get('end_time') && Input::get('end_time')) return ['error' => 1, 'msg' => 'Duration must be UNIX TIMESTAMP on integer format'];

			$begin_at = Input::get('beign_schedule');
			$duration = Input::get('end_time');
		}

		return ['begin_at' => $begin_at, 'duration' => $duration];
	}

	private function BannerMultiLangImage($filekey, $refkey, $type, $langkey = false){
		$imgfile = ($langkey !== FALSE ? Input::file($filekey)[$langkey] : Input::file($filekey));
		$imgref = ($langkey !== FALSE ? Input::get($refkey)[$langkey] : Input::get($refkey));
		if(!$imgfile && !$imgref) return ['error' => 1, 'msg' => 'Banner Image ' . ($type ? 'Mobile ' : '') . 'must be upload inorder to proceed'];
		if($imgfile && !$imgref){
			$image = $imgfile;
			$filename = ltrim(Input::get('banner_type'), 'banner_') . $type . '-' . time() . ($langkey !== FALSE ? '-' . $this->multilang[$langkey] : '') . '.' . $image->getClientOriginalExtension();
			$image->move(Config::get('constants.JOCOMMY_BANNER_PATH'), $filename);
		}
		if($imgfile || $imgref) return ['name' => 'image_path' . $type, 'file' => ($imgfile ? $filename : $imgref)];
	}

	private function BannerImageValid(){
		foreach (['', '_m'] as $type) {
			if($this->multilang){
				$tempname = 'image_path' . $type;
				${$tempname} = [];
				for ($i = 0; $i < count($this->multilang); $i++) { 
					$img_detail = $this->BannerMultiLangImage('limg' . $type, 'limgref' . $type, $type, $i);
					if($img_detail && isset($img_detail['error'])) return $img_detail;
					if($img_detail) ${$img_detail['name']}[] = $img_detail['file'];
				}
				${$tempname} = json_encode(array_combine($this->multilang, ${$tempname}));
			}else{
				$img_detail = $this->BannerMultiLangImage('image' . $type, 'image_ref' . $type, $type);
				if($img_detail && isset($img_detail['error'])) return $img_detail;
				if($img_detail) ${$img_detail['name']} = $img_detail['file'];
			}
		}
		return (isset($image_path) && isset($image_path_m) ? ['image_path' => $image_path, 'image_path_m' => $image_path_m] : ['no_update' => 'no_update']);
	}

	private function BannerType($event_type = false){
		$banner_val = ['Hero Banner', 'Star Brands Banner', 'Special Category', 'Voucher Promo', 'E-Wallet & BNPL PROMO', 'New Arrivals'];
		if($event_type === false){
			$banner_type = array_combine(['hero', 'brand', 'special', 'partner', 'ewallet', 'arrive'], $banner_val);
		}else{
			if($event_type === 0 || $event_type === TRUE) $banner_type = array_combine(['banner_hero', 'banner_brand', 'banner_special', 'banner_partner', 'banner_ewallet', 'banner_arrive'], $banner_val);

			if($event_type === 0){
				$now_date = date('Y-m-d H:i:s');
				$event_banner = DB::table('jocommy_banner_event_format')->join(
					DB::raw('( SELECT * FROM jocommy_banner_event AS ef WHERE status != 0 AND start_at <= "' . $now_date . '" AND end_at >= "' . $now_date . '" ) AS e'), 
					function($join){
						$join->on('e.id', '=', 'jocommy_banner_event_format.event_id');
				})->select('jocommy_banner_event_format.banner_type', 'jocommy_banner_event_format.banner_type_name')->lists('banner_type_name', 'banner_type');
				$banner_type = array_merge($banner_type, $event_banner);
			}

			if(is_int($event_type) && $event_type)
				$banner_type = DB::table('jocommy_banner_event_format')->select('banner_type', 'banner_type_name')->where('event_id', $event_type)->lists('banner_type_name', 'banner_type');
		}

		return $banner_type;
	}

	private function BannerCheckValid($id = false){
		$check = $this->BannerFlashTypeCheck();
		if(!$check['match']) return ['error' => 1, 'msg' => $check['msg']];

		$check = $this->BannerContentCheck();
		if(!$check['match']) return ['error' => 1, 'msg' => $check['msg']];
		$content_data = $check['data'];

		$this->BannerPositionSort($id, DB::table('jocommy_banner_manage')->where('type', Input::get('banner_type'))->where('status', '!=', 2));

		$data = $this->BannerDurationCheck();
		if($data['error']) return $data;
		$begin_at = $data['begin_at'];
		$duration = $data['duration'];

		$data = $this->BannerImageValid();
		if($data['error']) return $data;

		return [
			'no_update'     => (isset($data['no_update']) ? $data['no_update'] : false),
			'image_path'    => $data['image_path'],
			'image_path_m'  => $data['image_path_m'],
			'content_data'  => $content_data,
			'begin_at'      => $begin_at,
			'duration'      => $duration,
		];
	}

	private function BannerFetchDBformat($data, $type = 'create'){
		if(substr(Input::get('banner_type'), 0, 7 ) !== "banner_"){
			$result = DB::table('jocommy_banner_event_format')->where('banner_type', Input::get('banner_type'))->first();
			if(count($result)) $event_id = $result->event_id;
		}

		$return_data = [
			'type'              => Input::get('banner_type'),
			'content_type'      => Input::get('content_type'),
			'content_data'      => $data['content_data'],
			'title'             => (!$this->multilang ? Input::get('title') : json_encode(array_combine($this->multilang, Input::get('ltitle')))),
			'status'            => Input::get('status'),
			'position'          => Input::get('position'),
			'logic_operation'   => Input::get('logic_operation'),
			'begin_at'          => $data['begin_at'],
			'duration'          => $data['duration'],
			'event_id'          => (isset($event_id) ? $event_id : 0),
			'region'			=> (Input::get('region') === 'Worldwide All Region' ? 0 : Input::get('region')),
			'allow_multilang'	=> ($this->multilang ? 1 : 0),
			'lang'				=> ($this->multilang ? implode(',', $this->multilang) : 'en'),
			'platform'			=> Input::get('platform'),
			'modify_by'         => Session::get('username'),
			'modify_at'         => date('Y-m-d H:i:s'),
		];
		if ($type === 'create') {
			$return_data['insert_by'] = Session::get('username');
			$return_data['insert_at'] = date('Y-m-d H:i:s');
		}
		if(isset($data['image_path'])) $return_data['image'] = $data['image_path'];
		if(isset($data['image_path_m'])) $return_data['image_m'] = $data['image_path_m'];
		return $return_data;
	}

	private function ClearCache(){
		$env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
		$ch = curl_init(Config::get('constants.JOCOM_WEBAPI_BASE_' . $env) . '_api_call.php');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'action_type' => 'banner_reload',
			'API_KEY' => '55EC9F585111E1FAD6CEDFC4F663F',
		]);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

		return curl_exec($ch);
	}

	private function BannerDataReturnFormat($type, $count, $banner_data = false){
		$format = [
			'type'				=> $type, 
			'banner_type'		=> $this->BannerType(0),
			'banner_img'		=> DB::table('jocommy_banner_event_format')->select('banner_type', 'banner_exp_img')->lists('banner_exp_img', 'banner_type'),
			'template'			=> DB::table('jocommy_template')->select('id', 'name')->where('status', 1)->orderBy('id', 'DESC')->lists('name', 'id'),
			'content_type'		=> $this->banner_config['content_type'],
			'logic_operation'	=> $this->banner_config['logic_operation'],
			'status'			=> $this->banner_config['status'],
			'max_count'			=> $count,
			'scheduler_type'	=> $this->banner_config['scheduler_type'],
			'scheduler_week'	=> $this->banner_config['scheduler_week'],
			'scheduler_month'	=> $this->banner_config['scheduler_month'],
			'region'			=> DB::table('jocom_region')->lists('region', 'id'),
			'lang'				=> DB::table('language')->lists('name', 'code'),
			'platform'			=> $this->banner_config['platform'],
			'multilang'			=> (isset($banner_data->allow_multilang) ? $banner_data->allow_multilang : 0),
			'contenttype'		=> (isset($banner_data->content_type) ? $banner_data->content_type : false)
		];
		if($banner_data) $format['banner_data'] = $banner_data;
		return $format;
	}

	public function anyCreate(){
	   // print_r(Request::all());
	   
		if(Request::isMethod('post')){
			$valid = $this->BannerMultilangCheck();
			if(!$valid) return Redirect::back()->withInput()->with('message', 'Invalid Data');
			$data = $this->BannerCheckValid();
			if($data['error']) return Redirect::back()->withInput()->with('message', $data['msg']);
			DB::table('jocommy_banner_manage')->insert($this->BannerFetchDBformat($data, 'create'));
			$this->ClearCache();
            
			return Redirect::to('jocommy/index')->with('success', 'Banner has been updated.');
		}else{
			$count = DB::table('jocommy_banner_manage')->select(DB::raw('COUNT(id) as count_num'))->where('status', '!=', 2)->first();
			$count = (isset($count->count_num) && $count->count_num > 0 ? $count->count_num : 0);
// 			echo '<pre>';
// 			print_r($this->BannerDataReturnFormat('Create new', $count));
// 			echo '</pre>';
//              die($count);
			return View::make('jocommy.manage', $this->BannerDataReturnFormat('Create new', $count));
		}
	}

	public function anyUpdate($id){
		if(Request::isMethod('post')){
			$valid = $this->BannerMultilangCheck();
			if(!$valid) return Redirect::back()->withInput()->with('message', 'Invalid Data');
			$data = $this->BannerCheckValid($id);
			if($data['error']) return Redirect::back()->withInput()->with('message', $data['msg']);
			DB::table('jocommy_banner_manage')->where('id', $id)->update($this->BannerFetchDBformat($data, 'update'));
			$this->ClearCache();

			return Redirect::to('jocommy/index')->with('success', 'Banner has been updated.');
		}else{
			$count = DB::table('jocommy_banner_manage')->select(DB::raw('COUNT(id) as count_num'))->where('status', '!=', 2)->first();
			$count = (isset($count->count_num) && $count->count_num > 0 ? $count->count_num : 0);

			return View::make('jocommy.manage', $this->BannerDataReturnFormat('Edit', $count, DB::table('jocommy_banner_manage')->where('id', $id)->first()));
		}
	}

	public function anyDelete($id){
		DB::table('jocommy_banner_manage')->where('status', '!=', 2)->where('id', $id)->delete();
		return Redirect::to('jocommy/index')->with('success', 'Banner ' . $id . ' has been remove.');
	}

	private function EventExampleImageValid(){
		$filename_list = [];
		foreach (Input::get('image_ref') as $key => $ref_val) {
			if(!$ref_val && !isset(Input::file('image')[$key])){
				return ['error' => 1, 'msg' => 'Example Banner Image must be upload inorder to proceed'];
			}else if(isset(Input::file('image')[$key])){
				$image = Input::file('image')[$key];
				$new_filename = 'tips_' . preg_replace("/[^a-z0-9.]+/i", "", strtolower(Input::get('eventname'))) . '_' . Input::get('banner_type')[$key] . '.' . $image->getClientOriginalExtension();
				$image->move(Config::get('constants.JOCOMMY_BANNER_PATH'), $new_filename);
				$filename_list[$key] = $new_filename;
			}else if($ref_val){
				$filename_list[$key] = $ref_val;
			}
		}

		return (isset($filename_list) ? ['filename_list' => $filename_list] : ['no_update' => 'no_update']);
	}

	private function EventDurationValid(){
		if(!Input::get('beign_date') || !Input::get('end_date')) return ['error' => 1, 'msg' => 'Both Duration must not be empty'];
		if(strtotime(Input::get('end_date')) <= strtotime(Input::get('beign_date'))) return ['error' => 1, 'msg' => 'Banner End At duration must be greater than Beign at date'];

		return ['begin_at' => Input::get('beign_date'), 'end_at' => Input::get('end_date')];
	}

	private function EventCheckValid(){
		if(!Input::get('eventname')) return ['error' => 1, 'msg' => 'Event name must not empty'];
		
		if(strlen(Input::get('eventname')) > 40) return ['error' => 1, 'msg' => 'Event name must not exceed more than 40 characters'];

		if(count(array_unique(Input::get('banner_type'))) != count(Input::get('banner_type'))) return ['error' => 1, 'msg' => 'Banner Type for each banner cannot be same, Only unique is allow'];

		$data = $this->EventDurationValid();
		if($data['error']) return $data;

		return $this->EventExampleImageValid();
	}

	private function InsertEventFormat($id, $event_ref_name, $filename_list, $banner_type){
		$data = [];
		foreach (Input::get('banner_type') as $key => $b_type) {
			$data[] = [
				'event_id'          => $id,
				'type'              => 'banner_' . $b_type,
				'banner_type'       => $event_ref_name . '_' . $b_type,
				'banner_type_name'  => Input::get('eventname') . ' ' . $banner_type[$b_type],
				'banner_exp_img'    => $filename_list[$key],
			];
		}
		DB::table('jocommy_banner_event_format')->insert($data);
	}

	public function anyEvent(){
		if(Request::isMethod('post')){
			$jocom_event = DB::table('jocommy_banner_event');
			if(Input::get('banner_type') && strtolower(Input::get('banner_type')) !== 'any'){
				$jocom_event->join(
					DB::raw('(
						SELECT GROUP_CONCAT(ef2.type) AS type, GROUP_CONCAT(ef2.banner_type_name) AS type_name, ef2.event_id 
						FROM (
							SELECT event_id 
							FROM jocommy_banner_event_format 
							WHERE type = "banner_' . Input::get('banner_type') . '"
						) AS ef 
						LEFT JOIN jocommy_banner_event_format AS ef2 ON ef2.event_id = ef.event_id
						GROUP BY ef2.event_id 
					) AS ef'), 
					function($join){
						$join->on('jocommy_banner_event.id', '=', 'ef.event_id');
				});
			}else{
				$jocom_event->leftJoin(
					DB::raw('
						( SELECT GROUP_CONCAT(ef.type) AS type, GROUP_CONCAT(ef.banner_type_name) AS type_name, event_id 
						FROM jocommy_banner_event_format AS ef GROUP BY event_id ) AS ef
					'), 
					function($join){
						$join->on('jocommy_banner_event.id', '=', 'ef.event_id');
				});
			}

			if (strtolower(Input::get('status')) !== 'any' && (Input::get('status') || Input::get('status') === "0") && (int)Input::get('status') >= 0) $jocom_event->where('status', Input::get('status'));

			$jocom_event->select(['id', 'name', 'status', 'type', 'type_name', 'start_at', 'end_at', 'insert_at', 'insert_at', 'modify_at', 'modify_by']);
			
			return Datatables::of($jocom_event)
				->edit_column('status', function($row){
					return '<span class="label ' . ($row->status == 0 ? 'label-default">Inactive' : ($row->status == 1 ? 'label-warning">Active' : 'label-danger">Delete')) . '</span>';
				})->edit_column('type', function($row){
					return '<p>' . implode('</p><p>', explode(',', $row->type)) . '</p>';
				})->add_column('last_action', function($row){
					return '<p>' . $row->insert_at . '</p><p>' . $row->insert_by . '</p><p>' . $row->modify_at . '</p><p>' . $row->modify_by . '</p>';
				})
				->remove_column('insert_at')->remove_column('insert_by')->remove_column('modify_at')->remove_column('modify_by')
				->add_column('Action', function($row){
					$edit = '';
					if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
						$edit .= '<a class="btn btn-primary btn-sm" data-toggle="tooltip" href="'.url('jocommy/eventup/' . $row->id).'"><i class="fa fa-pencil"></i></a> ';

					if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 9, 'AND'))
						$edit .= '<a id="deleteItem" class="btn btn-danger btn-sm" data-toggle="tooltip" data-value="{{ $id }}" href="'.url('jocommy/eventdel/'.$row->id).'"><i class="fa fa-remove"></i></a>';

					return $edit;
				})->make();
		}
		return View::make('jocommy.eventidx', ['banner_type' => $this->BannerType(false)]);
	}

	public function anyEventup($id){
		$banner_type = $this->BannerType();

		if(Request::isMethod('post')){
			$data = $this->EventCheckValid();
			if(isset($data['error']) && $data['error']) return Redirect::back()->withInput()->with('message', $data['msg']);
			$filename_list = $data['filename_list'];
			$event_ref_name = preg_replace("/[^a-z0-9.]+/i", "", strtolower(Input::get('eventname')));
			DB::table('jocommy_banner_event')->where('id', $id)->update([
				'name' => Input::get('eventname'),
				'ref_name' => $event_ref_name,
				'status' => Input::get('status'),
				'start_at' => Input::get('beign_date'),
				'end_at' => Input::get('end_date'),
				'modify_by' => Session::get('username'),
				'modify_at' => date('Y-m-d H:i:s'),
			]);

			DB::table('jocommy_banner_event_format')->where('event_id', $id)->delete();
			$this->InsertEventFormat($id, $event_ref_name, $filename_list, $banner_type);
			$this->ClearCache();

			return Redirect::to('jocommy/event')->with('success', 'Event/Camping Banner Type has been updated.');
		}
		return View::make('jocommy.event', [
			'type'          => 'Edit',
			'banner_type'   => $banner_type,
			'status'        => $this->banner_config['status'],
			'event'         => DB::table('jocommy_banner_event')->where('id', $id)->first(),
			'event_banner'  => DB::table('jocommy_banner_event_format')->where('event_id', $id)->get(),
		]);
	}

	public function anyEventnew(){
		$banner_type = $this->BannerType();

		if(Request::isMethod('post')){
			$data = $this->EventCheckValid();
			if(isset($data['error']) && $data['error']) return Redirect::back()->withInput()->with('message', $data['msg']);
			$filename_list = $data['filename_list'];
			$event_ref_name = preg_replace("/[^a-z0-9.]+/i", "", strtolower(Input::get('eventname')));
			DB::table('jocommy_banner_event')->insert([
				'name' => Input::get('eventname'),
				'ref_name' => $event_ref_name,
				'status' => Input::get('status'),
				'start_at' => Input::get('beign_date'),
				'end_at' => Input::get('end_date'),
				'insert_by' => Session::get('username'),
				'insert_at' => date('Y-m-d H:i:s'),
				'modify_at' => date('Y-m-d H:i:s'),
			]);

			$id = DB::getPdo()->lastInsertId();
			$this->InsertEventFormat($id, $event_ref_name, $filename_list, $banner_type);
			$this->ClearCache();

			return Redirect::to('jocommy/event')->with('success', 'Event/Camping Banner Type has been created.');
		}
		return View::make('jocommy.event', [
			'type'          => 'Create New', 
			'banner_type'   => $banner_type,
			'status'        => $this->banner_config['status'],
		]);
	}

	public function anyEventdel($id){
		DB::table('jocommy_banner_event')->where('status', '!=', 2)->where('id', $id)->delete();
		DB::table('jocommy_banner_event_format')->where('event_id', $id)->delete();
		return Redirect::to('jocommy/event')->with('success', 'Event/Camping Banner Type ' . $id . ' has been remove.');
	}

	public function anyTemplate(){
		if(Request::isMethod('post')){
			$jocom_template = DB::table('jocommy_template');

			if (Input::get('name_id')){
				if(is_numeric(Input::get('name_id')))
					$jocom_template->where('id', (int)Input::get('name_id'));
				else
					$jocom_template->where('name', Input::get('name_id'));
			}

			if (strtolower(Input::get('status')) !== 'any' && (Input::get('status') || Input::get('status') === "0") && (int)Input::get('status') >= 0) $jocom_template->where('status', Input::get('status'));

			$jocom_template->select(['id', 'name', 'status', 'created_at', 'created_by', 'modify_at', 'modify_by']);
			
			return Datatables::of($jocom_template)
				->edit_column('status', function($row){
					return '<span class="label ' . ($row->status == 0 ? 'label-default">Inactive' : ($row->status == 1 ? 'label-warning">Active' : 'label-danger">Delete')) . '</span>';
				})->add_column('last_action', function($row){
					return '<p>' . $row->created_at . '</p><p>' . $row->created_by . '</p><p>' . $row->modify_at . '</p><p>' . $row->modify_by . '</p>';
				})
				->remove_column('created_at')->remove_column('created_by')->remove_column('modify_at')->remove_column('modify_by')
				->add_column('Action', function($row){
					$edit = '';
					if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
						$edit .= '<a class="btn btn-primary btn-sm" data-toggle="tooltip" href="'.url('jocommy/templatedit/'.$row->id).'"><i class="fa fa-pencil"></i></a> ';

					if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 9, 'AND'))
						$edit .= '<a id="deleteItem" class="btn btn-danger btn-sm" data-toggle="tooltip" data-value="{{ $id }}" href="'.url('jocommy/templatedel/'.$row->id).'"><i class="fa fa-remove"></i></a>';

					return $edit;
				})->make();
		}
		return View::make('jocommy.templateidx');
	}

	private function TemplateImageMoveNSet($image, $ref_name, $extra_ref = false){
		$filename = 'template' . $ref_name . ($extra_ref ? $extra_ref : '') . '-' . time() . '.' . $image->getClientOriginalExtension();
		$image->move(Config::get('constants.JOCOMMY_BANNER_PATH'), $filename);
		return $filename;
	}

	private function TemplateImageValid(){
		$content_image = []; $banner_image = '';
		foreach (['banner_image', 'content_image'] as $type) {
			if(Input::file($type) && !Input::get($type . '_ref')){
				if($type === 'content_image'){
					$count_img = (count(Input::file($type)) >= 1 ? (Input::file($type)[0] ? count(Input::file($type)) : 0) : 0);
					if($count_img != count($this->template_imgno)) return ['error' => 1, 'msg' => 'Content Image must be equal to amount of upload image.'];
					if($count_img > 0)
						foreach (Input::file($type) as $key => $value) {
							$idx = $key + 1;
							if(!in_array($idx, $this->template_imgno)) return ['error' => 1, 'msg' => 'Image index must set according to upload image index.'];
							$content_image[$idx] = $this->TemplateImageMoveNSet($value, '_' . explode('_', $type)[0], '_' . $idx);
						}
				}else{
					$banner_image = $this->TemplateImageMoveNSet(Input::file($type), '_' . explode('_', $type)[0]);
				}
			}
		}
		return ['banner_image' => $banner_image, 'content_image' => $content_image];
	}

	private function TemplateArrayPattern($value, $expreg_pattern, $pattern_html){
		if(preg_match_all("/" . implode(".*", $expreg_pattern) . "/", $value, $match)){
			if(!preg_match('/(<[bB]><\/[bB]>)/', implode('', $pattern_html))) self::$headerhtml = true;
			$d_set = array_merge(...array_values($match));
			unset($d_set[0]);
			$d_set = array_values($d_set);
			foreach ($d_set as $ref => $val) $value = preg_replace($expreg_pattern[$ref], $pattern_html[$ref], $value, 1);
		}
		return $value;
	}

	private function TemplateAnonymousFunction($value, $expreg_pattern, $f_anonymous){
		if(preg_match_all($expreg_pattern, $value, $match)) foreach (array_merge(...array_unique($match)) as $val) $value = $f_anonymous($expreg_pattern, $value, $val);
		return $value;
	}

	private function TemplateFetchDBFormat($data, $type = 'create', $id = false){
		$format = [
			'name' => Input::get('name'),
			'content_input' => Input::get('content'),
			'content_html' => $data['content_html'],
			'status' => Input::get('status'),
			'modify_by' => Session::get('username'),
		];
		if($data['banner_image']) $format['banner_image'] = $data['banner_image'];
		if($type === 'create') $format['created_by'] = Session::get('username');

		if(count($data['content_image']) > 0){
			foreach ($data['content_image'] as $idx => $value) $format['content_html'] = preg_replace("/({\|{{IMG_" . $idx . "}}\|})/", $value, $format['content_html'], 1);
			$format['content_image'] = implode('|', $data['content_image']);
		}else{
			if($type === 'edit' && $id){
				$result = DB::table('jocommy_template')->where('id', $id)->select('content_image')->first();
				if($result) foreach (explode('|', $result->content_image) as $idx => $value) $format['content_html'] = preg_replace("/({\|{{IMG_" . ((int)$idx + 1) . "}}\|})/", $value, $format['content_html'], 1);
			}
		}

		return $format;
	}

	private function TemplateContentParser(){
		$format = []; $html = ''; $f_pattern = ['number', 'bracket', 'bullet', 'dash']; $is_error = false; $count = 0;
		foreach (preg_split("/\r\n|\n|\r/", Input::get('content')) as $index => $value) {
			$value = strip_tags($value); self::$headerhtml = false;
			if(($key = array_search(trim($value), ['&|Format|Number|START|&', '&|Format|Bracket|START|&', '&|Format|Bullet|START|&', '&|Format|Dash|START|&'])) !== false){
				$format[] = $f_pattern[$key];
				$html .= '<ol class="childcount' . ($key ? ' ' . $f_pattern[$key] : '') .'">';
			} else if (($key = array_search(trim($value), ['&|Format|Number|END|&', '&|Format|Bracket|END|&', '&|Format|Bullet|END|&', '&|Format|Dash|END|&'])) !== false){
				$format = array_values($format);
				if (($key = array_search($f_pattern[$key], array_reverse($format))) !== false) unset($format[$key]);
				$html .= '</ol>';
				$count++;
			}else{
				$template_imgno = &$this->template_imgno;
				$base_url = url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH');
				$value = $this->TemplateAnonymousFunction($value, "/(&\|Image\|[0-9]+\|&)/i", function($expreg_pattern, $value, $val) use (&$template_imgno, $base_url){
					$template_imgno[] = $img_no = (int)preg_replace("/[^0-9]/", "", $val);
					return preg_replace($expreg_pattern, '<img class="lazy" src="image/lazy-load.gif" data-src="' . $base_url . '{|{{IMG_' . $img_no . '}}|}' . '">', $value, 1);
				});

				$value = $this->TemplateAnonymousFunction($value, "/(&\|URL\|(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+]{2}==|[A-Za-z0-9+]{3}=)?\|(NEW|)\|[\w\'\"\+\-\.!@:;#&%^*<>~`\(\)\{\}\[\]\/\\\ ]+\|&)/", function ($expreg_pattern, $value, $val) {
					$parm = explode('|', trim(trim($val, '&'), '|'));
					return preg_replace($expreg_pattern, '<a href="' . base64_decode($parm[1]) . '"' . ($parm[2] ? ' target="_blank"' : '') . '>' . $parm[3] . '</a>', $value, 1);
				});

				$value = $this->TemplateArrayPattern($value, ["(&\|TEXT\|BOLD\|START\|&)", "(&\|TEXT\|BOLD\|END\|&)"], ["<b>", "</b>"]);
				$value = $this->TemplateArrayPattern($value, ["(&\|TEXT\|CENTER\|START\|&)", "(&\|TEXT\|CENTER\|END\|&)"], ['<p style="text-align: center;">', "</p>"]);
				for ($i = 1; $i <= 4; $i++) $value = $this->TemplateArrayPattern($value, ["(&\|HEADER_" . $i . "\|START\|&)", "(&\|HEADER_" . $i . "\|END\|&)"], ["<h" . $i . ">", "</h" . $i . ">"]);

				$html .= (count($format) > 0 ? '<li><p>' . $value . '</p></li>' : (!self::$headerhtml && !preg_match('/.*?(?<=<[pP]>).*?(?=<\/[pP]>)/', $value) ? '<p>' . $value . '</p>' : $value));
			}
		}

		return $html;
	}

	private function templatecheck(){
		if(!Input::get('name')) return Redirect::back()->withInput()->with('message', 'Template Name is require');

		if(!Input::get('content'))
			return Redirect::back()->withInput()->with('message', 'Content is require');
		else
			$this->html = $this->TemplateContentParser();

		$this->result = $this->TemplateImageValid();
		if(isset($result['error'])) return Redirect::back()->withInput()->with('message', $this->result['msg']);
		return false;
	}

	public function anyTemplatenew(){
		if(Request::isMethod('post')){
			$err = $this->templatecheck();
			if($err) return $err;

			DB::table('jocommy_template')->insert($this->TemplateFetchDBFormat(['banner_image' => $this->result['banner_image'], 'content_image' => $this->result['content_image'], 'content_html' => $this->html]));

			return Redirect::to('jocommy/template')->with('success', 'Template has been generated.');
		}
		return View::make('jocommy.template', ['title' => 'Create', 'status' => $this->banner_config['status']]);
	}

	public function anyTemplatedit($id){
		if(Request::isMethod('post')){
			$err = $this->templatecheck();
			if($err) return $err;

			DB::table('jocommy_template')->where('id', $id)->update($this->TemplateFetchDBFormat(['banner_image' => $this->result['banner_image'], 'content_image' => $this->result['content_image'], 'content_html' => $this->html], 'edit', $id));

			return Redirect::to('jocommy/template')->with('success', 'Template has been updated.');
		}
		return View::make('jocommy.template', ['title' => 'Edit', 'status' => $this->banner_config['status'], 'data' => DB::table('jocommy_template')->where('id', $id)->first(), 'id' => $id]);
	}

	public function anyTemplatedel($id){
		DB::table('jocommy_template')->where('status', '!=', 2)->where('id', $id)->delete();
		return Redirect::to('jocommy/template')->with('success', 'Banner Template ' . $id . ' has been remove.');
	}
}
?>