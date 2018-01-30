<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Validator;
use Input;
use DB;
use Mail;
use URL;
use App\User;
use App\Order;
use File;
use Hash;
use PHPMailer;
use Session;
use Redirect;
use View;
use Cart;
use Auth;
use App\Language;
use App\Category;
use App\Vendoritem;
use App\Branch;
use App\Execlusion;
use stdClass;

class ApiController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Guard $auth, User $user, Language $language, Branch $branch, Order $order, Category $category, Vendoritem $vendoritem, Execlusion $execlusion)
	{
		$this->auth = $auth;
		$this->user = $user;
		$this->order = $order;
		$this->language = $language;
		$this->category = $category;
		$this->branch = $branch;
		$this->execlusion = $execlusion;
		$settings = DB::table('settings')->get();
	    $languages = $this->language->getlanguages();
	    $this->vendoritem = $vendoritem;
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		$this->config_data = $config_data;
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (Input::get('language') != '') ? Input::get('language') : 'en';
		$_SESSION['language'] = (Input::get('language') != '') ? Input::get('language') : 'en';
		$this->currency = getdefault_currency(); 
	}
	 
	public function signup()
	{
		$valid = Validator::make(Input::all(),
								['first_name' => 'required',
								'last_name' => 'required',
								'email' => 'required|email|unique:users,email',
								'password' => 'required|min:5',
								'mobile' => 'required|numeric|min:10|unique:users,mobile',
								]);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('users')->where('customer_key', $key)->count();
			if ($key_exits) { goto Random; }

			$otp = mt_rand(100000,999999);
			
			$this->user->customer_key = $key;			
			$this->user->first_name = Input::get('first_name');
			$this->user->last_name = Input::get('last_name');
			$this->user->email = Input::get('email');
			$this->user->password = bcrypt(Input::get('password'));
			$this->user->mobile = Input::get('mobile');
			$this->user->gcm_id = Input::get('gcm_id');
			$this->user->device_id = Input::get('device_id');
			$this->user->device_type = Input::get('device_type');
			$this->user->latitude = Input::get('latitude');
			$this->user->longitude = Input::get('longitude');
			$this->user->status = 1;
			$this->user->otp = $otp;
			$this->user->save();
			
			$email = Input::get('email');
			$name = Input::get('first_name').' '.Input::get('last_name');
			$pwd = Input::get('password');
			$url = URL::to('/verification/'.$key);
			
			$msg = "Hello ".$name.",<br><br>You have registered successfully.<br><br> You can find Your credentials below: <br>Username: ".$email."<br>Password: ".$pwd." <br><br> Please verify your account using the below link ".$url."<br> Thank You,<br>The Shuneez Team";
			$subject = "The Shunnez Registration";
			$this->sendmail($email, $subject, $msg);

			$mobile = valid_mobile(Input::get('mobile'));
			$subject = 'OTP - Shuneez';
			$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
			sendSMS(2, $mobile, $msg);

			$response = array('status' => 'success', 
							  'msg' => trans('frontend.Signup_Success'), 'user_id' => $this->user->id, 'otp' => $otp);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}

	public function resendotp_register()
	{
		$valid = Validator::make(Input::all(), ['user_id' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$user_id = Input::get('user_id');
			$otp = mt_rand(100000,999999);
			
			$user = DB::table('users')->where('id', $user_id)->first();
			DB::table('users')->where('id', $user_id)->update(['otp' => $otp]);
			
			$mobile = valid_mobile($user->mobile);
			
			$subject = 'OTP - Shuneez';
			$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
			//$this->sendmail($email, $subject, $msg);
			//$msg = 'Hi '.$name. ', Please find your order confirmation otp'.$otp.' Shuneez Team'; 
			sendSMS(2, $mobile, $msg);
			
			$response = array('status' => "success", 'msg' => trans('frontend.OTP send successfully'), 'user_id' => $user_id, 'otp' => $otp);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function verifyotp_register()
	{
		$valid = Validator::make(Input::all(), ['user_id' => 'required', 'otp' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$otp = Input::get('otp');
			$user_id = Input::get('user_id');
			
			$verify = DB::table('users')->where('id', $user_id)->where('otp', $otp)->first();
			if(count($verify))
			{
				DB::table('users')->where('id', $user_id)->where('otp', $otp)->update(['verify' => 1]);
				
				$response = array('status' => "success", 'msg' => trans('frontend.Verified successfully'), 'user' => $verify);
				$result = array('response' => $response);
				return json_encode($result);
			}
			else
			{
				$response = array('status' => "failure", 'msg' => trans('frontend.Invalid verification code'));
				$result = array('response' => $response);
				return json_encode($result);
			}
			
		}
			
	}
	
	public function verifyaccount($key)
	{
		$user = DB::table('users')->where('customer_key', $key)->first();
		if($user->verify == 0)
		{
			DB::table('users')->where('customer_key', $key)->update(['verify' => 1]);
			$response = array('status' => 'success', 
							  'msg' => trans('frontend.Verification_success'),
							  'user' => $user);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$response = array('status' => 'failure', 
							  'msg' => trans('frontend.Verification_failure')
							  );
			$result = array('response' => $response);
			return json_encode($result);
		}
		
	}
	
	public function login()
	{
		$valid = Validator::make(Input::all(),
		                            ['email' => 'required',
									 'password' => 'required']
									);
									
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors, 'verify' => '');
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$email = Input::get('email');
			$password = Input::get('password');
			if($this->auth->attempt(['email' => $email, 'password' => $password]))
			{
				$user = DB::table('users')->where('id', $this->auth->id())->first();
				if($user->is_delete == 1)
				{
					$this->auth->logout();
					$response = array('status' => 'failure', 'msg' => trans('frontend.Your account was deleted by admin. Please contact our admin'), 'verify' => '');
					$result = array('response' => $response);
					return json_encode($result);
				}
				if($user->verify == 0)
				{
					$user_id = $user->id;
					$otp = mt_rand(100000,999999);
					
					DB::table('users')->where('id', $user_id)->update(['otp' => $otp]);
					
					$mobile = valid_mobile($user->mobile);
					
					$subject = 'OTP - Shuneez';
					$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
					sendSMS(2, $mobile, $msg);
					$this->auth->logout();
					$response = array('status' => 'failure', 'msg' => trans('frontend.Your account has not been verified'), 'verify' => 0, 'otp' => $otp, 'user_id' => $user_id);
					$result = array('response' => $response);
					return json_encode($result);
				}
				elseif($user->status == 1)
				{
					$this->user->gcm_id = Input::get('gcm_id');
					$this->user->device_id = Input::get('device_id');
					$this->user->device_type = Input::get('device_type');
					$this->user->latitude = Input::get('latitude');
					$this->user->longitude = Input::get('longitude');
					DB::table('users')->where('id', $user->id)->update($this->user['attributes']);
					$response = array('status' => 'success', 'msg' => trans('frontend.Logged in successfully'), 'user' => $user, 'verify' => 1);
					$result = array('response' => $response);
					return json_encode($result);
			    }
			    else
			    {
			       $this->auth->logout();
			       $response = array('status' => 'failure', 'msg' => trans('frontend.You are blocked'), 'verify' => '');
				   $result = array('response' => $response);
				   return json_encode($result);
				}
			}
			else
			{
				$response = array('status' => 'failure', 'msg' => trans('frontend.Invalid Username or Password'), 'verify' => '');
			    $result = array('response' => $response);
			    return json_encode($result);
			}
		}
	}
	
	public function getcategory_list()
	{
		$valid = Validator::make(Input::all(),
		                            ['delivery_type' => 'required',
									 'branch_id' => 'required']
									);
									
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			/*$date = getdate();
			$day = strtolower($date['weekday']);
			$time = date('H:i:s');
			$latitude = Input::get('latitude');
			$longitude = Input::get('longitude');*/
			$delivery_type = Input::get('delivery_type');
			$branch_id = Input::get('branch_id');
			
			/*$branch = DB::select('SELECT sh_branches.*,sh_branch_description.branch_name as branch,
					   ( 3959 * acos( cos( radians('.$latitude.') ) * cos( radians( sh_branches.latitude ) ) 
					   * cos( radians(sh_branches.longitude) - radians('.$longitude.')) + sin(radians('.$latitude.')) 
					   * sin( radians(sh_branches.latitude)))) <= 10 AS distance
					FROM sh_branches 
					LEFT JOIN sh_branch_description ON sh_branches.id = sh_branch_description.branch_id
					LEFT JOIN sh_vendor_timeslot ON sh_branches.id = sh_vendor_timeslot.branch_id    
					WHERE
					sh_branches.status = 1 
					AND sh_branches.is_delete = 0
					AND sh_branch_description.language = "'.$this->current_language.'"
					AND sh_vendor_timeslot.working_day = "'.$day.'"
					AND sh_vendor_timeslot.start_time <= "'.$time.'"
					AND sh_vendor_timeslot.close_time >= "'.$time.'"
					AND (sh_branches.delivery_type = "'.$delivery_type.'" OR sh_branches.delivery_type = "b") 
					HAVING distance
					ORDER BY distance LIMIT 0,1;');*/
			$branch = $this->branch->getsearchbranch($branch_id, $delivery_type);
			//print_r($branch); exit;
			$service = ($delivery_type == 'd') ? 'delivery' : 'pickup';
			if(count($branch) == 0) {
				$response = array('status' => 'failure', 'msg' => trans('frontend.Sorry, this restaurants is not providing '.$service));
				$result = array('response' => $response);
				return json_encode($result);
			}
			else
			{
				$branch->image = URL::to('assets/uploads/branches/'.$branch->image);
				$categories = $this->category->getcategories();
				if(count($categories))
				{
					$i = 0;
					foreach($categories as $category)
					{
						$item[$i]['category'] = DB::table('categories')
												->join('category_description', 'categories.id', '=', 'category_description.category_id')
												->SelectRaw(DB::getTablePrefix().'categories.*,'.DB::getTablePrefix().'category_description.category_name as category')
												->where('category_description.language', $this->current_language)
												->where('categories.id', $category->id)
												->first();
						$item[$i]['category']->image = URL::to('assets/uploads/categories/'.$item[$i]['category']->image); 
						$item[$i]['items'] = DB::table('vendor_items')
											 ->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
											 ->SelectRaw($this->prefix.'vendor_items.*,'.$this->prefix.'vendor_item_description.item_name,'.$this->prefix.'vendor_item_description.item_description')
											 ->where('vendor_items.category_id', $category->id)
											 ->where('vendor_items.is_delete', 0)
											 ->where('vendor_items.status', 1)
											 ->where('vendor_item_description.language', $this->current_language)
											 ->orderby('vendor_items.sort_number', 'asc')
										 	 ->get();
						foreach($item[$i]['items'] as $row)
						{
							$row->image = URL::to('assets/uploads/vendor_items/'.$row->image);
							$row->ingredients = DB::table('vendor_item_ingredients')
											->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
											->SelectRaw($this->prefix.'vendor_item_ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name')
											->where('vendor_item_ingredients.item_id', $row->id)
											->where('ingredient_description.language', $this->current_language)
											->get();
							foreach($row->ingredients as $data)
							{
								$data->ingredienlists = DB::table('vendor_item_ingredientlist')
														->join('ingredientlist_description', 'vendor_item_ingredientlist.item_ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
														->SelectRaw(DB::getTablePrefix().'vendor_item_ingredientlist.item_ingredient_id as ingredient_id,'.DB::getTablePrefix().'vendor_item_ingredientlist.price,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_name,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_id')
														->where('vendor_item_ingredientlist.item_ingredient_id', $data->id)
														->where('ingredientlist_description.language', $this->current_language)
														->get();;
							}
							$row->execlusions = $this->execlusion->getVendorExeclusions($row->id);
							$row->size_list = DB::table('item_size')
											->join('item_size_description', 'item_size.id', '=', 'item_size_description.item_size_id')
											->select('item_size.*', 'size_name as size')
											->where('item_size.item_id', $row->id)
											->where('language', $this->current_language)
											->get();
						}
						
						$i++;
					}
				}
				
				$response = array('status' => 'success',
								  'branch' => $branch,
								  'items' => $item,
								  'service_tax' => $this->config_data['service_tax'],
								  'vat' => $this->config_data['vat'],
								  'delivery_time' => $this->config_data['delivery_time'],
								  'pickup_time' => $this->config_data['pickup_time'],);
				$result = array('response' => $response);
				return json_encode($result);
			}
		}
	}
	
	public function getitem()
	{
		$valid = Validator::make(Input::all(),
		                            ['item_id' => 'required']);
									
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{ 
			$item_id = Input::get('item_id');
			$item = $this->vendoritem->getvendoritem($item_id);
			//echo '<pre>'; print_r($item); echo '</pre>'; exit;

		    $item->image = URL::to('assets/uploads/vendor_items/'.$item->image);
		    
		    $ingredients = DB::table('vendor_item_ingredients')
						->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw($this->prefix.'vendor_item_ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name')
						->where('vendor_item_ingredients.item_id', $item_id)
						->where('ingredient_description.language', $this->current_language)
						->get();
			$data = [];
			if(count($ingredients))
			{
				$i = 0;
				foreach($ingredients as $ingredient)
				{
					$data[$i]['ingredients'] = DB::table('vendor_item_ingredients')
												->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
												->SelectRaw($this->prefix.'vendor_item_ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name')
												->where('vendor_item_ingredients.id', $ingredient->id)
												->where('ingredient_description.language', $this->current_language)
												->first();
					$data[$i]['ingredients']->ingredientlists = DB::table('vendor_item_ingredientlist')
										->join('ingredientlist_description', 'vendor_item_ingredientlist.item_ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
										->SelectRaw(DB::getTablePrefix().'vendor_item_ingredientlist.item_ingredient_id as ingredient_id,'.DB::getTablePrefix().'vendor_item_ingredientlist.price,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_name,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_id')
										->where('vendor_item_ingredientlist.item_ingredient_id', $ingredient->id)
										->where('ingredientlist_description.language', $this->current_language)
										->get();
					$i++;
				}
			}
		   
		   $response = array('status' => 'success','item' => $item);
		   $result = array('response' => $response);
		   return json_encode($result);
	   }
	}
	
	/* View User Profile */
	public function getuser()
	{
		$valid = Validator::make(Input::all(),
		                            ['user_id' => 'required']);
									
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{ 
			$user_id = Input::get('user_id');			
			$user = DB::table('users')->where('id', $user_id)->first();
			$response = array('status' => 'success', 'user' => $user);
			$result = array('response' => $response);
			return json_encode($result);
		}
		
	}
	
	/* Update User Profile Details */
	public function updateuser()
	{
		$id = Input::get('user_id');

        $valid = Validator::make(Input::all(), ['first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'mobile' => 'required|numeric|unique:users,mobile,' . $id]);

        if ($valid->fails())
        {
			$errors = $this->modifyErrorStructure($valid->errors());
            $response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
        } 
        else 
        {
            $this->user->first_name = Input::get('first_name');
            $this->user->last_name = Input::get('last_name');
            $this->user->email = Input::get('email');
            $this->user->mobile = Input::get('mobile');

            DB::table('users')->where('id', $id)->update($this->user['attributes']);
            $user = DB::table('users')->where('id', $id)->first();

            $response = array('status' => "success", 
            	'msg'=> trans('messages.User details updated successfully'), 
            	'user' => $user);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function getingredients()
	{
		$id = Input::get('item_id');
		$ingredients = DB::table('vendor_item_ingredients')
						->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw($this->prefix.'vendor_item_ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name')
						->where('vendor_item_ingredients.item_id', $id)
						->where('ingredient_description.language', $this->current_language)
						->get();
		$data = [];
		if(count($ingredients))
		{
			$i = 0;
			foreach($ingredients as $ingredient)
			{
				$data[$i]['ingredients'] = DB::table('vendor_item_ingredients')
											->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
											->SelectRaw($this->prefix.'vendor_item_ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name')
											->where('vendor_item_ingredients.id', $ingredient->id)
											->where('ingredient_description.language', $this->current_language)
											->first();
				$data[$i]['ingredients']->ingredientlists = DB::table('vendor_item_ingredientlist')
									->join('ingredientlist_description', 'vendor_item_ingredientlist.item_ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
									->SelectRaw(DB::getTablePrefix().'vendor_item_ingredientlist.item_ingredient_id as ingredient_id,'.DB::getTablePrefix().'vendor_item_ingredientlist.price,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_name,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_id')
									->where('vendor_item_ingredientlist.item_ingredient_id', $ingredient->id)
									->where('ingredientlist_description.language', $this->current_language)
									->get();
				$data[$i]['execlusions'] = $this->execlusion->getVendorExeclusions($id);
				$data[$i]['size_list'] = DB::table('item_size')
								->join('item_size_description', 'item_size.id', '=', 'item_size_description.item_size_id')
								->select('item_size.*', 'size_name as size')
								->where('item_size.item_id', $id)
								->where('language', $this->current_language)
								->get();
				$i++;
			}
		}
		
	
		$response = array('status' => 'success', 'data' => $data);
		$result = array('response' => $response);
		return json_encode($result);
	  
	}
	
	public function get_addressbooks()
	{
		$valid = Validator::make(Input::all(),
		                            ['user_id' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$customer_id = Input::get('user_id');
			$addressbooks = DB::table('user_addressbook')->where('customer_id', $customer_id)->where('is_delete', 0)->get();
        	
			$response = array('status' => 'success', 'addressbooks' => $addressbooks);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function add_addressbook()
	{
		$valid = Validator::make(Input::all(),
		                            ['user_id' => 'required',
		                             'address' => 'required',
		                             'latitude' => 'required',
		                             'longitude' => 'required']
									);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$address_key = str_random(16);				
			$customer_id = Input::get('user_id');
			$defalut = 0;
			$user_exits = DB::table('user_addressbook')->where('customer_id', $customer_id)->count();
        	if($user_exits == 0)
        	{
        		$defalut = 1;
        	}
			DB::table('user_addressbook')->insert(['address_key' => $address_key, 'customer_id' => $customer_id, 'address' => Input::get('address'), 'default_address' => $defalut, 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude'), 'created_at' => date('Y-m-d H:i:s')]);
			
			$response = array('status' => 'success', 'msg' => trans('frontend.Address Added Successfully'));
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	
	public function get_addressbook()
	{
		$valid = Validator::make(Input::all(),
		                            ['address_id' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$address_id = Input::get('address_id');
			$addressbook = DB::table('user_addressbook')->where('id', $address_id)->first();
        	
			$response = array('status' => 'success', 'addressbook' => $addressbook);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function update_addressbook()
	{
		$valid = Validator::make(Input::all(),
		                            ['address_id' => 'required',
		                             'address' => 'required',
		                             'latitude' => 'required',
		                             'longitude' => 'required',
		                             ]
									);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$address_id = Input::get('address_id');
			
			DB::table('user_addressbook')->where('id', $address_id)->update(['address' => Input::get('address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
			
			$response = array('status' => 'success', 'msg' => trans('frontend.Address details updated successfully'));
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function delete_address()
	{
		$valid = Validator::make(Input::all(),
		                            ['address_id' => 'required']
									);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
		   $address_id = Input::get('address_id');
		   $address = DB::table('user_addressbook')->where('id', $address_id)->first();
		   $orders = DB::table('orders')->where('address_key', $address->address_key)->count();
		   if($orders)
		   {
			   DB::table('user_addressbook')->where('id', $address_id)->update(['is_delete' => 1]);
		   }
		   else
		   {
		       DB::table('user_addressbook')->where('id', $address_id)->delete();
		   }
	        
	        $response = array('status' => 'success', 'msg' => trans('frontend.Address book deleted successfully'));
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function changedefault_address()
	{
		$valid = Validator::make(Input::all(),
		                            ['address_id' => 'required',
		                             'user_id' => 'required']
									);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$address_id = Input::get('address_id');
			$user_id = Input::get('user_id');
			
			DB::table('user_addressbook')->where('customer_id', $user_id)->where('default_address', 1)->update(['default_address' => 0]);
			DB::table('user_addressbook')->where('id', $address_id)->update(['default_address' => 1]);
			
			$response = array('status' => 'success', 'msg' => trans('frontend.Default address changed successfully'));
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function getdatetime()
	{
		$date = date('d-m-Y');
		$time = date('H:i');
		$response = array('status' => 'success', 'date' => $date, 'time' => $time);
		$result = array('response' => $response);
		return json_encode($result);
	}
	
	public function verify_branch_availability()
	{
		$valid = Validator::make(Input::all(),
		                            ['branch_id' => 'required',
		                             'date' => 'required',
		                             'time' => 'required']
									);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$branch_id = Input::get('branch_id');
			$date = date('Y-m-d', strtotime(Input::get('date')));
			$delivery_time = Input::get('time');
			$day = strtolower(date('l', strtotime($date)));
				
			$valid_time = DB::table('vendor_timeslot')
							->where('branch_id', $branch_id)
							->where('working_day', $day)
							->where('start_time', '<', $delivery_time)
							->where('close_time', '>', $delivery_time)
							->count();
			if($valid_time)
			{
				$response = array('status' => 'success', 'available' => 1);
				$result = array('response' => $response);
				return json_encode($result);
			}
			else
			{
				$response = array('status' => 'failure', 'available' => 0, 'msg' => trans('frontend.Please select another delivery time'));
				$result = array('response' => $response);
				return json_encode($result);
			}
		}
	}
	
	public function myorders()
	{
		$valid = Validator::make(Input::all(),['user_id' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => 'failure', 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$user_id = Input::get('user_id');
			$orders = DB::table('orders')->where('customer_id', $user_id)->get();
			$data = [];
			if(count($orders))
			{
				$i = 0;
				foreach($orders as $order)
				{
					$data[$i]['order'] = DB::table('orders')->select('id','invoice_number','created_at as order_date','order_status','order_total')->where('id', $order->id)->first();
					$data[$i]['order']->order_date = date('M d,Y, g:iA', strtotime($data[$i]['order']->order_date));
					$items = DB::table('order_itemdetails')
							->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
							->select('vendor_item_description.item_name')
							->where('order_itemdetails.order_id', $order->id)
							->where('vendor_item_description.language', $this->current_language)
							->get();
					$item = '';
					for($j=0; $j<count($items); $j++)
					{
						$comma = ($j == 0) ? '' : ',';
						$item .= $comma.$items[$j]->item_name;
					}
					$data[$i]['items'] = $item;
					$data[$i]['order']->order_status_code = $data[$i]['order']->order_status;
					if($data[$i]['order']->order_status == 'p')
					{
						$data[$i]['order']->order_status = trans('frontend.Pending');
					}
					elseif($data[$i]['order']->order_status == 'as')
					{
						$data[$i]['order']->order_status = trans('frontend.Assigned');
					}
					elseif($data[$i]['order']->order_status == 'd')
					{
						$data[$i]['order']->order_status = trans('frontend.Delivered');
					}
					elseif($data[$i]['order']->order_status == 'ca')
					{
						$data[$i]['order']->order_status = trans('frontend.Cancelled');
					}
					elseif($data[$i]['order']->order_status == 'c')
					{
						$data[$i]['order']->order_status = trans('frontend.Confirmed');
					}
					elseif($data[$i]['order']->order_status == 'o')
					{
						$data[$i]['order']->order_status = trans('frontend.Out for delivery');
					}
					elseif($data[$i]['order']->order_status == 'a')
					{
						$data[$i]['order']->order_status = trans('frontend.Accepted');
					}

					$i++;
				}
			}
			
			$response = array('status' => 'success', 'orders' => $data);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}

	/* Change Password */

	public function changepassword()
	{
		$id = Input::get('user_id');

		$valid = Validator::make(Input::all(), ['current_password' => 'required',
                    'new_password' => 'required',
                    'confirm_password' => 'required|same:new_password']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($errors);
           $response = array('status' => "failure", 'msg' => $valid->errors());
		   $result = array('response' => $response);
		   return json_encode($result);
        }
        else 
        {
        	$current_password = Input::get('current_password');
            $new_password = Input::get('new_password');
            $confirm_password = Input::get('confirm_password');

            $user = DB::table('users')->where('id', $id)->first();

            if (Hash::check($current_password, $user->password))
            {
            	DB::table('users')->where('id', $id)->update(['password' => bcrypt($new_password)]);

            	$response = array('status' => "success", 'msg' => trans("frontend.Password changed successfully"));
				$result = array('response' => $response);
				return json_encode($result);
            }
            else
            {
            	$response = array('status' => "failure", 'msg' => trans("frontend.Current password doesn't match"));
				$result = array('response' => $response);
				return json_encode($result);

            } 
         
        }

	}

	/* Forget Password */

	public function forgetpassword()
	{
		$email_id = Input::get('email');
		$valid = Validator::make(Input::all(), ['email' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('status' => "failure", 'msg' => $errors);
		   $result = array('response' => $response);
		   return json_encode($result);
        }
        else
        {
        	$user = DB::table('users')->where('email', $email_id)->first();
        	if(count($user))
        	{
        	   $new_password = str_random(6);
        	   DB::table('users')->where('email', $email_id)->update(['password' => bcrypt($new_password)]);
        	   $subject = "Forget Password - Shuneez";
        	   $msg = "Hi ".$user->first_name." ".$user->last_name.", Kindly find your new password ".$new_password;
        	   $this->sendmail($email_id, $subject, $msg);
        	   $response = array('status' => "success", 'msg' => trans("frontend.Kindly check your mail to retrieve your password"));
			   $result = array('response' => $response);
			   return json_encode($result);
        	}
        	else
        	{
        	   $response = array('status' => "success", 'msg' => trans("frontend.Invalid email id"));
			   $result = array('response' => $response);
			   return json_encode($result);
        	}


        }
	}
	
	public function getorder()
	{
		$valid = Validator::make(Input::all(), ['order_id' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('status' => "failure", 'msg' => $errors);
		   $result = array('response' => $response);
		   return json_encode($result);
        }
        else
        {
			$order_id = Input::get('order_id');
			$order['details'] = DB::table('orders')
								->join('branches', 'orders.branch_id', '=', 'branches.id')
								->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
								->select('orders.sub_total','orders.order_total','orders.service_tax', 'orders.vat', 'orders.delivery_fee', 'orders.invoice_number', 'orders.delivery_type', 'orders.order_status', 'branch_description.branch_name as branch', 'branches.mobile')
								->where('orders.id', $order_id)
								->first();
			$order['details']->delivery_type = ($order['details']->delivery_type == 'd') ? trans('frontend.Delivery') : trans('frontend.Pickup');
			if($order['details']->order_status == 'p')
			{
				$order['details']->order_status = trans('frontend.Pending');
			}
			elseif($order['details']->order_status == 'as')
			{
				$order['details']->order_status = trans('frontend.Assigned');
			}
			elseif($order['details']->order_status == 'a')
			{
				$order['details']->order_status = trans('frontend.Accepted');
			}
			elseif($order['details']->order_status == 'd')
			{
				$order['details']->order_status = trans('frontend.Delivered');
			}
			elseif($order['details']->order_status == 'ca')
			{
				$order['details']->order_status = trans('frontend.Cancelled');
			}
			elseif($order['details']->order_status == 'c')
			{
				$order['details']->order_status = trans('frontend.Confirmed');
			}
			elseif($order['details']->order_status == 'o')
			{
				$order['details']->order_status = trans('frontend.Out for delivery');
			}
			$order['details']->service_tax_percentage = $this->config_data['service_tax'];
			$order['details']->vat_tax_percentage = $this->config_data['vat']; 
			$items = DB::table('order_itemdetails')
						->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
						->select('order_itemdetails.item_id', 'vendor_item_description.item_name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients', 'is_execlusion', 'is_size')
						->where('order_itemdetails.order_id', $order_id)
						->where('vendor_item_description.language', $this->current_language)
						->get();
		  $i = 0;
		  foreach($items as $item)
		  {
			$order['items'][$i] = DB::table('order_itemdetails')
								->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
								->select('vendor_item_description.item_name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients')
								->where('order_itemdetails.item_id', $item->item_id)
								->where('vendor_item_description.language', $this->current_language)
								->where('order_itemdetails.order_id', $order_id)
								->first();
			if($item->is_size == 1)
			{
				$order['items'][$i]->size = DB::table('order_item_sizedetails')
												->join('item_size_description', 'order_item_sizedetails.size_id', '=', 'item_size_description.item_size_id')
												->select('order_item_sizedetails.price as size_price', 'item_size_description.size_name')
												->where('order_item_sizedetails.item_id', $item->item_id)
												->where('item_size_description.language', $this->current_language)
												->where('order_item_sizedetails.order_id', $order_id)
												->first();
			}
			else
			{
				$order['items'][$i]->size = [];
			}
			if($item->is_ingredients == 1)
			{
				$order['items'][$i]->ingredients = DB::table('order_ingredientdetails')
												->join('ingredientlist_description', 'order_ingredientdetails.ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
												->select('order_ingredientdetails.price as ingredient_price', 'ingredientlist_description.ingredientlist_name as ingredient_name')
												->where('order_ingredientdetails.item_id', $item->item_id)
												->where('ingredientlist_description.language', $this->current_language)
												->where('order_ingredientdetails.order_id', $order_id)
												->get();
			}
			else
			{
				$order['items'][$i]->ingredients = [];
			}
			if($item->is_execlusion == 1)
			{
				$order['items'][$i]->execlusions = DB::table('order_execlusion_details')
												->join('execlusion_description', 'order_execlusion_details.execlusion_id', '=', 'execlusion_description.execlusion_id')
												->select('execlusion_description.execlusion_name')
												->where('order_execlusion_details.item_id', $item->item_id)
												->where('execlusion_description.language', $this->current_language)
												->where('order_execlusion_details.order_id', $order_id)
												->get();
			}
			else
			{
				$order['items'][$i]->execlusions = [];
			}
			$i++;
		  }

		  $response = array('status' => "success", 'order' => $order);
		  $result = array('response' => $response);
		  return json_encode($result);
		}
	}
	
	public function customer_info()
	{
		$address_id = Input::get('address_id');
		$user_id = Input::get('user_id');
		if($address_id == '' && $user_id == '')
		{
			$valid = Validator::make(Input::all(),
										['first_name' => 'required',
										 'last_name' => 'required',
										 'email' => 'required|email|unique:users,email',
										 'mobile' => 'required|numeric|unique:users,mobile',
										 'address' => 'required',
										 'latitude' => 'required',
										 'longitude' => 'required',
										]);
		}
		elseif($address_id != '' && $user_id == '')
		{
			$valid = Validator::make(Input::all(),
										['first_name' => 'required',
										 'last_name' => 'required',
										 'email' => 'required|email|unique:users,email',
										 'mobile' => 'required|numeric|unique:users,mobile',
										]);
		}
		elseif($address_id == '' && $user_id != '')
		{
			$valid = Validator::make(Input::all(),
										['address' => 'required',
										 'latitude' => 'required',
										 'longitude' => 'required',]);
		}
		elseif($address_id != '' && $user_id != '')
		{
			$valid = Validator::make(Input::all(),
										['address_id' => 'required',
										 'user_id' => 'required',
										]);
		}
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			
				Random :
				$key = str_random(16);
				$address_key = str_random(16);
				if($user_id == '')
				{
					$key_exits = DB::table('users')->where('customer_key', $key)->count();
					if ($key_exits) { goto Random; }
					$password = str_random(6);
					$this->user->customer_key = $key;
					$this->user->first_name = Input::get('first_name');
					$this->user->last_name = Input::get('last_name');
					$this->user->email = Input::get('email');
					$this->user->mobile = Input::get('mobile');
					$this->user->status = 1;
					$this->user->password = bcrypt($password);				
					
					$this->user->save();
					
					$email = Input::get('email');
					$name = Input::get('first_name').' '.Input::get('last_name');
					$pwd = $password;
					$url = URL::to('/verification/'.$key);
					
					$msg = "Hello ".$name.",<br><br>You have registered successfully.<br><br> You can find Your credentials below: <br>Username: ".$email."<br>Password: ".$pwd." <br><br> Please verify your account using the below link ".$url."<br> Thank You,<br>The Shuneez Team";
					$subject = "The Shunnez Registration";
					$this->sendmail($email, $subject, $msg);
					
					$user_id = $this->user->id;
				}
				
				if($address_id == '')
				{
					$address_key = str_random(16);
					
					$key_exits = DB::table('user_addressbook')->where('address_key', $address_key)->count();
					if ($key_exits) { goto Random; }
					
					DB::table('user_addressbook')->insert(['address_key' => $address_key, 'customer_id' => $user_id, 'address' => Input::get('address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
					$address_id = DB::getPdo()->lastInsertId();
				}
				
				$response = array('status' => "success", 'user_id' => $user_id, 'address_id' => $address_id);
				$result = array('response' => $response);
				return json_encode($result);
			}
	}
	
	public function payment()
	{
		$cart = file_get_contents("php://input"); 
		$cart = json_decode($cart, true); //print_r($cart); exit;
		if(!empty($cart))
		{
			$payment_type = Input::get('payment_type');
			
			$user_id = Input::get('user_id');
			$address_id = Input::get('address_id'); 
			
			$user = DB::table('users')->where('id', $user_id)->first();
			$address = DB::table('user_addressbook')->where('id', $address_id)->first();
			
			$service_tax = Input::get('service_tax'); 
			$vat_tax = Input::get('vat_tax'); 
			$subtotal = Input::get('subtotal');
			$total = Input::get('total');

			$delivery_date = (Input::get('delivery_date') != '') ? date('Y-m-d H:i:s', strtotime(Input::get('delivery_date').Input::get('delivery_time'))) : date('Y-m-d H:i:s');

			InvoiceRandom:
			$invoice = mt_rand(100000,999999);

			OrderRandom:
			$order_key = mt_rand(100000,999999);

			$invoice_exits = DB::table('orders')->where('invoice_number', $invoice)->count();
			if ($invoice_exits) { goto InvoiceRandom; }

			$order_key_exits = DB::table('orders')->where('order_key', $order_key)->count();
			if ($order_key_exits) { goto OrderRandom; }

			$otp = mt_rand(100000,999999);
			
			$this->order->order_key = $order_key;
			$this->order->sub_total = $subtotal;
			$this->order->service_tax = $service_tax;
			$this->order->vat_percentage = $this->config_data['vat'];
			$this->order->vat = $vat_tax;
			$this->order->service_tax_percentage = $this->config_data['service_tax'];
			$this->order->order_total = $total;
			$this->order->customer_key = $user->customer_key;
			$this->order->customer_id = $user->id;
			$this->order->customer_first_name = $user->first_name;
			$this->order->customer_last_name = $user->last_name;
			$this->order->customer_email = $user->email;
			$this->order->customer_mobile = $user->mobile;
			$this->order->branch_id = Input::get('branch_id');
			$this->order->delivery_type = Input::get('delivery_type');
			$this->order->order_datetime = 	(Input::get('delivery_date') != '') ? date('Y-m-d H:i:s', strtotime(Input::get('delivery_date').Input::get('delivery_time'))) : date('Y-m-d H:i:s');
			$this->order->delivery_fee = 	(Input::get('delivery_type') == 'd') ? Input::get('delivery_fee') : 0;											
			$this->order->address_key = $address->address_key;
			$this->order->address_id = $address_id;
			$this->order->otp = $otp;
			$this->order->invoice_number = $invoice;
			$this->order->order_status = 'p';
			$this->order->created_by = 'm';
			
			$this->order->save();
			$order_id = $this->order->id;
			$products = [];
			
			$items = $cart['cart_items']['items']; //echo '<pre>'; print_r($items); exit;
			$overall_price = 0;
			foreach($items as $item)
			{
				$item_price = DB::table('vendor_items')->where('id', $item['item_id'])->first();
				DB::table('order_itemdetails')->insert(['order_id' => $order_id, 'item_id' => $item['item_id'], 'quantity' => $item['quantity'], 'price' => $item_price->price, 'is_ingredients' => $item['is_ingredients'], 'is_size' => $item['is_size'], 'is_execlusion' => $item['is_execlusion']]);

				$order_item_id = DB::getPdo()->lastInsertId();
				//echo count($item['ingredients']);
				$ingredient_total = 0;
				if(count($item['ingredients']))
				{
					for($i=0; $i<count($item['ingredients']); $i++)
					{
						$ingredient = DB::table('vendor_item_ingredientlist')->where('item_id', $item['item_id'])->where('item_ingredientlist_id', $item['ingredients'][$i])->first();
						DB::table('order_ingredientdetails')->insert(['order_itemdetails_id' => $order_item_id, 'order_id' => $order_id, 'item_id' => $item['item_id'], 'ingredientlist_id' => $item['ingredients'][$i], 'price' => $ingredient->price]);
						$ingredient_total += $ingredient->price * $item['quantity'];
					}
				}
				
				if($item['is_size'] == 1)
				{
					$size = DB::table('item_size')->where('id', $item['size_id'])->first();
					DB::table('order_item_sizedetails')->insert(['order_item_id' => $order_item_id, 'order_id' => $order_id, 'item_id' => $item['item_id'], 'size_id' => $item['size_id'], 'price' => $size->price]);
				}

				if($item['is_execlusion'] == 1)
				{
					for($i=0; $i<count($item['execlusions']); $i++)
					{
						DB::table('order_execlusion_details')->insert(['item_details_id' => $order_item_id, 'order_id' => $order_id, 'item_id' => $item['item_id'], 'execlusion_id' => $item['execlusions'][$i]]);
					}
				}

				$foodicsitem = DB::table('vendor_items')->select('foodics_id')->where('id', $item['item_id'])->first();
				if($foodicsitem->foodics_id != '')
				{
					$item_size = DB::table('item_size')->select('foodics_id')->where('id', $item['size_id'])->first();
					$notes = 'no notes';
					$price = ($item['is_size'] == 1) ? $item_price->price + $size->price : $item_price->price;
					$final_price = ($item['is_ingredients']) ? ($price * $item['quantity']) + $ingredient_total : ($price * $item['quantity']);
					$overall_price += $final_price;
					$options = [];
					$execlusions = [];
					if($item['is_ingredients'])
					{
						for($i=0; $i<count($item['ingredients']); $i++)
						{
							$ingredientlist = DB::table('vendor_item_ingredientlist')->select('foodics_id', 'price')->where('item_id',  $item['item_id'])->where('item_ingredientlist_id',  $item['ingredients'][$i])->first();
							$ingredient_final_price = $ingredientlist->price * $item['quantity'];
							$options[] = array
											(
												'hid' => $ingredientlist->foodics_id,
												'original_price' => $ingredientlist->price,
												'quantity' => $item['quantity'],
												'final_price' => $ingredient_final_price	
											);
						}
					}
					if($item['is_execlusion'])
					{
						for($i=0; $i<count($item['execlusions']); $i++)
						{
							$execlusion = DB::table('execlusions')->select('foodics_id')->where('id', $item['execlusions'][$i])->first();
							$execlusions[] = array(
													'hid' => $execlusion->foodics_id
												);
						}
					}
					//echo "<pre>"; print_r($options); exit;
					$products[] = array
									(
										'notes' => $notes,
										'original_price' => $price,
										'product_hid' => $foodicsitem->foodics_id,
										'product_size_hid' => $item_size->foodics_id,
										'quantity' => $item['quantity'],
										'removed_ingredients' => $execlusions,
										'final_price' => $final_price,
										'options' => $options
									);
				}
			}
				//$foodics_delivery_fee = (Input::get('delivery_type') == 'd') ? Input::get('delivery_fee') : 0;
				$foodics_delivery_fee = 0;
				if(count($products))
				{
					$vat_tax = (($overall_price * $this->config_data['vat']) / 100); 
					$total_price = $overall_price + $foodics_delivery_fee + $vat_tax;
					$type = (Input::get('delivery_type') == 'd') ? 4 : 3;
					$access_token = $this->config_data['foodics_access_token'];
					$delivery_address = [];
					if($type == 4)
					{
						$delivery_address = array(
							        "address" => $address->address,
							        "delivery_zone_hid" => '_96765a78',
							        "notes" => '',
							        "latitude" => $address->latitude,
							        "longitude" => $address->longitude
							        );
					}
					$header = array();
					$header[] = 'Authorization: Bearer '.$access_token;
					$header[] = 'Content-type: application/json; charset=utf-8';

					$data = array(
								'branch_hid' => '_ad722a47',
								"price" => $overall_price,
							    "delivery_price" => $foodics_delivery_fee,
							    "discount_amount" => 0,
							    "final_price" => $total_price,
							    "total_tax" => $vat_tax,
							    "due_time" => date('Y-m-d H:i:s', strtotime($delivery_date)),
							    "notes" => "",
							    "type" => $type,
							    "delivery_address" => $delivery_address,
							    "customer" => array(
							        "name" => $user->first_name.' '.$user->last_name,
							        "phone" => $user->mobile,
							        "country_code" => "SA"
							    ),
							    "products" => $products,
							    "taxes" => []
							);
					$fields = json_encode($data);
			//echo '<pre>'; print_r($fields); exit;
					$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url."/api/v2/orders");
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

					$response = curl_exec($ch);
					curl_close($ch);
					$details = json_decode($response);
				//echo '<pre>'; print_r($details); exit;
					$foodics_id = ( isset($details->order_hid) ) ? $details->order_hid : '';
					DB::table('orders')->where('id', $order_id)->update(['foodics_id' => $foodics_id]);
				}
			
			$name = $user->first_name.' '.$user->last_name;
			$email = $user->email;
			$subject = 'OTP - Shuneez';
			$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
			$mobile = valid_mobile($user->mobile);
			//echo $payment_type; exit;
			$success = 'Please verify your otp';
			sendSMS(2, $mobile, $msg);
			
			/*if($payment_type == 'cod')
			{
				$success = 'Please verify your otp';
				sendSMS(2, $mobile, $msg);
				//$this->sendmail($email, $subject, $msg);
			}
			else
			{
				$msg = 'Hi, Your order id - '.$invoice.' has been confirmed.';
				sendSMS(2, $mobile, $msg);
				$success = 'Proceed your order';
			}*/
			
			$response = array('status' => "success", 'msg' => $success, 'order_id' => $order_id, 'billing_email' => $user->email, 'billing_name' => $name, 'overalltotal' => $total, 'payment_type' => $payment_type);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$response = array('status' => "failure", 'msg' => trans('frontend.Unexpected Error'));
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function resendotp()
	{
		$valid = Validator::make(Input::all(), ['order_id' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$order_id = Input::get('order_id');
			$otp = mt_rand(100000,999999);
			
			$user = DB::table('orders')->where('id', $order_id)->first();
			DB::table('orders')->where('id', $order_id)->update(['otp' => $otp]);
			
			$name = $user->customer_first_name.' '.$user->customer_last_name;
			$email = $user->customer_email;
			$mobile = valid_mobile($user->customer_mobile);
			
			$subject = 'OTP - Shuneez';
			$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
			//$this->sendmail($email, $subject, $msg);
			//$msg = 'Hi '.$name. ', Please find your order confirmation otp'.$otp.' Shuneez Team'; 
			sendSMS(2, $mobile, $msg);
			
			$response = array('status' => "success", 'msg' => trans('frontend.OTP send successfully'), 'order_id' => $order_id);
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function verifyotp()
	{
		$valid = Validator::make(Input::all(), ['order_id' => 'required', 'otp' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$otp = Input::get('otp');
			$order_id = Input::get('order_id');
			
			$verify = DB::table('orders')->where('id', $order_id)->where('otp', $otp)->first();
			if(count($verify))
			{
				DB::table('orders')->where('id', $order_id)->where('otp', $otp)->update(['is_verified' => 1, 'order_status' => 'c', 'confirmed_at' => date('Y-m-d H:i:s')]);
				/*$mobile = valid_mobile($verify->customer_mobile);
				$msg = 'Hi, Your order id - '.$verify->invoice_number.' has been confirmed.';
				sendSMS(2, $mobile, $msg);*/
				$response = array('status' => "success", 'msg' => trans('frontend.Your order placed successfully'), 'order_id' => $order_id, 'total' => $verify->order_total, 'invoice' => $verify->invoice_number);
				$result = array('response' => $response);
				return json_encode($result);
			}
			else
			{
				$response = array('status' => "failure", 'msg' => trans('frontend.Invalid verification code'));
				$result = array('response' => $response);
				return json_encode($result);
			}
			
		}
			
	}
	
	public function payment_response()
	{
		$valid = Validator::make(Input::all(), ['order_id' => 'required', 'payment_response' => 'required', 'payment_status' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$order_id = Input::get('order_id');
			$payment_response = Input::get('payment_response');
			$payment_status = Input::get('payment_status');
			DB::table('orders')->where('id', $order_id)->update(['transaction_id' => $payment_response, 'payment_status' => $payment_status]);
			$order = DB::table('orders')->where('id', $order_id)->first();
			$response = array('status' => "success", 'msg' => trans('frontend.Your order placed successfully'), 'order_id' => $order_id, 'total_amount' => $order->order_total, 'invoice' => $order->invoice_number );
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function contact_us()
	{
		$valid = Validator::make(Input::all(), 
								['name' => 'required', 
								'email' => 'required|email', 
								'subject' => 'required',
								'message' => 'required',
								'mobile' => 'required|numeric']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$admin = DB::table('adminusers')->where('superadmin', 1)->first();
			$email = $admin->email;
			$subject = 'New Feedback - Shuneez';
			$msg = 'Hi admin, <br><br> We have received new feedback<br><br>Name : '.Input::get("name").'<br><br> Email : '.Input::get("email").'<br><br> Mobile : '.Input::get("mobile").'<br><br> Subject : '.Input::get("subject").'<br><br>Message : '.Input::get("message").'<br><br> Thanks & Regards <br><br> The Shuneez';
			$this->sendmail($email, $subject, $msg);
			$response = array('status' => "success", 'msg' => trans('frontend.Your details send successfully'));
			$result = array('response' => $response);
			return json_encode($result);
		}
	}
	
	public function promocode()
	{
		$valid = Validator::make(Input::all(), 
								['user_id' => 'required', 
								'promocode' => 'required', 
								'total' => 'required|numeric'
								]);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$promo_code = Input::get('promocode');
			$user_id = Input::get('user_id');
			$total = Input::get('total');
			$promocode = DB::table('promocodes')->where('promocode', $promo_code)->first();
			if(count($promocode))
			{
				$verify = DB::table('promocode_users')->where('promocode_id', $promocode->id)->where('customer_id', $user_id)->first();
				if(count($verify))
				{
					if($promocode->expiry_date < date('Y-m-d'))
					{
						$response = array('status' => "failure", 'msg' => trans('frontend.Promocode has been expired'));
						$result = array('response' => $response);
						return json_encode($result);
					}
					elseif($verify->is_used == 1)
					{
						$response = array('status' => "failure", 'msg' => trans('frontend.Promocode has been already used'));
						$result = array('response' => $response);
						return json_encode($result);
					}
					else
					{
						if($promocode->discount_type == 'a')
						{
							$discount_amount = $promocode->amount;
						}
						else
						{
							$discount_amount = ($promocode->amount / 100) * $total;
						}
							$total_amount = $total - $discount_amount;
							DB::table('promocode_users')->where('id', $verify->id)->update(['is_used' => 1]);
							$response = array('status' => "success", 'msg' => trans('frontend.Promocode has been applied successfully'), 'discount_amount' => $discount_amount, 'total_amount' => $total_amount);
							$result = array('response' => $response);
							return json_encode($result);
					}
				}
				else
				{
					$response = array('status' => "failure", 'msg' => trans('frontend.Invalid promocode'));
					$result = array('response' => $response);
					return json_encode($result);
				}	
			}
			else
			{
				$response = array('status' => "failure", 'msg' => trans('frontend.Invalid promocode'));
				$result = array('response' => $response);
				return json_encode($result);
			}
		}
	}
		
	
	public function sendmail($email, $subject, $msg)
	{
		include('mail/class.phpmailer.php');
		
		$mail = new PHPMailer(); // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->IsHTML(true);
		$mail->Username = "order@shuneezfsc.com";
		$mail->Password = "shuneez123";
		$mail->SetFrom("order@shuneezfsc.com");
		$mail->Subject = $subject;
		$mail->Body = $msg;
		$mail->AddAddress($email);
		$mail->Send();
		
		return 1;
	}
	
	public function getlanguages()
	{
		include(base_path()."/resources/lang/".$this->current_language."/api.php");
		//print_r($params); exit;
		$myfile = fopen(base_path()."/resources/lang/".$this->current_language."/api.php", "r") or die("Unable to open file!");
		$msg = fread($myfile,filesize(base_path()."/resources/lang/".$this->current_language."/api.php"));
		fclose($myfile);
		
		$response = array('status' => "success", 'languages' => $params);
		$result = array('response' => $response);
		return json_encode($result);
	}
	
	public function modifyErrorStructure($modelError) 
	{
		$temp[] = $modelError;
		$errors = '';
		$i=0;
		foreach ($modelError->toArray() as $key => $value) 
		{
			$comma = ($i == 0) ? '' : ' &'; 
			$errors .= $comma.' '.$value[0];
			$i++;
		}
		return $errors;
	}
	
	public function getbranches()
	{
		$date = getdate();
		$day = strtolower($date['weekday']);
		$time = date('H:i:s');
		$keyword = Input::get('keyword');

		$branchAvailables = DB::table('branches')
							->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
							->select('branches.id', 'branch_description.branch_name')
							->where(function($query) use($keyword)
								{
									if($keyword != '')
									{
										$query->where('branch_description.branch_name', 'like', '%'.$keyword.'%')
												->OrWhere('street', 'like', '%'.$keyword.'%')
												->OrWhere('street', 'city', '%'.$keyword.'%')
												->OrWhere('country', 'like', '%'.$keyword.'%')
												->OrWhere('meta_keywords', 'like', '%'.$keyword.'%');;
									}
								})
							->where('branch_description.language', $this->current_language)
							->get();
		if(count($branchAvailables))
		{
			$arraybranch = array();
			$arraymsg = array();
			foreach($branchAvailables as $branchAvailable) 
			{
				$branches = DB::table('branches')
							->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
							->join('vendor_timeslot', 'branches.id', '=', 'vendor_timeslot.branch_id')
							->select('branches.id','branch_description.branch_name as branch', DB::raw('CONCAT(street,",", city,",", country) AS address'))
							->where(function($query) use($keyword)
								{
									if($keyword != '')
									{
										$query->where('branch_description.branch_name', 'like', '%'.$keyword.'%')->OrWhere('street', 'like', '%'.$keyword.'%')->OrWhere('street', 'city', '%'.$keyword.'%')->OrWhere('country', 'like', '%'.$keyword.'%');
									}
								})
							->where('vendor_timeslot.working_day', $day)
							->where('vendor_timeslot.start_time', '<=', $time)
							->where('vendor_timeslot.close_time', '>=', $time)
							->where('branches.id', $branchAvailable->id)
							->where('branch_description.language', $this->current_language)
							->get();

				if(count($branches))
				{
					$arraybranch = $branches;
				}
				else
				{
					array_push($arraymsg,$branchAvailable->branch_name.' - '.trans('frontend.Closed now'));
				}
			}
			$response = array('status' => "success", 'branches' => $arraybranch, 'msg' =>$arraymsg,'closed_branches' =>$arraymsg);
		}
		else
		{
			$response = array('status' => "failure", 'branches' => [], 'msg' => trans('messages.No data found'),'error_msg' => trans('messages.No data found'));
		}
		
		$result = array('response' => $response);
		return json_encode($result);
	}
	
	public function trackorder()
	{
		$valid = Validator::make(Input::all(), 
								['order_id' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('status' => "failure", 'msg' => $errors);
			$result = array('response' => $response);
			return json_encode($result);
		}
		else
		{
			$data = DB::table('orders')
					->join('branches', 'orders.branch_id', '=', 'branches.id')
					->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
					->select('branches.latitude as pickup_latitude', 'branches.longitude as pickup_longitude', 'user_addressbook.latitude as delivery_latitude', 'user_addressbook.longitude as delivery_longitude')
					->where('orders.id', Input::get('order_id'))
					->first();
			$response = array('status' => "success", 'data' => $data);
			$result = array('response' => $response);
			return json_encode($result);
		}		
	}
	
	public function getorder_status()
	{
		$valid = Validator::make(Input::all(), ['order_id' => 'required']);
		if ($valid->fails()) 
        {
			$errors = $this->modifyErrorStructure($valid->errors());
            $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		    return json_encode($response);
        } 
        else 
        {
			$order = DB::table('orders')->select('order_status', 'created_at', DB::Raw('IFNULL(confirmed_at , "" ) as confirmed_at'), DB::Raw('IFNULL(delivered_at , "" ) as delivered_at'), DB::Raw('IFNULL(assigned_at , "" ) as assigned_at'), DB::Raw('IFNULL(accepted_at , "" ) as accepted_at'))->where('id', Input::get('order_id'))->first();
			if($order->order_status == 'p')
			{
				$order->order_status = 'Processing';
			}
			elseif($order->order_status == 'a' || $order->order_status == 'c' || $order->order_status == 'as')
			{
				$order->order_status = 'Confirmed';
			}
			elseif($order->order_status == 'd')
			{
				$order->order_status = 'Delivered';
			}
			elseif($order->order_status == 'o')
			{
				$order->order_status = 'Assigned';
			}
			$order->created_at = ($order->confirmed_at != '') ? date('d-M-Y g:i A', strtotime($order->confirmed_at)) : '';
			$order->confirmed_at = ($order->accepted_at != '') ? date('d-M-Y g:i A', strtotime($order->accepted_at)) : '';
			$order->delivered_at = ($order->delivered_at != '') ? date('d-M-Y g:i A', strtotime($order->delivered_at)) : '';
			$order->assigned_at = ($order->assigned_at != '') ? date('d-M-Y g:i A', strtotime($order->assigned_at)) : '';
			
			$response = array('status' => "success", 'order' => $order);
			$result = array('response' => $response);
			return json_encode($result);
		}		
	}

	public function addRating()
	{
		$valid = Validator::make(Input::all(), ['order_id' => 'required', 'rating' => 'required', 'review' => 'required']);
		if ($valid->fails()) 
        {
			$errors = $this->modifyErrorStructure($valid->errors());
            $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		    return json_encode($response);
        } 
        else 
        {
			$order_id = Input::get('order_id');
			$rating = Input::get('rating');
			$review = Input::get('review');

			$fields = ['count' => $rating];
			$fields = json_encode($fields);
				
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/FleetOwners/".$this->config_data['dook_fleet_owner_id']."/stars?access_token=".$this->config_data['dook_access_token']."");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);

			$result = json_decode($response);

			$rating_id = $result->id;

			$fields = ['text' => $review];
			$fields = json_encode($fields);
				
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Messages?access_token=".$this->config_data['dook_access_token']."");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);

			$result = json_decode($response);

			$review_id = $result->id;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://test-api.dook.sa/api/Messages/".$review_id."/stars/rel/".$rating_id."?access_token=".$this->config_data['dook_access_token']."");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);

			$result = json_decode($response);

			$relation_id = $result->id;

			DB::table('orders')->where('id', $order_id)->update(['deliveryboy_rating' => $rating, 'deliveryboy_review' => $review, 'dook_rating_id' => $relation_id]);

			$order = DB::table('orders')->where('id', $order_id)->first();
			$rating = DB::table('orders')->where('deliveryboy_id', $order->deliveryboy_id)->where('dook_rating_id', '!=', '')->avg('deliveryboy_rating');
			DB::table('deliveryboys')->where('id', $order->deliveryboy_id)->update(['rating' => $rating]);
			 $response = array('httpcode' => 200, 'status' => "success", 'message' => trans('api.Thanks for your review'), 'data' => new stdClass());
		    return json_encode($response);
		}
	}	

}

