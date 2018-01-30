<?php namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Validator;
use Input;
use DB;
use Response;
use Mail;
use Session;
use App\Language;
use View;

class CmsController extends Controller {

	public function __construct(Guard $auth, Language $language)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->language = $language;
	}
	
	public function getpages()
	{
		$pages = DB::table('cms')->orderby('id', 'desc')->paginate(10);
		return view('admin/cms', array('pages' => $pages));
	}
	
	public function addpage_form()
	{
		return view('admin/addcms');
	}
	
	public function addpage()
	{
		$valid = Validator::make(Input::all(),
                                        ['title' => 'required|unique:cms,title',
                                         'description' => 'required',
                                         'order' => 'integer'
                                        ]);
		if($valid->fails())
		{
			return redirect('admin/addcms')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			DB::table('cms')->insert(['title' => Input::get('title'), 'description' => Input::get('description'), 'order' => Input::get('order'), 'meta_title' => Input::get('meta_title'), 'meta_keyword' => Input::get('meta_keyword'), 'meta_description' => Input::get('meta_description'), 'status' => Input::get('status'), 'created_at' => date('Y-m-d H:i:s')]);
			return redirect('admin/cms')->with('success', 'New page added successfully...');
		}
	}
	
	public function getpage($id)
	{
		$page = DB::table('cms')->where('id', $id)->first();
                return view('admin/editcms', array('page' => $page));
	}
	
	public function updatepage()
	{
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
                                        ['title' => 'required|unique:cms,title,'.$id,
                                         'description' => 'required',
                                         'order' => 'integer'
                                        ]);
		if($valid->fails())
		{
			return redirect('admin/getcms/'.$id)->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			DB::table('cms')->update(['title' => Input::get('title'), 'description' => Input::get('description'), 'order' => Input::get('order'), 'meta_title' => Input::get('meta_title'), 'meta_keyword' => Input::get('meta_keyword'), 'meta_description' => Input::get('meta_description'), 'status' => Input::get('status'), 'created_at' => date('Y-m-d H:i:s')]);
			return redirect('admin/cms')->with('success', 'Page details updated successfully...');
		}
	}
	
	public function change_pagestatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('cms')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => 'Status changed successfully...');
		return json_encode($result);
	}
	
	public function deletepage($id)
	{
		DB::table('cms')->where('id', $id)->delete();
		return redirect('admin/cms')->with('success', 'Page deleted successfully...');
	}
	
	public function filterpages()
	{
		$search = Input::get('name');
		$status = Input::get('status');
		
		$pages = DB::table('cms')
                            ->where(function($query) use($search, $status)
                            {
                                    if($search != '')
                                    {
                                            $query->where('title', 'like', '%'.$search.'%');
                                    }
                                    if($status != '')
                                    {
                                            $query->where('status', $status);
                                    }
                            })
                            ->orderby('id', 'desc')
                            ->paginate(10);
				
		return view('admin/cms', array('pages' => $pages));
	}

}
