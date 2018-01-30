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
use App\Cuisine;
use App\Language;
use View;

class CuisineController extends Controller {

	public function __construct(Guard $auth, Cuisine $cuisine, Language $language)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->cuisine = $cuisine;
	    $this->language = $language;
		$this->prefix = DB::getTablePrefix();
	}
	
	/*********** Get cuisines ***************/
	
	public function getcuisines()
	{
		$cuisines = $this->cuisine->getcuisines();
						 
		return view('admin/cuisines', array('cuisines' => $cuisines));
	}
	
	/*********** Add Cuisine Form *******************/
	
	public function addcuisine_form()
	{
		$languages = $this->language->getlanguages();
		return view('admin/addcuisine', array('languages' => $languages));
	}
	
	/************* Insert Cuisine *******************/
	
	Public function addcuisine()
	{
		$cuisines = Input::get('cuisine');
		$valid = Validator::make(Input::all(),
								 ['status' => 'required']);
		$array_valid = $this->cuisine->rules($cuisines);
		if($array_valid['error_count'])
		{
			return redirect('admin/addcuisine')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/addcuisine')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('cuisines')->where('cuisine_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->cuisine->cuisine_key = $key;
			$this->cuisine->status = Input::get('status');
			$this->cuisine->created_by = Session('admin_userid');
			$this->cuisine->created_at = date('Y-m-d H:i:s');
			
			$this->cuisine->save();
			$cuisine_id = $this->cuisine->id;
			$cuisines = Input::get('cuisine');
			$language = Input::get('language');
			if(count($cuisines) > 0)
			{
				for($i=0; $i<count($cuisines); $i++)
				{
					DB::table('cuisine_description')->insert(['cuisine_id' => $cuisine_id, 'cuisine_name' => $cuisines[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/cuisines')->with('success', trans('messages.Cuisine Add'));
		}			
	}
	
	/*************** Get Cuisine *********************/
	
	public function getcuisine($id)
	{
		$languages = $this->language->getlanguages();
		$cuisine = DB::table('cuisines')->where('id', $id)->first();
		
		return view('admin/editcuisine', array('languages' => $languages, 'cuisine' => $cuisine));
	}
	
	/************* Update Cuisine *******************/
	
	Public function updatecuisine()
	{
		$cuisines = Input::get('cuisine');
		$cuisine_id = Input::get('id');
		$valid = Validator::make(Input::all(),
								 ['status' => 'required']);
		$array_valid = $this->cuisine->rules($cuisines);
		if($array_valid['error_count'])
		{
			return redirect('admin/editcuisine/'.$cuisine_id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/editcuisine/'.$cuisine_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->cuisine->status = Input::get('status');
			$this->cuisine->updated_by = Session('admin_userid');
			
			DB::table('cuisines')->where('id', $cuisine_id)->update($this->cuisine['attributes']);
			
			DB::table('cuisine_description')->where('cuisine_id', $cuisine_id)->delete();
			$cuisines = Input::get('cuisine');
			$language = Input::get('language');
			if(count($cuisines) > 0)
			{
				for($i=0; $i<count($cuisines); $i++)
				{
					DB::table('cuisine_description')->insert(['cuisine_id' => $cuisine_id, 'cuisine_name' => $cuisines[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/cuisines')->with('success', trans('messages.Cuisine Update'));
		}			
	}
	
	/**********Update Cuisine Status************/

	public function change_cuisinestatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('cuisines')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => 'Status changed successfully...');
		return json_encode($result);
	}
	
	/*********** Filter Cuisines ***************/
	
	public function filtercuisines()
	{
		$cuisine = Input::get('name');
		$status = Input::get('status');
		
		$cuisines = DB::table('cuisines')
						->join('cuisine_description', 'cuisines.id', '=', 'cuisine_description.cuisine_id')
						->SelectRaw($this->prefix.'cuisines.*,'.$this->prefix.'cuisine_description.cuisine_name as cuisine')
						->where('cuisine_description.language', 'en')
						->where(function($query) use($cuisine, $status)
						{
							if($cuisine != '')
							{
								$query->where('cuisine_description.cuisine_name', 'like', '%'.$cuisine.'%');
							}
							if($status != '')
							{
								$query->where('cuisines.status', $status);
							}
						})
						->paginate(10);
						 
		return view('admin/cuisines', array('cuisines' => $cuisines));
	}
	
	/************* Delete Cuisine ***************/
	
	public function deletecuisine($id)
	{
		$items = DB::table('vendor_items')->where('cuisine_id', $id)->count();
		if($items)
		{
			return redirect('admin/cuisines')->with('error', trans('messages.Delete Error'));
		}
		else
		{
			DB::table('cuisines')->where('id', $id)->delete();
			DB::table('cuisine_description')->where('cuisine_id', $id)->delete();
			return redirect('admin/cuisines')->with('success', trans('messages.Cuisine Delete'));
		}
	}

}
