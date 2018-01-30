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
use App\Addresstype;

class UserController extends Controller {

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
	public function __construct(Guard $auth, User $user, Language $language, Order $order, Addresstype $addresstype)
	{
		//$this->middleware('auth');
		$this->auth = $auth;
		$this->user = $user;
		$this->order = $order;
		$this->language = $language;
		$this->addresstype = $addresstype;
		$settings = DB::table('settings')->get();
	    $languages = $this->language->getlanguages();
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		
		$this->config_data = $config_data;
		$this->prefix = DB::getTablePrefix();
		View::share (['config_data'=> $config_data, 'languages' => $languages]);
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	 
    /**
	* Display the specified resource.
	* GET /user/{id}
	*
	* @param  int  $id  The id of a User
	* @return Response
	*/
	
	 public function logout()
	{
		$this->auth->logout();
		return redirect('/');
	}

    /** 
	* Function to display myprofile page of user
	**/
	public function myprofile()
	{
		return view('users/myprofile');
	}

   

	/* Display user profile details and perform edit option */
	public function profiledetails()
	{ 
		if (Auth::user())
		{
			$uid 		= Auth::user()->id;					
			$user 		= DB::table('users')->where('id', $uid)->first();
			$newsletter = DB::table('newsletter_subscribers')->where('user_id', $uid)->first();
			$subscribe 	= count($newsletter);
			return view('edit_profile',array('user' => $user, 'subscribe' => $subscribe));
		}
		
	}

	/* Update User Profile Details */
	public function updateuser()
	{
		$id 		= Input::get('id');
		$subscribe 	= Input::get('subscribe');
        $valid 		= Validator::make(Input::all(), ['first_name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                    'last_name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'mobile' => 'required|digits_between:9,13',
                    ]);
        if ($valid->fails()) {
            return redirect('edit_profile')->withInput(Input::all())->with('error', $valid->errors());
        } else {
            $this->user->first_name = Input::get('first_name');
            $this->user->last_name = Input::get('last_name');
            $this->user->email = Input::get('email');
            $this->user->mobile = Input::get('mobile');
            DB::table('users')->where('id', $id)->update($this->user['attributes']);
            if(isset($subscribe))
            {
            	$subscribe_user = DB::table('newsletter_subscribers')->where('user_id', $id)->first();
            	if(count($subscribe_user)==0)
            	{
					DB::table('newsletter_subscribers')->insert(['user_id' => Input::get('id'), 'name' => Input::get('first_name'), 'email' => Input::get('email')]);
            	}
            	else
            	{
            		DB::table('newsletter_subscribers')->where('user_id', $id)->update(['name' => Input::get('first_name'), 'email' => Input::get('email')]);
            	}
            }
            else{
            	$subscribe_user = DB::table('newsletter_subscribers')->where('user_id', $id)->first();
            	if(count($subscribe_user)>=1)
            	{
					DB::table('newsletter_subscribers')->where('user_id', $id)->delete();
            	}          	
            }
            return redirect('edit_profile')->with('success', 'User details updated successfully...');
        }
	} 

	/* Address Book Display */
	public function address_book()
	{
		if (Auth::user())
		{
			$uid 		= Auth::user()->id;					
			$address 		= DB::table('user_addressbook')->where('customer_id', $uid)->where('is_delete', 0)->get();
			return view('address_book', array('address' => $address));
		}
	}

	/* Add new address to logined user */
	public function add_address()
	{
		if (Auth::user())
		{
			$address_key = str_random(16);				
			$customer_id = Auth::user()->id;
			$default 	 = Input::get('default');
			if(isset($default)){
				$defalut = 1;
			}	
			else{
				$defalut = 0;
			}
			$user_exits = DB::table('user_addressbook')->where('customer_id', $customer_id)->first();
        	if(count($user_exits)==0)
        	{
        		$defalut = 1;
        	}
			DB::table('user_addressbook')->insert(['address_key' => $address_key, 'customer_id' => $customer_id, 'address' => Input::get('address'), 'default_address' => $defalut, 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
			return redirect('address_book')->with('success', 'Address Added Successfully!!');
		}
	}

	/* Delete Address */
	public function delete_address()
	{
		if(isset($_GET) && !empty($_GET))
		{
		   $customer_id = Input::get('customer_id');
		   $address_key = Input::get('address_key');
		   $orders = DB::table('orders')->where('address_key', $address_key)->count();
		   if($orders)
		   {
			   DB::table('user_addressbook')->where('address_key', $address_key)->update(['is_delete' => 1]);
		   }
		   else
		   {
		       DB::table('user_addressbook')->where('address_key', $address_key)->delete();
		   }
	       return "success";
		}
	}
	
	/* Display Selected Address In popup */
	public function addaddress_popup()
	{
		$id 				= Input::get('id');
	   	$address_key 		= Input::get('address_key');
		$address_display 	=   DB::table('user_addressbook')->where('id', $id)
							   ->where('address_key', $address_key)
							   ->first();
	   $address= view('addaddress_popup', array('address_display' => $address_display));
	   echo $address->render();
	}

	/* Update User Address */
	public function updateaddress()
	{	
		if (Auth::user())
		{			
			$customer_id 		= Auth::user()->id;
			$id 				= Input::get('id');
	   		$address_key 		= Input::get('address_key');	
			DB::table('user_addressbook')->where('address_key', $address_key)->where('id', $id)->where('customer_id', $customer_id)
										->update(['customer_id' => $customer_id, 'address' => Input::get('address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
			return redirect('address_book')->with('success', 'Address Updated Successfully!!');
		}
	}

	/* My Order Details Display */
	public function myorder()
	{
		$items = [];
		if (Auth::user())
		{
			$customer_id 	= Auth::user()->id;	
			$orders = DB::table('orders')->where('customer_id', $customer_id)->get();
			$data = [];
			if(count($orders))
			{
				$i = 0;
				foreach($orders as $order)
				{
					$data[$i]['order'] = DB::table('orders')
											->leftjoin('branch_description', 'orders.branch_id', '=', 'branch_description.branch_id')
											->select('branch_description.branch_name','orders.id','orders.invoice_number','orders.created_at as order_date','orders.order_status','orders.order_total', 'dook_rating_id')
											->where('orders.id', $order->id)
											->where('branch_description.language', $this->current_language)
											->first();
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
					$i++;
				}
			}
			//echo '<pre>'; print_r($orders); exit;
			return view('myorder', array('orders' => $data));
		}
	}
	
	public function getorder()
	{
			$order_id = Input::get('id');
			$order['details'] = DB::table('orders')->select('invoice_number', 'sub_total','order_total','service_tax', 'vat', 'delivery_fee')->where('id', $order_id)->first();
			$items = DB::table('order_itemdetails')
						->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
						->select('order_itemdetails.item_id', 'vendor_item_description.item_name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients')
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
			$i++;
		   }
		   $data= view('getorder', array('order' => $order));
		   echo $data->render();
	}

	/* Myorder Reorder */

	public function reorder()
	{
		$order_id 	= Input::get('id');
		$order =  DB::table('orders')
			 					->join('order_itemdetails', 'orders.id', '=', 'order_itemdetails.order_id')
			 					->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
			 					->join('vendor_items', 'vendor_items.id', '=', 'vendor_item_description.item_id')
					->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item')
					->where('orders.id', $order_id)
					->where('vendor_item_description.language', $this->current_language)
					->get();
					
	}
	
	public function update_address($address_key, $order_key)
	{
		$order = DB::table('orders')->where('order_key', $order_key)->first();
		$address = DB::table('user_addressbook')->where('address_key', $address_key)->first();
		$addresstype = $this->addresstype->getaddresstype();
		return view('update_address', array('address' => $address, 'order' => $order, 'addresstype' => $addresstype));
	}
	
	public function update_orderaddress()
	{
		$order_key = Input::get('order_key');
		$address_key = Input::get('address_key');
		$addresstype_id = Input::get('addresstype_id');
		$address = Input::get('address');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		
		DB::table('orders')->where('order_key', $order_key)->update(['is_address_change' => 1, 'addresstype_id' => $addresstype_id]);
		DB::table('user_addressbook')->where('address_key', $address_key)->Update(['address' => $address, 'latitude' => $latitude, 'longitude' => $longitude]);
		return 1;
	}

	public function rateDriver()
	{
		$data = view('deliveryboy_rating', array('order_id' => Input::get('id')));
		echo $data->render();
	}
	
	public function addRating()
	{
		$order_id = Input::get('order_id');
		$rating = Input::get('rating') * 5;
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
		return 1;
	}
}
