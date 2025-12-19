<?php
 
class KeywordsController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function getAddressIndex()
    {
        return View::make('sysadmin.keywords.address-index');
    }
    
    public function getAddressData()
    {
        $keywords = DB::table('jocom_keywords')->select(['id', 'title', 'type']);
         return Datatables::of($keywords)
            ->addColumn('action', function ($item) {
                return '<div class="text-center">
                    <a href="/sysadmin/address-keywords-edit/'.$item->id.'" class="btn btn-warning sub" data-toggle="modal"><i class="fa fa-pencil"></i> Edit</a>
                </div>';
            })
            ->make(true);
    }

    public function createKeyword()
    {
        return View::make('sysadmin.keywords.address-create');
    }

    public function storeKeyword()
    {
        $keyword = Input::get('keyword');
        $type = Input::get('keyword_type');

        $validator = Validator::make(
            ['title' => Input::get('keyword')], ['title' => 'required|unique:jocom_keywords']
        );

        if ($validator->fails())
        {
            return Redirect::to('/sysadmin/address-keywords-create')->withErrors($validator);
        }

        $query = DB::table('jocom_keywords')->insert(
            ['title' => $keyword, 'type' => $type]
        );

        if($query)
        {
            return Redirect::back()->with('message', 'Keyword created successfully');
        }

    }

    public function editKeyword($id)
    {
        $keyword = DB::table('jocom_keywords')->find($id);
        return View::make('sysadmin.keywords.address-edit')->with(compact('keyword'));
    }

    public function updateKeyword($id)
    {
        $keyword = Input::get('keyword');
        $type = Input::get('keyword_type');

        if($id)
        {
            $query = DB::table('jocom_keywords')
                ->where('id', $id)
                ->update(['title' => $keyword, 'type' => $type]);

            return Redirect::back()->with('message', 'Keyword updated successfully');
        }
    }

    public function deleteKeyword()
    {
        if(Input::get('id'))
        {
            $query = DB::table('jocom_keywords')->where('id', '=', Input::get('id'))->delete();            
            return Redirect::to('/sysadmin/address-keywords')->with('message', 'Keyword Deleted');
        }
    }
}
