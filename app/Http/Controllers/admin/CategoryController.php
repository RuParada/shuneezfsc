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
use App\Category;
use App\Language;
use View;

class CategoryController extends Controller {

	public function __construct(Guard $auth, Category $category, Language $language)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->category = $category;
	    $this->language = $language;
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	
	/*********** Get Categories ***************/
	
	public function getcategories()
	{
		$categories = $this->category->getcategories();
		return view('admin/categories', array('categories' => $categories));
	}
	
	/*********** Add Category Form *******************/
	
	public function addcategory_form()
	{
		$languages = $this->language->getlanguages();
		return view('admin/addcategory', array('languages' => $languages));
	}
	
	/************* Insert Category *******************/
	
	Public function addcategory()
	{
		$categories = Input::get('category');
		$valid = Validator::make(Input::all(),
								 ['image' => 'required|mimes:jpg,jpeg,png']);
		$array_valid = $this->category->rules($categories);
		if($array_valid['error_count'])
		{
			return redirect('admin/addcategory')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/addcategory')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('categories')->where('category_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->category->category_key = $key;
			$this->category->status = Input::get('status');
			$this->category->created_by = Session('admin_userid');
			$this->category->created_at = date('Y-m-d H:i:s');
			
			if(Input::file('image') != '')
			{
				$dest = 'assets/uploads/categories';
				$image = Input::file('image')->getClientOriginalName();
				Input::file('image')->move($dest,$image);
				$this->category->image = $image;
			}
			
			$this->category->save();
			$category_id = $this->category->id;
			$categories = Input::get('category');
			$language = Input::get('language');
			if(count($categories) > 0)
			{
				for($i=0; $i<count($categories); $i++)
				{
					DB::table('category_description')->insert(['category_id' => $category_id, 'category_name' => $categories[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/categories')->with('success', trans('messages.Category Add'));
		}			
	}
	
	/*************** Get Category *********************/
	
	public function getcategory($id)
	{
		$languages = $this->language->getlanguages();
		$category = DB::table('categories')->where('id', $id)->first();
		
		return view('admin/editcategory', array('languages' => $languages, 'category' => $category));
	}
	
	/************* Update Category *******************/
	
	Public function updatecategory()
	{
		$category_id = Input::get('id');
		$valid = Validator::make(Input::all(),
								 ['image' => 'mimes:jpg,jpeg,png']);
		if($valid->fails())
		{
			return redirect('admin/editcategory/'.$category_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->category->status = Input::get('status');
			$this->category->updated_by = Session('admin_userid');
			
			if(Input::file('image') != '')
			{
				$dest = 'assets/uploads/categories';
				$category = DB::table('categories')->where('id', $category_id)->first();
				if(file_exists($dest.'/'.$category->image))
				{
					unlink($dest.'/'.$category->image);
				}
				$image = Input::file('image')->getClientOriginalName();
				Input::file('image')->move($dest,$image);
				$this->category->image = $image;
			}
			
			DB::table('categories')->where('id', $category_id)->update($this->category['attributes']);
			
			DB::table('category_description')->where('category_id', $category_id)->delete();
			$categories = Input::get('category');
			$language = Input::get('language');
			if(count($categories) > 0)
			{
				for($i=0; $i<count($categories); $i++)
				{
					DB::table('category_description')->insert(['category_id' => $category_id, 'category_name' => $categories[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/categories')->with('success', trans('messages.Category Update'));
		}			
	}
	
	/**********Update Category Status************/

	public function change_categorystatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('categories')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}
	
	/*********** Filter Categories ***************/
	
	public function filtercategories()
	{
		$category = Input::get('name');
		$status = Input::get('status');
		
		$categories = DB::table('categories')
						->join('category_description', 'categories.id', '=', 'category_description.category_id')
						->SelectRaw($this->prefix.'categories.*,'.$this->prefix.'category_description.category_name as category')
						->where('category_description.language', $this->current_language)
						->where(function($query) use($category, $status)
						{
							if($category != '')
							{
								$query->where('category_description.category_name', 'like', '%'.$category.'%');
							}
							if($status != '')
							{
								if($status == 'deleted')
								{
									$query->where('categories.is_delete', 1);
								}
								else
								{
									$query->where('categories.status', $status);
								}
							}
							else
							{
								$query->where('categories.is_delete', 0);
							}
						})
						->paginate(10);
						 
		return view('admin/categories', array('categories' => $categories));
	}
	
	/************* Delete Category ***************/
	
	public function deletecategory($id)
	{
		DB::table('categories')->where('id', $id)->update(['is_delete' => 1]);
		DB::table('subcategories')->where('category_id', $id)->update(['is_delete' => 1]);
		DB::table('vendor_items')->where('category_id', $id)->update(['is_delete' => 1]);
		return redirect('admin/categories')->with('success', trans('messages.Category Delete'));
	}
	
	/************* Restore Deleted Category ***************/
	
	public function restorecategory($id)
	{
		DB::table('categories')->where('id', $id)->update(['is_delete' => 0]);
		DB::table('subcategories')->where('category_id', $id)->update(['is_delete' => 0]);
		DB::table('vendor_items')->where('category_id', $id)->update(['is_delete' => 0]);
		return redirect('admin/categories')->with('success', trans('messages.Category Restore'));
	}
	

}
