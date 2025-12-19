<?php
class BlogController extends BaseController {
	private static $is_error = 0;
	private function DB_ACT(){
		if(self::$is_error)
			DB::rollback();
		else
			DB::commit();
	}

	private function ModelUpdate(&$model_obj = false, $id = false){
		if($model_obj){ // Model Object == Blog Post
			$model_obj->title = Input::get("title");
			$model_obj->category_id = Input::get("category");
			$model_obj->is_publish = 0;
			$model_obj->published_date = Input::get("publish_date");
			$model_obj->status = Input::get("is_active");
			$model_obj->author = Session::get("username");
			$model_obj->content = htmlentities(preg_replace('/([\t]|[ ]{2,})/i', '', urldecode(Input::get("content"))), ENT_QUOTES); // strip all tab space
			$model_obj->is_pinned_post = Input::get("pin_post");
			$model_obj->updated_by = Session::get("username");
			if(!$id) $model_obj->created_by = Session::get("username");
		}
	}

	private function ModelTagUpdate($tag_data, $blog_id){
		if(count($tag_data) > 0){
			$d = [];
			foreach ($tag_data as $value) {
				$d[] = [
					'post_id' => $blog_id,
					'tag' => $value,
					'created_by' => Session::get("username")
				];
			}
			BlogTag::insert($d);
		}
	}

	private function storeContentHTML($blog_obj = false, $content = ''){
		$f = fopen(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $blog_obj->id . ".txt", "w");
		fwrite($f, $content);
		fclose($f);
		
		if(Input::hasFile('file')) {
			$file                   = Input::file('file');
			$file_ext               = $file->getClientOriginalExtension();
			$filename               = date("Ymdhis") . "_" . $blog_obj->id . "." . $file_ext;
			$file_path = Config::get('constants.IMAGE_CONTENT_BLOG_PATH');
			Input::file('file')->move($file_path . '/', $filename);
			if($blog_obj->main_image_id != ''){
				File::delete($file_path . '/'. $blog_obj->main_image_id);
			}
			
			$blog_obj->main_image_id = $filename;
			$blog_obj->save();
		}
	}

	public function index(){
		return View::make('blog.index');
	}
	
	public function getPosts() {
		try{
			$posts = DB::table('jocom_blog_posts')
				->select(['jocom_blog_posts.id', 'jocom_blog_posts.title', 'jocom_blog_category.category', 'jocom_blog_posts.author', 'jocom_blog_posts.created_at', 'jocom_blog_posts.status', 'jocom_blog_posts.is_publish', 'jocom_blog_posts.published_date'])
				->leftJoin('jocom_blog_category', 'jocom_blog_category.id', '=', 'jocom_blog_posts.category_id')
				->where('jocom_blog_posts.activation',1)
				->orderBy('jocom_blog_posts.id','DESC');
			
			return Datatables::of($posts)->make(true);
		} catch (Exception $ex) { echo $ex->getMessage(); }
	}

	public function getMigrate() {
		try{
			$blog_lists = DB::table('jocom_blog_posts')->whereNull('jocom_blog_posts.content')->orderBy('jocom_blog_posts.id','DESC')->lists('id');

			if(count($blog_lists) > 0){
				$case_query = '';
				foreach ($blog_lists as $blog_id) {
					$f_path = Config::get('constants.HTML_CONTENT_BLOG_PATH') . $blog_id . ".txt";
					$c = ' '; // set empty content, if file not found
					if(file_exists($f_path)){
						$f = fopen($f_path, "r");
						if(filesize($f_path)){
							$c = fread($f, filesize($f_path));
							$c = trim(htmlentities(preg_replace('/([\t]|[ ]{2,})/i', '', urldecode($c)), ENT_QUOTES));
						}
						fclose($f);
					}

					DB::table('jocom_blog_posts')->where('id', $blog_id)->update(['content' => $c]);
				}
			}
			return Redirect::to('blog')->with("message", 'content migrate successfully');
		} catch (Exception $ex) { echo $ex->getMessage(); }
	}

	public function createArticle(){
		$categories = DB::table('jocom_blog_category')->where("activation", 1)->get();
		return View::make('blog.create')->with("categories",$categories);
	}

	public function editArticle($id){
		$post = DB::table('jocom_blog_posts')->where("id", $id)->first();
		if(!$post->content){
			$htmlFile = fopen(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $post->id . ".txt", "r") or die("Unable to open file!");
			$htmlString = fread($htmlFile, filesize(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $post->id . ".txt"));
			fclose($htmlFile);
		}else{
			$htmlString = urldecode(html_entity_decode($post->content));
		}

		return View::make('blog.edit')
			->with("categories", DB::table('jocom_blog_category')->where("activation", 1)->get())
			->with("post", $post)
			->with("htmlContent", $htmlString);
	}
	
	
	public function getPostInfo(){
		self::$is_error = 0;
		$message = "";
		
		try{
			$data = [
				"info" => DB::table('jocom_blog_posts')->where("id", Input::get("post_id"))->first(),
				"tags" => DB::table('jocom_blog_tag')->where("post_id", Input::get("post_id"))->get(),
			];
		} catch (Exception $ex) {
			self::$is_error = 1;
			$message = $ex->getMessage();
		}

		return [
			"responseCode" => (self::$is_error ? 0 : 1),
			"message" => $message,
			"data" => (isset($data) ? $data : [])
		];
	}

	public function saveArticle(){
		self::$is_error = 0;
		
		try{
			DB::beginTransaction();

			$tags = strlen(Input::get("tag")) > 0 ? explode(",", Input::get("tag")) : array();
			$htmlContent = preg_replace('/([\t]|[ ]{2,})/i', '', urldecode(Input::get("content")));
			
			// Save Blog Information
			$BlogPosts = new BlogPosts();
			self::ModelUpdate($BlogPosts);

			if($BlogPosts->save()){
				self::ModelTagUpdate($tags, $BlogPosts->id);
				self::storeContentHTML($BlogPosts, $htmlContent);
			}
		} catch (Exception $ex) {
			self::$is_error = 0;
		}

		self::DB_ACT();
	}

	public function updateArticle(){
		self::$is_error = 0;
		
		try{
			DB::beginTransaction();

			$tags = strlen(Input::get("tag")) > 0 ? explode(",", Input::get("tag")) : array();
			$htmlContent = preg_replace('/([\t]|[ ]{2,})/i', '', urldecode(Input::get("content")));
		   
			// Save Blog Information
			$BlogPosts =  BlogPosts::find(Input::get("id"));
			self::ModelUpdate($BlogPosts, Input::get("id"));
			
			if($BlogPosts->save()){
				BlogTag::where("post_id", Input::get("id"))->delete();
				self::ModelTagUpdate($tags, Input::get("id"));
				self::storeContentHTML($BlogPosts, $htmlContent);
			}
		} catch (Exception $ex) {
			self::$is_error = 1;
			$errorCode = $ex->getMessage();
		}

		self::DB_ACT();

		return [
			"responseCode" => (self::$is_error ? 0 : 1),
			"message" => (self::$is_error ? "Failed to update" : "Article successfully saved!"),
			"errorCode" => $errorMessage,
		];
	}

	public function Removepost(){
		self::$is_error = 0;

		try{
			DB::beginTransaction();
			$BlogPosts = BlogPosts::find(Input::get("id"));
			$BlogPosts->activation = 0;
			$BlogPosts->status = 0;
			$BlogPosts->save();
		} catch (Exception $ex) {
			self::$is_error = 1;
		}

		self::DB_ACT();
		
		return [
			"responseCode" => (self::$is_error ? 0 : 1),
			"message" => (self::$is_error ? 'Failed to delete' : 'Article has been deleted'),
			"data" => Input::get("id")
		];
	}
}
