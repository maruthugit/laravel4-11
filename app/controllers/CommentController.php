<?php

class CommentController extends BaseController {
	protected $product;
	protected $comment;
	private $status = [
		0 => 'Pending / Inactive / Waiting Approve',
		1 => 'Active / Approve',
		2 => 'Soft Delete / Disable'
	];
	private $lang = [
		'EN' => 'English',
		'CN' => 'Chinese',
		'MY' => 'Malay'
	];

	public function __construct(Comment $comment, Product $product) {
		$this->product = $product;
		$this->comment = $comment;
	}

	/**
	 * Display the comment page.
	 *
	 * @return Page
	 */
	public function anyIndex() {
		return View::make('comment.index');
	}

	/**
	 * Display a listing of the comment resource.
	 *
	 * @return Response
	 */
	public function anyComments() {		
		$comments = $this->comment->select([
			'jocom_comments.id', 
			'jocom_comments.comment_date', 
			'jocom_user.full_name',
			'jocom_products.name',
			'jocom_comments.comment', 
			'jocom_comments.rating',
			'jocom_comments.status'
		])
		->leftJoin('jocom_user', 'jocom_comments.user_id', '=', 'jocom_user.id')
		->leftJoin('jocom_products', 'jocom_comments.product_id', '=', 'jocom_products.id')
		->where('jocom_comments.status', '!=', '2');
		$status = $this->status;
		$label_color = [0 => 'warning', 1 => 'success', 2 => 'danger']; // primary info

		return Datatables::of($comments)
			->edit_column('status', function($row) use ($status, $label_color){
                return '<span class="label label-' . $label_color[$row->status] . '">' . trim(explode(' / ', $status[$row->status])[0]) . '</span>';
            })
			->add_column('Action', '
				<a class="btn btn-primary" title="" data-toggle="tooltip" href="/comment/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
				@if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 3, 9, \'AND\'))
					<a id="deleteItem" class="btn btn-danger" title="" data-toggle="tooltip" href="/comment/delete/{{$id}}"><i class="fa fa-remove"></i></a>
				@endif
			')
			->make();
	}

	/**
	 * Display a listing of the products resource.
	 *
	 * @return Response
	 */
	public function anyCustomersajax() {    
		$customers = DB::table('jocom_user')->select('id', 'username', 'full_name', 'email');

		return Datatables::of($customers)
			->add_column('Action', '<a id="selectCust" class="btn btn-primary" title="" href="../customer/{{$id}}">Select</a>')
			->make();
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $product_id
	 * @return Response
	 */
	public function anyAjaxcustomer() {
		return View::make('comment.ajaxcustomer');
	}

	/**
	 * Display a listing of the products resource.
	 *
	 * @return Response
	 */
	public function anyProductsajax() {		
		$products = DB::table('jocom_products')->select([
			'jocom_products.id', 
			'jocom_products.sku', 
			'jocom_seller.company_name',
			'jocom_products.name',
			'jocom_products_category.category_name',
		])
		->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
		->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
		->where('jocom_products.status', '!=', '2');

		return Datatables::of($products)
			->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/product/{{$id}}">Select</a>')
			->make();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $product_id
	 * @return Response
	 */
	public function anyAjaxproduct() {
		return View::make('comment.ajaxproduct');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function anyCreate() {
		if (Permission::CheckAccessLevel(Session::get('role_id'), 3, 5, 'AND'))
			return View::make('comment.create')->with(['status' => $this->status, 'lang' => $this->lang]);
		else
			return View::make('home.denied', array('module' => 'Comments > Add Comment'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function anyStore() {
		$input = Input::all();

		if( !$this->comment->fill($input)->isValid() ) {
			return Redirect::back()->withInput()->withErrors($this->comment->errors);
		} else {
			$lang = (in_array(trim(Input::get('lang')), array_keys($this->lang)) ? trim(Input::get('lang')) : 'EN');
			$id = DB::table('jocom_comments')->insertGetId([
				'comment_date' => trim(Input::get('comment_date')),
				'user_id' => trim(Input::get('user_id')), 
				'product_id' => trim(Input::get('product_id')),
				'comment' => trim(Input::get('comment')),
				'image' => json_encode($img),
				'rating' => trim(Input::get('rating')),
				'lang' => $lang,
				'insert_date' => date('Y-m-d H:i:s'),
				'modify_date' => date('Y-m-d H:i:s'),
				'status' => (int)Input::get('status'),
			]);

			if(count(Input::file('image')) && isset(Input::file('image')[0]) && Input::file('image')[0]){
				$img = [];
				if(!file_exists('./' . Config::get('constants.COMMENT_IMG_PATH'))) mkdir('./' . Config::get('constants.COMMENT_IMG_PATH'), 0755, true);
				foreach (Input::file('image') as $image) {
					if(!in_array(strtolower($image->getClientOriginalExtension()), ['png', 'jpg', 'gif', 'jpeg']) || $image->getSize() > 1000000) continue;
					$filename = $id . '_' . md5(openssl_random_pseudo_bytes(4)) . '_' . time() . '.' . $image->getClientOriginalExtension();
					$image->move(Config::get('constants.COMMENT_IMG_PATH'), $filename);
					$img[] = $filename;
				}

				DB::table('jocom_comments')->where('id', $id)->update(['image' => json_encode($img)]);
			}

			Session::flash('message', ($id ? 'Successfully updated.' : 'Error. Unknown error occured.'));
			Cache::forget('prode_JC' . trim(Input::get('product_id')) . '_' . $lang); // clear these product cache prevent issue
			return Redirect::to('comment');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $comment_id
	 * @return Response
	 */
	public function anyEdit($comment_id) {
		$comment = $this->comment->select([
			'jocom_comments.id', 
			'jocom_comments.comment_date', 
			'jocom_user.full_name',
			'jocom_user.id as userid',
			'jocom_products.name',
			'jocom_products.id as prodid',
			'jocom_comments.comment',
			'jocom_comments.image',
			'jocom_comments.rating',
			'jocom_comments.lang',
			'jocom_comments.status'
		])
		->leftJoin('jocom_user', 'jocom_comments.user_id', '=', 'jocom_user.id')
		->leftJoin('jocom_products', 'jocom_comments.product_id', '=', 'jocom_products.id')
		->where('jocom_comments.status', '!=', '2')
		->where('jocom_comments.id', '=', $comment_id)
		->first();

		return View::make('comment.edit')->with(['comment' => $comment, 'status' => $this->status, 'lang' => $this->lang]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $comment_id
	 * @return Response
	 */
	public function anyUpdate($comment_id) {
		$input = Input::all();
		if(!$this->comment->fill($input)->isValid()) return Redirect::back()->withInput()->withErrors($this->comment->errors);

		$img = [];
		if(count(Input::file('image')) && isset(Input::file('image')[0]) && Input::file('image')[0]){
			if(!file_exists('./' . Config::get('constants.COMMENT_IMG_PATH'))) mkdir('./' . Config::get('constants.COMMENT_IMG_PATH'), 0755, true);
			foreach (Input::file('image') as $image) {
				if(!in_array(strtolower($image->getClientOriginalExtension()), ['png', 'jpg', 'gif', 'jpeg']) || $image->getSize() > 1000000) continue;
				$filename = $comment_id . '_' . md5(openssl_random_pseudo_bytes(4)) . '_' . time() . '.' . $image->getClientOriginalExtension();
				$image->move(Config::get('constants.COMMENT_IMG_PATH'), $filename);
				$img[] = $filename;
			}
		}
		if(Input::get('image_ref')){
			$ref = explode(',', Input::get('image_ref'));
			$ref = array_filter($ref);
			$img = array_merge($img, $ref);
		}

		$lang = (in_array(trim(Input::get('lang')), array_keys($this->lang)) ? trim(Input::get('lang')) : 'EN');

		$comment = $this->comment->find($comment_id);
		$comment->comment_date = trim(Input::get('comment_date'));
		$comment->user_id = trim(Input::get('user_id'));
		$comment->product_id = trim(Input::get('product_id'));
		$comment->comment = trim(Input::get('comment'));
		if(count($img)) $comment->image = json_encode($img);
		$comment->rating = trim(Input::get('rating'));
		$comment->lang = $lang;
		$comment->status = (int)Input::get('status');
		$comment->save();

		Session::flash('message', 'Successfully updated.');
		Cache::forget('prode_JC' . trim(Input::get('product_id')) . '_' . $lang); // clear these product cache prevent issue
		return Redirect::to('comment');
	}

	/**
	 * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
	 *
	 * @param  int  $comment_id
	 * @return Response
	 */
	public function anyDelete($comment_id) {
		$comment = $this->comment->find($comment_id);
		$comment->status = 2;
		$comment->save();

		Session::flash('message', 'Successfully deleted.');
		Cache::forget('prode_JC' . $comment->product_id . '_' . $comment->lang); // clear these product cache prevent issue
		return Redirect::to('comment');
	}
}