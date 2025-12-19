<?php

class CategoryController extends BaseController {

    protected $category;

    public function __construct(Category $category) {

        $this->category = $category;

    }

    /**
     * Display the category page.
     * @return Response
     */
    public function anyIndex()
    {
        // $categoriesOptions = $this->arrangeCategories($this->category->sortByWeight()->toArray());

        // return View::make('product.category.index', ['categoriesOptions' => $categoriesOptions]);
        $categoriesOptions = $this->arrangeCategories($this->category->sortByWeight()->toArray());
        $new = array();
        foreach ($categoriesOptions as $a){
            $new[$a['category_parent']][] = $a;
        }
        $tree = $this->createTree($new, $new[0]);

        // return View::make('product.category.index', ['categoriesOptions' => $categoriesOptions]);
        return View::make('product.category.index', ['categoriesOptions' => $tree]);
    }

    public function createTree(&$list, $parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        } 
        return $tree;
    }
    
    /**
     * Display a listing of the categories resource.
     *
     * @return Response
     */
    public function getCategories() {
        // http://stackoverflow.com/questions/10786973/how-to-list-parent-category-and-sub-category-data-with-proper-order
        // WORKING
        $categories = $this->category->select(array(
                                        'jocom_products_category.id',
                                        'jocom_products_category.category_name',
                                        'jocom_products_category.category_descriptions',
                                        'jocom_products_category.status',
                                        'jocom_products_category.category_parent'
                                    ));
        return Datatables::of($categories)
                                    ->edit_column('category_name', '@if($category_parent == 0) {{ $category_name }} @else -- {{ $category_name }} @endif')
                                    ->edit_column('status', '@if($status == 1) <span class="label label-success">Active</span> @else <span class="label label-danger">Inactive</span> @endif')
                                    ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/category/edit/{{ $id }}"><i class="fa fa-pencil"></i></a>
                                        @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 2, 9, \'AND\'))
                                            <a id="deleteItem" class="btn btn-danger" title="" data-toggle="tooltip" href="/product/category/delete/{{ $id }}"><i class="fa fa-remove"></i></a>
                                        @endif
                                        ')
                                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $category_id
     * @return Response
     */
    public function anyShow($id) {

        $category = $this->category->whereid($id)->first();

        return View::make('product.category.show', ['category' => $category]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function anyCreate() {
        $categoriesOptions = $this->arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- ');
        $permissions      = array('0' => 'Public', '1' => 'Private');
        $charity      = array('0' => 'No', '1' => 'Yes');

        if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
            return View::make('product.category.create', ['categoriesOptions' => $categoriesOptions, 'permissions' => $permissions, 'charity' => $charity]);
        else
            return View::make('home.denied', array('module' => 'Products > Add Category'));
        //return View::make('product.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function anyStore() {
        $input = Input::all();

        if(!$this->category->fill($input)->isValid()) return Redirect::back()->withInput()->withErrors($this->category->errors);

        $lastId = $this->category->select('jocom_products_category.id')->orderBy('id', 'DESC')->first();

        // Image
        $imgFilename = '';
        if (Input::hasFile('image')) {
            $image = Input::file('image');
            $imgFilename = ($lastId->id + 1) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('images/category/thumbs/', $imgFilename);
            $this->create_webpimg('./images/category/thumbs/', $imgFilename);
        }

        $imgFilenamebanner = '';
        if (Input::hasFile('image_banner')) {
            $image  = Input::file('image_banner');
            $imgFilenamebanner = ($lastId->id + 1) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('images/category/', $imgFilenamebanner);
            $this->create_webpimg('./images/category/', $imgFilenamebanner);
        }
        // END Image

        $this->category->category_img           = $imgFilename;
        $this->category->category_img_banner    = $imgFilenamebanner;
        $this->category->status                 = Input::get('status');
        $this->category->permission             = Input::get('permission');
        $this->category->charity                = Input::get('charity');
        $this->category->insert_by              = Session::get('username');
        $this->category->modify_by              = Session::get('username');
        $rs = $this->category->save();

        Session::flash('message', ($rs ? 'Successfully added.' : 'Error. Unknown error occured.'));
        return Redirect::to('product/category');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $category_id
     * @return Response
     */
    public function anyEdit($category_id) {

        $category = $this->category->find($category_id);
        $categoriesOptions = $this->arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- ');
        $permissions      = array('0' => 'Public', '1' => 'Private');
        
        $charity        = array('0' => 'No', '1' => 'Yes');
        $charityInfo    = CharityCategory::where('category_id', '=', $category_id)->first();
        $countries      = Delivery::getDeliveryCountries();
        if (count($charityInfo)>0)
        {
            $states = Delivery::getStateList($charityInfo->country);
            $cities = Delivery::getCityList($charityInfo->state);
        }
        else
        {
            $states = array();
            $cities = array();
        }

        $categoryChilds = $this->getChildsID($category_id);

        // show the edit form and pass the category
        return View::make('product.category.edit')->with(array('category' => $category, 'categoriesOptions' => $categoriesOptions, 'permissions' => $permissions, 'categoryChilds' => $categoryChilds, 'charity' => $charity, 'charityInfo' => $charityInfo, 'countries' => $countries, 'states' => $states, 'cities' => $cities));
    }

    public function getChildsID($category_id)
    {
        $categoryChilds = array_pluck($this->category->getByParentIgnoreStatus($category_id)->toArray(), 'id');

        foreach ($categoryChilds as $categoryChildId)
        {
            $categoryChilds = array_merge($categoryChilds, array_pluck($this->category->getByParentIgnoreStatus($categoryChildId)->toArray(), 'id'));
        }

        return $categoryChilds;
    }

    private function remove_imgfile($dir, $filename = ''){
        if($filename && file_exists($dir . $filename)) File::delete($dir . $filename);
    }

    // https://www.solutionspacenet.com/post/how-to-convert-images-to-webp-files-using-php
    private function create_webpimg($dir, $filename){
        if(preg_match('/[.]([pP][nN][gG]|[jJ][pP][gG]|[jJ][pP][eE][gG])$/i', $filename, $match) && file_exists($dir . $filename)){
            $filepath = $dir . $filename;
            $img = (strtolower($match[0]) === '.png' ? imagecreatefrompng($filepath) : imagecreatefromjpeg($filepath));
            imagewebp($img, preg_replace('/[.]([pP][nN][gG]|[jJ][pP][gG]|[jJ][pP][eE][gG])$/i', '.webp', $filepath), 83);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $category_id
     * @return Response
     */
    public function anyUpdate($category_id) {

        $input = Input::all();
        $UpdateStatusSubCat = false;

        if(!$this->category->fill($input)->isValid()) return Redirect::back()->withInput()->withErrors($this->category->errors);

        // Image
        $cat            = Category::getCategory($category_id);
        $old_filename   = $cat->category_img;
        $old_web_banner = $cat->category_web_banner;
        $old_filename_banner = $cat->category_img_banner;

        // YH: clean up the bugged code that cause overwrite issue
        $imgFilename    = "";
        if (Input::hasFile('image')) {
            $image  = Input::file('image');
            $imgFilename = $category_id . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('images/category/thumbs', $imgFilename);
            $this->create_webpimg('./images/category/thumbs/', $imgFilename);
            $this->remove_imgfile('./images/category/thumbs/', $old_filename);
        } else {
            if (Input::get('current_image') == "") {
                $this->remove_imgfile('./images/category/thumbs/', $old_filename);
            } else {
                $imgFilename = Input::get('current_image');
            }
        }
        // END Image

        //image banner
        $imgFilenamebanner    = "";
        if (Input::hasFile('image_banner')) {
            $image  = Input::file('image_banner');
            $imgFilenamebanner = $category_id . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('images/category/', $imgFilenamebanner);
            $this->create_webpimg('./images/category/', $imgFilenamebanner);
            $this->remove_imgfile('./images/category/', $old_filename_banner);
        } else {
            if (Input::get('current_image_banner') == "") {
                $this->remove_imgfile('./images/category/', $old_filename_banner);
            } else {
                $imgFilenamebanner = Input::get('current_image_banner');
            }
        }
        // END Image banner

        // Web Image banner
        $imgWebbanner    = "";
        if (Input::hasFile('web_banner')) {
            $image  = Input::file('web_banner');
            $imgWebbanner = $category_id . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('images/category/', $imgWebbanner);
            $this->create_webpimg('./images/category/web/', $imgWebbanner);
            $this->remove_imgfile('./images/category/web/', $old_web_banner);
        } else {
            if (Input::get('current_image_banner') == "") {
                $this->remove_imgfile('./images/category/web/', $old_web_banner);
            } else {
                $imgWebbanner = Input::get('current_image_banner');
            }
        }
        // END Web Image banner

        $category = $this->category->find($category_id);
        
        if($category->status != Input::get('status')) $UpdateStatusSubCat = true;
        
        $category->category_name        = Input::get('category_name');
        $category->category_name_cn     = Input::get('category_name_cn');
        $category->category_name_my     = Input::get('category_name_my');
        $category->category_descriptions= Input::get('category_descriptions');
        $category->category_parent      = Input::get('category_parent');
        $category->status               = Input::get('status');
        $category->permission           = Input::get('permission');
        $category->weight               = Input::get('weight');
        $category->charity              = Input::get('charity');
        $category->category_img         = $imgFilename;
        $category->category_img_banner  = $imgFilenamebanner;
        $category->category_web_banner  = $imgWebbanner;
        $category->modify_by            = Session::get('username');
        $category->save();

        

        $insert_audit = General::audit_trail('CategoryController.php', 'update()', 'Edit Category', Session::get('username'), 'CMS');

        if (Input::has('sublist')) {
            foreach(Input::get('sublist') as $key => $value) {
                $subCats = $this->category->find(trim(Input::get("sublist.$key")));
                if($subCats) {
                    if($UpdateStatusSubCat){
                        $subCats->status    = Input::get('status');
                    }
                    $subCats->permission    = Input::get('permission');
                    $subCats->modify_by     = Session::get('username');
                    
                    $subCats->save();
                }
            }
        }

        Session::flash('message', 'Successfully updated.');
        return Redirect::to('product/category');
    }

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $category_id
     * @return Response
     */
    public function anyDelete($category_id) {

        $category = $this->category->find($category_id);
        $category->modify_by = Session::get('username');
        $category->status = 2;
        $category->save();

        $insert_audit = General::audit_trail('CategoryController.php', 'delete()', 'Delete Category', Session::get('username'), 'CMS');

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('product/category');
    }

    private function arrangeCategories(array $categories, $firstLevelPrefix = '', $secondLevelPrefix = '', $thirdLevelPrefix = '')
    {
        foreach ($categories as $key => $category)
        {
            if ($category['category_parent'] == 0 && $category['id'] != 0)
            {
                $greatgrandparent = $category['id'];
                $arranged[] = $category;
                unset($categories[$key]);

                foreach ($categories as $key => $category)
                {
                    if ($category['category_parent'] == $greatgrandparent)
                    {
                        $grandparent = $category['id'];
                        $category['category_name'] = $firstLevelPrefix.$category['category_name'];
                        $arranged[] = $category;
                        unset($categories[$key]);

                        foreach ($categories as $key => $category)
                        {
                            if ($category['category_parent'] == $grandparent)
                            {
                                $parent = $category['id'];
                                $category['category_name'] = $secondLevelPrefix.$category['category_name'];
                                $arranged[] = $category;
                                unset($categories[$key]);

                                foreach ($categories as $key => $category)
                                {
                                    if ($category['category_parent'] == $parent)
                                    {
                                        $category['category_name'] = $thirdLevelPrefix.$category['category_name'];
                                        $arranged[] = $category;
                                        unset($categories[$key]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arranged;
    }


    /**
     * Add or edit charity info
     *
     * @param  int  $
     * @return Response
     */
    public function anyCharityinfo($category_id)
    {
        $id = "";
        $success = "";

        $data['name'] = Input::has('name') ? Input::get('name') : "";
        $data['contactno'] = Input::has('contactno') ? Input::get('contactno') : "";
        $data['address1'] = Input::has('address1') ? Input::get('address1') : "";
        $data['address2'] = Input::has('address2') ? Input::get('address2') : "";
        $data['postcode'] = Input::has('postcode') ? Input::get('postcode') : "";
        $data['country'] = Input::has('country') ? Input::get('country') : "";
        $data['state'] = Input::has('state') ? Input::get('state') : "";
        $data['city'] = Input::has('city') ? Input::get('city') : "";
        $data['specialmsg'] = Input::has('specialmsg') ? Input::get('specialmsg') : "";

        // update existing
        if (Input::has('id'))
        {
            $data['modify_by'] = Session::get('username');
            $data['modify_date'] = date("Y-m-d H:i:s");

            DB::table('jocom_charity_category')->where('id', Input::get('id'))->update($data);
            $id = Input::get('id');
            $success = "Charity info updated successfully";
        }
        elseif (Input::has('name'))
        {
            $data['category_id'] = $category_id;
            $data['insert_by'] = Session::get('username');
            $data['insert_date'] = date("Y-m-d H:i:s");

            $id = DB::table('jocom_charity_category')->insertGetId($data);            
            $success = "Charity info added successfully";
        }

        if ($id != "")
        {
            if (Input::hasFile("image2"))
            {
                $data['image2'] = Input::file("image2");

                $validator = Validator::make($data, CharityCategory::$rules);

                if ($validator->passes())
                {
                    $filePath = Config::get('constants.CHARITY_FILE_PATH');
                    $fileExt = $data['image2']->getClientOriginalExtension();
                    $fileName = $id . "p." . $fileExt;

                    // delete existing file
                    if (file_exists($filePath . $fileName))
                        unlink($filePath . $fileName);

                    $upload_file_succ = $data['image2']->move($filePath, $fileName);

                    if(isset($upload_file_succ))
                    {
                        unset($data);
                        $data['img_phone'] = $fileName;
                        DB::table('jocom_charity_category')->where('id', $id)->update($data);
                    }                    
                }                    
                else
                {
                    return Redirect::back()
                                ->withInput()
                                ->with('message', 'File type error!');
                }                
            }
            elseif (Input::get("del_phone") == "" AND Input::get("img_phone") != "")
            {
                // delete existing file
                if (file_exists(Config::get('constants.CHARITY_FILE_PATH') . Input::get("img_phone")))
                    unlink(Config::get('constants.CHARITY_FILE_PATH') . Input::get("img_phone"));

                unset($data);
                $data['img_phone'] = $fileName;
                DB::table('jocom_charity_category')->where('id', $id)->update($data);
            }


            if (Input::hasFile("image3"))
            {
                $data['image3'] = Input::file("image3");

                $validator = Validator::make($data, CharityCategory::$rules);

                if ($validator->passes())
                {
                    $filePath = Config::get('constants.CHARITY_FILE_PATH');
                    $fileExt = $data['image3']->getClientOriginalExtension();
                    $fileName = $id . "t." . $fileExt;

                    // delete existing file
                    if (file_exists($filePath . $fileName))
                        unlink($filePath . $fileName);                    

                    $upload_file_succ = $data['image3']->move($filePath, $fileName);

                    if(isset($upload_file_succ))
                    {
                        unset($data);
                        $data['img_tablet'] = $fileName;
                        DB::table('jocom_charity_category')->where('id', $id)->update($data);
                    }
                }                    
                else
                {
                    return Redirect::back()
                                ->withInput()
                                ->with('message', 'File type error!');
                }                
            }
            elseif (Input::get("del_tablet") == "" AND Input::get("img_tablet") != "")
            {
                // delete existing file
                if (file_exists(Config::get('constants.CHARITY_FILE_PATH') . Input::get("img_tablet")))
                    unlink(Config::get('constants.CHARITY_FILE_PATH') . Input::get("img_tablet"));

                unset($data);
                $data['img_tablet'] = $fileName;
                DB::table('jocom_charity_category')->where('id', $id)->update($data);
            }
        }
        return Redirect::to('product/category/edit/'.$category_id)->with('success', $success);
    }

    /**
     * Delete charity info
     *
     * @param  int  $
     * @return Response
     */
    public function anyDeletecharity($charity_id)
    {
        DB::table('jocom_charity_product')->where('charity_id', $charity_id)->delete();

        $charity = DB::table('jocom_charity_category')->where('id', $charity_id)->first();

        if (count($charity) > 0)
        {
            if ($charity->img_phone != "")
                unlink(Config::get('constants.CHARITY_FILE_PATH') . $charity->img_phone);

            if ($charity->img_tablet != "")
                unlink(Config::get('constants.CHARITY_FILE_PATH') . $charity->img_tablet);

            DB::table('jocom_charity_category')->where('id', $charity_id)->delete();

            return Redirect::to('product/category/edit/'.$charity->category_id)->with('success', "Charity info deleted!");
        }
        else
        {
            return Redirect::back()->with('message', 'No record or invalid ID!');
        }
    }
    public function anyDeletecategory($category_id){

        $category = $this->category->find($category_id); //id
        $category->modify_by = Session::get('username'); //modify by
        $category->status = 2; //status
        $category->save();

        $insert_audit = General::audit_trail('CategoryController.php', 'delete()', 'Delete Category', Session::get('username'), 'CMS');

        if ($category->status = 2) {
            $cat = DB::table('jocom_products_category')
                ->select('category_parent', 'category_name', 'status')
                ->where('category_parent', '=', $category_id)
                ->update(['status' => 2]);
        }

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('product/category/'); 
        
    }

}