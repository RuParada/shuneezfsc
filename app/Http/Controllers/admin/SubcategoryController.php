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
use App\Subcategory;
use App\Category;
use App\Language;
use View;
use Redirect;

class SubcategoryController extends Controller {

	public function __construct(Guard $auth, Subcategory $subcategory, Category $category, Language $language)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->subcategory = $subcategory;
	    $this->category = $category;
	    $this->language = $language;
		$this->prefix = DB::getTablePrefix();
	}
	
	/*********** Get Sub Categories ***************/
	
	public function getsubcategories()
	{
		$categories = $this->category->getcategories();
		$subcategories = $this->subcategory->getsubcategories();
						 
		return view('admin/subcategories', array('categories' => $categories, 'subcategories' => $subcategories));
	}
	
	/*********** Add Sub Category Form *******************/
	
	public function addsubcategory_form()
	{
		$languages = $this->language->getlanguages();
		$categories = $this->category->getcategories();
		return view('admin/addsubcategory', array('categories' => $categories, 'languages' => $languages));
	}
	
	/************* Insert Sub Category *******************/
	
	Public function addsubcategory()
	{
		$subcategories = Input::get('subcategory');
		$valid = Validator::make(Input::all(),
								 ['category' => 'required',
								 'image' => 'mimes:jpg,jpeg,png']);
		$array_valid = $this->subcategory->rules($subcategories);
		if($array_valid['error_count'])
		{
			return redirect('admin/addsubcategory')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/addsubcategory')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('subcategories')->where('subcategory_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->subcategory->subcategory_key = $key;
			$this->subcategory->category_id = Input::get('category');
			$this->subcategory->status = Input::get('status');
			$this->subcategory->created_by = Session('admin_userid');
			$this->subcategory->created_at = date('Y-m-d H:i:s');
			
			if(Input::file('image') != '')
			{
				$dest = 'assets/uploads/subcategories';
				$image = Input::file('image')->getClientOriginalName();
				Input::file('image')->move($dest,$image);
				$this->subcategory->image = $image;
			}
			
			$this->subcategory->save();
			$subcategory_id = $this->subcategory->id;
			$subcategories = Input::get('subcategory');
			$language = Input::get('language');
			if(count($subcategories) > 0)
			{
				for($i=0; $i<count($subcategories); $i++)
				{
					DB::table('subcategory_description')->insert(['subcategory_id' => $subcategory_id, 'subcategory_name' => $subcategories[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/subcategories')->with('success', trans('messages.Subcategory Add'));
		}			
	}
	
	/*************** Get Sub Category *********************/
	
	public function getsubcategory($id)
	{
		$languages = $this->language->getlanguages();
		$categories = $this->category->getcategories();
		$subcategory = DB::table('subcategories')->where('id', $id)->first(); 
		
		return view('admin/editsubcategory', array('languages' => $languages, 'subcategory' => $subcategory, 'categories' => $categories));
	}
	
	/************* Update Sub Category *******************/
	
	Public function updatesubcategory()
	{
		$subcategories = Input::get('subcategory');
		$subcategory_id = Input::get('id');
		$valid = Validator::make(Input::all(),
								 ['category' => 'required',
								 'image' => 'mimes:jpg,jpeg,png']);
		$array_valid = $this->subcategory->rules($subcategories);
		if($array_valid['error_count'])
		{
			return redirect('admin/editsubcategory/'.$subcategory_id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/editsubcategory/'.$subcategory_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->subcategory->category_id = Input::get('category');
			$this->subcategory->status = Input::get('status');
			$this->subcategory->updated_by = Session('admin_userid');
			
			if(Input::file('image') != '')
			{
				$dest = 'assets/uploads/subcategories';
				$subcategory = DB::table('subcategories')->where('id', $subcategory_id)->first();
				if(file_exists($dest.'/'.$subcategory->image))
				{
					unlink($dest.'/'.$subcategory->image);
				}
				$image = Input::file('image')->getClientOriginalName();
				Input::file('image')->move($dest,$subcategory->image);
				$this->category->image = $image;
			}
			
			DB::table('subcategories')->where('id', $subcategory_id)->update($this->subcategory['attributes']);
			
			DB::table('subcategory_description')->where('subcategory_id', $subcategory_id)->delete();
			$subcategories = Input::get('subcategory');
			$language = Input::get('language');
			if(count($subcategories) > 0)
			{
				for($i=0; $i<count($subcategories); $i++)
				{
					DB::table('subcategory_description')->insert(['subcategory_id' => $subcategory_id, 'subcategory_name' => $subcategories[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/subcategories')->with('success', trans('messages.Subcategory Update'));
		}			
	}
	
	/**********Update Sub Category Status************/

	public function change_subcategorystatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('subcategories')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}
	
	/*********** Filter Sub Categories ***************/
	
	public function filtersubcategories()
	{
		$subcategory = Input::get('name');
		$category = Input::get('category');
		$status = Input::get('status');
		
		$subcategories = DB::table('subcategories')
						->join('subcategory_description', 'subcategories.id', '=', 'subcategory_description.subcategory_id')
						->join('category_description', 'subcategories.category_id', '=', 'category_description.category_id')
						->SelectRaw($this->prefix.'subcategories.*,'.$this->prefix.'subcategory_description.subcategory_name as subcategory,'.$this->prefix.'category_description.category_name as category')
						->where('subcategory_description.language', 'en')
						->where('category_description.language', 'en')
						->where(function($query) use($subcategory, $category, $status)
						{
							if($subcategory != '')
							{
								$query->where('subcategory_description.subcategory_name', 'like', '%'.$subcategory.'%');
							}
							if($category != '')
							{
								$query->where('subcategories.category_id', $category);
							}
							if($status != '')
							{
								if($status == 'deleted')
								{
									$query->where('subcategories.is_delete', 1);
								}
								else
								{
									$query->where('subcategories.status', $status);
								}
							}
							else
							{
								$query->where('subcategories.is_delete', 0);
							}
						})
						->paginate(10);
		$categories = $this->category->getcategories();
						 
		return view('admin/subcategories', array('categories' => $categories, 'subcategories' => $subcategories));
	}
	
	/************* Delete Sub Category ***************/
	
	public function deletesubcategory($id)
	{
		DB::table('subcategories')->where('id', $id)->update(['is_delete' => 1]);
		DB::table('vendor_items')->where('category_id', $id)->update(['is_delete' => 1]);
		return redirect('admin/subcategories')->with('success', trans('messages.Subcategory Delete'));
	}
	
	/************* Restore Sub Deleted Category ***************/
	
	public function restoresubcategory($id)
	{
		$subcategory = DB::table('subcategories')->where('id', $id)->first();
		$category = DB::table('categories')->where('id', $subcategory->category_id)->first();
		if($category->is_delete == 0)
		{
			DB::table('subcategories')->where('id', $id)->update(['is_delete' => 0]);
			DB::table('vendor_items')->where('category_id', $id)->update(['is_delete' => 0]);
			return redirect('admin/subcategories')->with('success', trans('messages.Restore Category'));
		}
		else
		{
			return Redirect::back()->with('delete_error', trans('messages.Restore Subcategory'));
		}
	}
	
}
