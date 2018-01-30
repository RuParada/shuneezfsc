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
use App\Vendor;
use App\Language;
use App\Category;
use App\Cuisine;
use View;

class VendorController extends Controller {

	public function __construct(Guard $auth, Vendor $vendor, Language $language, Category $category, Cuisine $cuisine)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->vendor = $vendor;
	    $this->language = $language;
	    $this->category = $category;
	    $this->cuisine = $cuisine;
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		
	}
	
	/*********** Get Vendors***************/
	
	public function getvendors()
	{
		$vendors = $this->vendor->getvendors();
		return view('admin/vendors', array('vendors' => $vendors));
	}
	
	public function addvendor_form()
	{
		$languages = $this->language->getlanguages();
		$categories = $this->category->getcategories();
		$cuisines = $this->cuisine->getcuisines();
		
		return view('admin/addvendor', array('languages' => $languages, 'categories' => $categories, 'cuisines' => $cuisines));
		
	}
	
	public function addvendor()
	{
		
		$valid = Validator::make(Input::all(),
									['email' => 'required|unique:vendors,email',
									 'mobile' => 'required|unique:vendors,mobile|numeric',
									 'image' => 'required|mimes:jpeg,jpg,png',
									 'commission_percentage' => 'required',
									]);
		if($valid->fails())
		{
			return redirect('admin/addvendor')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('vendors')->where('vendor_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->vendor->vendor_key = $key;
			$this->vendor->email = Input::get('email');
			$this->vendor->mobile = Input::get('mobile');
			$this->vendor->password = base64_encode(Input::get('password'));
			$this->vendor->commission_percentage = Input::get('commission_percentage');
			$this->vendor->status = Input::get('status');
			$this->vendor->city = Input::get('city');
			$this->vendor->street = Input::get('street');
			$this->vendor->country = Input::get('country');
			$this->vendor->zipcode = Input::get('zipcode');
			$this->vendor->latitude = Input::get('latitude');
			$this->vendor->longitude = Input::get('longitude');
			$this->vendor->created_by = Session('admin_userid');
			
			$logo = str_random(6).Input::file('image')->getClientOriginalName();
			$dest = 'assets/uploads/vendors';
			Input::file('image')->move($dest,$logo);
			$this->vendor->image = $logo;
			
			$this->vendor->save();
			
			$vendor_id = $this->vendor->id;
			
			$languages = Input::get('language');
			$vendors = Input::get('vendor');
			$description = Input::get('description');
			$categories = Input::get('category_id');
			$cuisines = Input::get('cuisine_id');
			
			if(count($vendors) > 0)
			{
				for($i=0; $i<count($vendors); $i++)
				{
					DB::table('vendor_description')->insert(['vendor_id' => $vendor_id, 'vendor_name' => $vendors[$i], 'description' => $description[$i], 'language' => $languages[$i]]);
				}
			}
			
			if(count($categories) > 0)
			{
				for($i=0; $i<count($categories); $i++)
				{
					DB::table('vendor_categories')->insert(['vendor_id' => $vendor_id, 'category_id' => $categories[$i]]);
				}
			}
			
			if(count($cuisines) > 0)
			{
				for($i=0; $i<count($cuisines); $i++)
				{
					DB::table('vendor_cuisines')->insert(['vendor_id' => $vendor_id, 'cuisine_id' => $cuisines[$i]]);
				}
			}
			
			return redirect('admin/vendors')->with('success', 'New vendor added successfully');
		}
	}
	
	public function getvendor($id)
	{
		$vendor = DB::table('vendors')->where('id', $id)->first();
		$vendor_categories = DB::table('vendor_categories')->where('vendor_id', $id)->get();
		$vendor_cuisines = DB::table('vendor_cuisines')->where('vendor_id', $id)->get();
		$languages = $this->language->getlanguages();
		$categories = $this->category->getcategories();
		$cuisines = $this->cuisine->getcuisines();
		
		return view('admin/editvendor', array('vendor' => $vendor, 'vendor_categories' => $vendor_categories, 'vendor_cuisines' => $vendor_cuisines, 'languages' => $languages, 'categories' => $categories, 'cuisines' => $cuisines));
	}
	
	public function updatevendor()
	{
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
									['email' => 'required|unique:vendors,email,'.$id,
									 'mobile' => 'required|numeric|unique:vendors,mobile,'.$id,
									 'commission_percentage' => 'required',
									]);
		if($valid->fails())
		{
			return redirect('admin/editvendor/'.$id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->vendor->email = Input::get('email');
			$this->vendor->mobile = Input::get('mobile');
			$this->vendor->commission_percentage = Input::get('commission_percentage');
			$this->vendor->status = Input::get('status');
			$this->vendor->city = Input::get('city');
			$this->vendor->street = Input::get('street');
			$this->vendor->country = Input::get('country');
			$this->vendor->zipcode = Input::get('zipcode');
			$this->vendor->latitude = Input::get('latitude');
			$this->vendor->longitude = Input::get('longitude');
			$this->vendor->updated_by = Session('admin_userid');
			
			if(Input::file('image') != '')
			{
				$vendor = DB::table('vendors')->where('id', $id)->first();
				$logo = str_random(6).Input::file('image')->getClientOriginalName();
				$dest = 'assets/uploads/vendors';
				if(file_exists($dest.'/'.$vendor->image))
				{
					unlink($dest.'/'.$vendor->image);
				}
				Input::file('image')->move($dest,$logo);
				$this->vendor->image = $logo;
			}
			
			DB::table('vendors')->where('id', $id)->update($this->vendor['attributes']);
			
			$languages = Input::get('language');
			$vendors = Input::get('vendor');
			$description = Input::get('description');
			$categories = Input::get('category_id');
			$cuisines = Input::get('cuisine_id');
			
			DB::table('vendor_description')->where('vendor_id', $id)->delete();
			if(count($vendors) > 0)
			{
				for($i=0; $i<count($vendors); $i++)
				{
					DB::table('vendor_description')->insert(['vendor_id' => $id, 'vendor_name' => $vendors[$i], 'description' => $description[$i], 'language' => $languages[$i]]);
				}
			}
			
			DB::table('vendor_categories')->where('vendor_id', $id)->delete();
			if(count($categories) > 0)
			{
				for($i=0; $i<count($categories); $i++)
				{
					DB::table('vendor_categories')->insert(['vendor_id' => $id, 'category_id' => $categories[$i]]);
				}
			}
			
			DB::table('vendor_cuisines')->where('vendor_id', $id)->delete();
			if(count($cuisines) > 0)
			{
				for($i=0; $i<count($cuisines); $i++)
				{
					DB::table('vendor_cuisines')->insert(['vendor_id' => $id, 'cuisine_id' => $cuisines[$i]]);
				}
			}
			
			return redirect('admin/vendors')->with('success', 'Vendor details updated successfully');
		}
	}
	
	/**********Update Vendor Status************/

	public function change_vendorstatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('vendors')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => 'Status changed successfully...');
		return json_encode($result);
	}
	
	public function filtervendors()
	{
		$name = Input::get('name');
		$status = Input::get('status');
		
		$vendors = DB::table('vendors')
				->join('vendor_description', 'vendors.id', '=', 'vendor_description.vendor_id')
				->SelectRaw(DB::getTablePrefix().'vendors.*,'.DB::getTablePrefix().'vendor_description.vendor_name as vendor')
				->where('vendor_description.language', $this->current_language)
				->where(function($query) use($name, $status)
				{
					if($name != '')
					{
						$query->where('vendor_description.vendor_name', 'like', '%'.$name.'%');
					}
					if($status != '')
					{
						if($status == 'deleted')
						{
							$query->where('vendors.is_delete', 1);
						}
						else
						{
							$query->where('vendors.status', $status);
						}
					}
					else
					{
						$query->where('vendors.is_delete', 0);
					}
				})
				->paginate(10);
				
		return view('admin/vendors', array('vendors' => $vendors));
	}
	
	/************* Delete Vendor ***************/
	
	public function deletevendor($id)
	{
		DB::table('vendors')->where('id', $id)->update(['is_delete' => 1]);
		return redirect('admin/vendors')->with('success', 'Vendor deleted successfully');
	}
	
	/************* Restore Deleted Vendor ***************/
	
	public function restorevendor($id)
	{
		DB::table('vendors')->where('id', $id)->update(['is_delete' => 0]);
		return redirect('admin/vendors')->with('success', 'Vendor restored successfully');
	}

}
