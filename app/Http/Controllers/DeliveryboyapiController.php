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
use App\Deliveryboy;
use stdClass;

class DeliveryboyapiController extends Controller {

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
	public function __construct(Guard $auth, User $user, Language $language, Order $order, Category $category, Vendoritem $vendoritem, Deliveryboy $deliveryboy)
	{
		$this->auth = $auth;
		$this->user = $user;
		$this->order = $order;
		$this->language = $language;
		$this->category = $category;
		$settings = DB::table('settings')->get();
	    $languages = $this->language->getlanguages();
	    $this->vendoritem = $vendoritem;
	    $this->deliveryboy = $deliveryboy;
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		$this->config_data = $config_data;
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (Input::get('language') != '') ? Input::get('language') : 'en';
		$this->currency = getdefault_currency(); 
	}
	 
	public function login()
	{
		$valid = Validator::make(Input::all(),
		                            ['delivery_boy_email' => 'required',
									 'delivery_boy_password' => 'required',
									 'device_type' => 'required',
									 'device_token' => 'required']
									);
									
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('httpcode' => 406, 'status' => 'failure', 'message' => $errors, 'data' => new stdClass());
			return json_encode($response);
		}
		else
		{
			$email = Input::get('delivery_boy_email');
			$password = base64_encode(Input::get('delivery_boy_password'));
			$deliveryboy = DB::table('deliveryboys')
							 ->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
							 ->select('deliveryboys.deliveryboy_key','deliveryboy_description.deliveryboy_name as delivery_boy_name','deliveryboys.email as delivery_boy_email','deliveryboys.image as profile_image','deliveryboys.mobile as mobile_number','deliveryboys.is_delete','deliveryboys.status','deliveryboys.id')
							 ->where('deliveryboy_description.language', $this->current_language)
							 ->where('deliveryboys.email', $email)
							 ->where('deliveryboys.password', $password)
							 ->first();
			if(count($deliveryboy))
			{
				$deliveryboy->profile_image = URL::to(($deliveryboy->profile_image != '') ? 'assets/uploads/deliveryboys/'.$deliveryboy->profile_image : 'assets/admin/images/user.png');
				if($deliveryboy->is_delete == 1)
				{
					$response = array('httpcode' => 406, 'status' => 'failure', 'message' => 'Your account was deleted by admin. Please contact our admin', 'data' => new stdClass());
					return json_encode($response);
				}
				elseif($deliveryboy->status == 1)
				{
					DB::table('deliveryboys')->where('id', $deliveryboy->id)->update(['device_type' => Input::get('device_type'), 'device_token' => Input::get('device_token'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude'), 'is_logout' => 0, 'availability' => 1]);
					$response = array('httpcode' => 200, 'status' => 'success', 'data' => $deliveryboy,  'message' => 'Logged in successfully', 'responsetime' => date('Y-m-d g:i A'));
					return json_encode($response);
			    }
			    else
			    {
			       $response = array('httpcode' => 406, 'status' => 'failure', 'message' => 'You are blocked', 'data' => new stdClass());
				   return json_encode($response);
				}
			}
			else
			{
				$response = array('httpcode' => 406, 'status' => 'failure', 'message' => 'Invalid Username or Password', 'data' => new stdClass());
			    return json_encode($response);
			}
		}
	}
	
	public function forgetpassword()
	{
		$email = Input::get('delivery_boy_email');
		$valid = Validator::make(Input::all(), ['delivery_boy_email' => 'required|email']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());	
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		   return json_encode($response);
        }
        else
        {
        	$deliveryboy = DB::table('deliveryboys')
							 ->join('deliveryboy_description', 'deliveryboys.id', '=', 'deliveryboy_description.deliveryboy_id')
							 ->SelectRaw(DB::getTablePrefix().'deliveryboys.*,'.DB::getTablePrefix().'deliveryboy_description.deliveryboy_name as name')
							 ->where('deliveryboy_description.language', $this->current_language)
							 ->where('deliveryboys.email', $email)
							 ->first();
        	if(count($deliveryboy))
        	{
        	   $new_password = base64_decode($deliveryboy->password);
        	   $subject = "Forget Password - Shuneez";
        	   $msg = "Hi ".$deliveryboy->name.", Kindly find your new password ".$new_password;
        	   $this->sendmail($email, $subject, $msg);
        	   $response = array('httpcode' => 200, 'status' => "success", 'message' => "Kindly check your mail to retrieve your password", 'data' => new stdClass(), 'responsetime' => date('Y-m-d g:i A'));
			   return json_encode($response);
        	}
        	else
        	{
        	   $response = array('httpcode' => 406, 'status' => "failure", 'message' => "Invalid email id", 'data' => new stdClass());
			   return json_encode($response);
        	}


        }
	}
	
	public function getneworders()
	{
		$valid = Validator::make(Input::all(),['delivery_boy_key' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('httpcode' => 406, 'status' => 'failure', 'message' => $errors, 'data' => new stdClass());
			return json_encode($response);
		}
		else
		{
			$deliveryboy = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->first();
			$deliveryboy_id = $deliveryboy->id;
			$current_day = date('Y-m-d H:i:s');
			
			$orders = DB::select("SELECT TIME_TO_SEC(TIMEDIFF('".$current_day."', sh_deliveryboy_request.created_at)) AS remaining_seconds, CONCAT(sh_branches.street, sh_branches.city,sh_branches.country,sh_branches.zipcode) AS shop_address, sh_orders.order_key as order_key, sh_branch_description.branch_name as shop_name, sh_user_addressbook.address as delivery_address, sh_orders.order_total, sh_deliveryboy_request.created_at as order_datetime, sh_orders.invoice_number as invoice, sh_branches.mobile as vendor_mobile, sh_branches.latitude as shop_latitude, sh_branches.longitude as shop_longitude, sh_user_addressbook.latitude as customer_latitude, sh_user_addressbook.longitude as customer_longitude, sh_orders.customer_mobile 
			FROM sh_deliveryboy_request 
			JOIN sh_orders ON sh_deliveryboy_request.order_id = sh_orders.id 
			JOIN sh_branches ON sh_orders.branch_id = sh_branches.id 
			JOIN sh_branch_description ON sh_branches.id = sh_branch_description.branch_id 
			JOIN sh_user_addressbook ON sh_orders.address_id = sh_user_addressbook.id 
			WHERE (TIME_TO_SEC(TIMEDIFF('".$current_day."', sh_deliveryboy_request.created_at)) BETWEEN 0 AND ".$this->config_data['order_accept_timelimit'].") AND sh_deliveryboy_request.deliveryboy_id = ".$deliveryboy_id."
			AND sh_deliveryboy_request.status = 'n'
			AND sh_branch_description.language = '".$this->current_language."' ORDER BY remaining_seconds");

			for($i=0; $i<count($orders);$i++)
			{
				$orders[$i]->shop_logo = URL::to('assets/admin/images/user.png');
				$orders[$i]->order_datetime = date('M d Y g:i A', strtotime($orders[$i]->order_datetime));
				$orders[$i]->delivery_time = '';
				$orders[$i]->remaining_seconds = $this->config_data['order_accept_timelimit'] - $orders[$i]->remaining_seconds;
			}
			
			$response = array('httpcode' => 200, 'status' => 'success', 'data' => array('orders_list' => $orders, 'currency_symbol' => $this->currency), 'message' => 'orders list', 'response_time' => date('Y-m-d g:i A'));
			return json_encode($response);
		}
	}
	
	public function getassignorders()
	{
		$valid = Validator::make(Input::all(),['delivery_boy_key' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('httpcode' => 406, 'status' => 'failure', 'message' => $errors, 'data' => new stdClass());
			return json_encode($response);
		}
		else
		{
			$deliveryboy = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->first();
			$deliveryboy_id = $deliveryboy->id;
			
			$orders = DB::table('deliveryboy_request')
						->join('orders', 'deliveryboy_request.order_id', '=', 'orders.id')
						->join('branches', 'orders.branch_id', '=', 'branches.id')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
						->select(DB::raw('CONCAT(sh_branches.street, ", ", sh_branches.city, ", ", sh_branches.country, ", ", sh_branches.zipcode) AS shop_address'), 'orders.id as order_id', 'branch_description.branch_name as shop_name', 'branches.mobile as vendor_mobile', 'user_addressbook.address as delivery_address', 'orders.order_total', 'deliveryboy_request.created_at as order_datetime', 'orders.invoice_number as invoice','orders.sub_total','orders.order_total', 'orders.order_status as delivery_status', 'orders.order_key', 'orders.order_key', 'branches.latitude as shop_latitude', 'branches.longitude as shop_longitude', 'user_addressbook.latitude as customer_latitude', 'user_addressbook.longitude as customer_longitude')
						->where('deliveryboy_request.deliveryboy_id', $deliveryboy_id)
						->where('deliveryboy_request.status', 'a')
						->where(function($query)
							{
								$query->where('orders.order_status', 'as')->OrWhere('orders.order_status', 'o');
							})
						->where('branch_description.language', $this->current_language)
						->get();
			for($i=0; $i<count($orders); $i++)
			{			
				$orders[$i]->shop_logo = URL::to('assets/admin/images/user.png');
				$orders[$i]->delivery_status = ($orders[$i]->delivery_status == 'o') ? 'pickup' : 'Accepted';
				$orders[$i]->order_datetime = date('M d Y g:i A', strtotime($orders[$i]->order_datetime));
			}
			
			$response = array('httpcode' => 200, 'status' => 'success', 'message' => 'orders list', 'data' => array('orders_list' => $orders, 'currency_symbol' => $this->currency), 'response_time' => date('Y-m-d g:i A'));
			return json_encode($response);
		}
	}
	
	public function getdelivered_orders()
	{
		$valid = Validator::make(Input::all(),['delivery_boy_key' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('httpcode' => 406, 'status' => 'failure', 'message' => $errors, 'data' => new stdClass());
			return json_encode($response);
		}
		else
		{
			$deliveryboy = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->first();
			$deliveryboy_id = $deliveryboy->id;
			$orders = DB::table('deliveryboy_request')
						->join('orders', 'deliveryboy_request.order_id', '=', 'orders.id')
						->join('branches', 'orders.branch_id', '=', 'branches.id')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
						->select(DB::raw('CONCAT(sh_branches.street, ", ", sh_branches.city, ", ", sh_branches.country, ", ", sh_branches.zipcode) AS shop_address'), 'orders.id as order_id', 'branch_description.branch_name as shop_name', 'branches.mobile as vendor_mobile', 'user_addressbook.address as delivery_address', 'orders.order_total', 'orders.delivered_at as order_datetime', 'orders.invoice_number as invoice','orders.sub_total','orders.order_total', 'orders.order_status as delivery_status', 'orders.order_key', 'orders.order_key', 'branches.latitude as shop_latitude', 'branches.longitude as shop_longitude', 'user_addressbook.latitude as customer_latitude', 'user_addressbook.longitude as customer_longitude')
						->where('deliveryboy_request.deliveryboy_id', $deliveryboy_id)
						->where('deliveryboy_request.status', 'a')
						->where('orders.order_status', 'd')
						->where('branch_description.language', $this->current_language)
						->get();
			for($i=0; $i<count($orders); $i++)
			{			
				$orders[$i]->order_datetime = date('M d Y g:i A', strtotime($orders[$i]->order_datetime));
			}
			$response = array('httpcode' => 200, 'status' => 'success', 'message' => 'orders list', 'data' => array('orders_list' => $orders, 'currency_symbol' => $this->currency), 'response_time' => date('Y-m-d g:i A'));
			return json_encode($response);
		}
	}
	
	public function getcancel_orders()
	{
		$valid = Validator::make(Input::all(),['delivery_boy_key' => 'required']);
		if($valid->fails())
		{
			$errors = $this->modifyErrorStructure($valid->errors());
			$response = array('httpcode' => 406, 'status' => 'failure', 'message' => $errors, 'data' => new stdClass());
			return json_encode($response);
		}
		else
		{
			$deliveryboy = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->first();
			$deliveryboy_id = $deliveryboy->id;
			$orders = DB::table('deliveryboy_request')
						->join('orders', 'deliveryboy_request.order_id', '=', 'orders.id')
						->join('branches', 'orders.branch_id', '=', 'branches.id')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
						->select(DB::raw('CONCAT(sh_branches.street, ", ", sh_branches.city, ", ", sh_branches.country, ", ", sh_branches.zipcode) AS shop_address'), 'orders.id as order_id', 'branch_description.branch_name as shop_name', 'branches.mobile as vendor_mobile', 'user_addressbook.address as delivery_address', 'orders.order_total', 'deliveryboy_request.created_at as order_datetime', 'orders.invoice_number as invoice','orders.sub_total','orders.order_total', 'orders.order_status as delivery_status', 'orders.order_key', 'orders.order_key', 'branches.latitude as shop_latitude', 'branches.longitude as shop_longitude', 'user_addressbook.latitude as customer_latitude', 'user_addressbook.longitude as customer_longitude')
						->where('deliveryboy_request.deliveryboy_id', $deliveryboy_id)
						->where('deliveryboy_request.status', 'd')
						->where('branch_description.language', $this->current_language)
						->get();
			for($i=0; $i<count($orders); $i++)
			{			
				$orders[$i]->order_datetime = date('M d Y g:i A', strtotime($orders[$i]->order_datetime));
			}
			$response = array('httpcode' => 200, 'status' => 'success', 'message' => 'orders list', 'data' => array('orders_list' => $orders, 'currency_symbol' => $this->currency), 'response_time' => date('Y-m-d g:i A'));
			return json_encode($response);
		}
	}
	
	public function getorder()
	{
		$valid = Validator::make(Input::all(), ['order_key' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		   return json_encode($response);
        }
        else
        {
			$data = DB::table('orders')->where('order_key', Input::get('order_key'))->first();
			$order_id = $data->id;
			$order = DB::table('orders')
								->join('branches', 'orders.branch_id', '=', 'branches.id')
								->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
								->join('users', 'orders.customer_id', '=', 'users.id')
								->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
								->join('deliveryboy_request', 'orders.deliveryboy_id', '=', 'deliveryboy_request.deliveryboy_id')
								->select(DB::raw('CONCAT(sh_branches.street, ", ", sh_branches.city, ", ", sh_branches.country, ", ", sh_branches.zipcode) AS shop_address'), 'orders.id as order_id', 'branch_description.branch_name as shop_name', 'branches.mobile as vendor_mobile', 'user_addressbook.address as delivery_address', 'orders.order_total', 'deliveryboy_request.created_at as assign_datetime', 'orders.order_datetime as order_datetime', 'orders.invoice_number as invoice','orders.sub_total','orders.order_total','orders.service_tax', 'orders.vat', 'orders.payment_type', 'orders.payment_status', 'orders.delivery_type', 'orders.delivery_fee', 'orders.order_status as delivery_status', 'orders.order_key', 'orders.order_key', 'orders.customer_first_name', 'orders.customer_last_name', 'orders.customer_mobile', 'branches.latitude as shop_latitude', 'branches.longitude as shop_longitude', 'user_addressbook.latitude as customer_latitude', 'user_addressbook.longitude as customer_longitude')
								->where('orders.id', $order_id)
								->first();
			$order->order_datetime = date('M d Y g:i A', strtotime($order->order_datetime));
			$order->assign_datetime = date('M d Y g:i A', strtotime($order->assign_datetime));
			$order->shop_logo = URL::to('assets/admin/images/user.png');
			$order->payment_type = ($order->payment_type == 0) ? 'COD' : 'Online';
			if($order->payment_status == 'p')
			{ 
				$order->payment_status = 'Payment Pending';
			}
			elseif($order->payment_status == 's')
			{ 
				$order->payment_status = 'Payment Success';
			}
			elseif($order->payment_status == 'f')
			{ 
				$order->payment_status = 'Payment Failure';
			}
			$order->delivery_type = 'Delivery';
			$order->delivery_status = 'Confirmed';
			$order->package_price = 0;
			$items = DB::table('order_itemdetails')
						->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
						->select('order_itemdetails.item_id', 'vendor_item_description.item_name as name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients')
						->where('order_itemdetails.order_id', $order_id)
						->where('vendor_item_description.language', $this->current_language)
						->get();
			$i = 0;
		   foreach($items as $item)
		   {
				$order->items[$i] = DB::table('order_itemdetails')
								->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
								->select('vendor_item_description.item_name as name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients')
								->where('order_itemdetails.item_id', $item->item_id)
								->where('vendor_item_description.language', $this->current_language)
								->where('order_itemdetails.order_id', $order_id)
								->first();
				$order->items[$i]->total_price = $item->price * $item->quantity; 
				if($item->is_ingredients == 1)
				{
					$order->items[$i]->ingredients = DB::table('order_ingredientdetails')
													->join('ingredientlist_description', 'order_ingredientdetails.ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
													->select('order_ingredientdetails.price as ingredient_price', 'ingredientlist_description.ingredientlist_name as ingredient_name')
													->where('order_ingredientdetails.item_id', $item->item_id)
													->where('ingredientlist_description.language', $this->current_language)
													->where('order_ingredientdetails.order_id', $order_id)
													->get();
				}
				else
				{
					$order->items[$i]->ingredients = [];
				}
				$i++;
		    }
		  $order->total_ingredient_price = 0;
		  $order->currency_symbol = $this->currency;
		  $response = array('httpcode' => 200, 'status' => "success", 'message' => 'Order list successfully retrieved', 'data' => $order, 'response_time' => date('Y-m-d g:i A'));
		  return json_encode($response);
		}
	}
	
	public function acceptorder()
	{
		$valid = Validator::make(Input::all(), ['delivery_boy_key' => 'required', 'order_key' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		   return json_encode($response);
        }
        else
        {
			$deliveryboy = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->first();
			$deliveryboy_id = $deliveryboy->id;
			$order = DB::table('orders')->where('order_key', Input::get('order_key'))->first();
			$order_id = $order->id;
			
			$count = DB::table('deliveryboy_request')->where('order_id', $order_id)->where('status', 'a')->count();
			if($count)
			{ 
				$response = array('httpcode' => 406, 'status' => "failure", 'message' => 'Order already accepted', 'data' => new stdClass());
				return json_encode($response);
			}
			else
			{
				DB::table('deliveryboy_request')->where('order_id', $order_id)->where('status', 'n')->update(['status' => 'r']);
				DB::table('deliveryboy_request')->where('deliveryboy_id', $deliveryboy_id)->where('order_id', $order_id)->update(['status' => 'a']);
				$response = array('httpcode' => 200, 'status' => "success", 'message' => 'Order accepted successfully', 'data' => array('accepted_deliveryboy_key' => Input::get('delivery_boy_key'), 'order_key' => Input::get('order_key'), 'deliveryboy_status' => 'Onjob'), 'responsetime' => date('Y-m-d g:i A'));
				return json_encode($response);
			}
		}
	}
	
	public function pickuporder()
	{
		$valid = Validator::make(Input::all(), ['order_key' => 'required', 'delivery_boy_key' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());	
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		   return json_encode($response);
        }
        else
        {
			$order = DB::table('orders')->where('order_key', Input::get('order_key'))->first();
			$order_id = $order->id;
			$user = DB::table('users')->where('customer_key', $order->customer_key)->first();
			DB::table('orders')->where('id', $order_id)->update(['order_status' => 'o', 'assigned_at' => date('Y-m-d H:i:s')]);
			$order = DB::table('orders')->where('id', $order_id)->first();
			$name = $order->customer_first_name.' '.$order->customer_last_name;
			$email = $order->customer_email;
			$subject = 'Order Delivery -'.$order->invoice_number;
			$msg = trans('frontend.Hello').' '.$name.' '.trans('frontend.Out Delivery Message').' '.trans('frontend.Shuneez');
			//$this->sendmail($email, $subject, $msg);
			$mobile = valid_mobile($order->customer_mobile);
			sendSMS(2, $mobile, $msg);
			sendPushNotification($msg, $user->device_id);
			
			$response = array('httpcode' => 200, 'status' => "success", 'msg' => 'Order pickuped successfully', 'data' => new stdClass(), 'response_time' => date('Y-m-d H:i:s'));
			return json_encode($response);
		}
	}
	
	public function cancelorder()
	{
		$valid = Validator::make(Input::all(), ['order_key' => 'required', 'delivery_boy_key' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		   return json_encode($response);
        }
        else
        {
			$deliveryboy = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->first();
			$deliveryboy_id = $deliveryboy->id;
			$order = DB::table('orders')->where('order_key', Input::get('order_key'))->first();
			$order_id = $order->id;
			$reason = Input::get('cancel_reason');
			DB::table('deliveryboy_request')->where('status', 'r')->where('order_id', $order_id)->update(['status' => 'n']); 
			DB::table('deliveryboy_request')->where('deliveryboy_id', $deliveryboy_id)->where('order_id', $order_id)->update(['status' => 'd', 'reason' => $reason]);
			DB::table('orders')->where('id', $order_id)->update(['deliveryboy_id' => 0, 'deliveryboy_accept' => 0, 'order_status' => 'c']);
			
			$response = array('httpcode' => 200, 'status' => "success", 'message' => 'Order cancelled successfully', 'data' => new stdClass());
			return json_encode($response);
		}
	}
	
	public function completeorder()
	{
		$valid = Validator::make(Input::all(), ['order_key' => 'required', 
												'delivery_boy_key' => 'required',
												'is_payment' => 'required',
												'payment_type' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass() );
		   return json_encode($response);
        }
        else
        {
			$payment = (Input::get('is_payment') == 1) ? 'success' : 'pending';
			$order = DB::table('orders')->where('order_key', Input::get('order_key'))->first();
			$order_id = $order->id;
			$image = '';
			if(Input::file('signature') != '')
			{
				$dest = 'assets/uploads/signature';
				$image = Input::file('signature')->getClientOriginalName();
				Input::file('signature')->move($dest,$image);
			}
			DB::table('orders')->where('id', $order_id)->update(['order_status' => 'd', 'payment_status' => $payment, 'payment_type' => Input::get('payment_type'), 'comments' => Input::get('comments'), 'delivered_at' => date('Y-m-d H:i:s')]);
			$order = DB::table('orders')->where('id', $order_id)->first();
			$name = $order->customer_first_name.' '.$order->customer_last_name;
			$email = $order->customer_email;
			$subject = 'Order Delivery -'.$order->invoice_number;
			$msg = 'Hi '.$name.', <br><br> Your order - '.$order->invoice_number.' has been delivered successfully<br><br> Thanks & Regards <br><br> The Shuneez';
			$this->sendmail($email, $subject, $msg);
			
			$response = array('httpcode' => 200, 'status' => "success", 'message' => 'Order delivered successfully', 'data' => new stdClass());
			return json_encode($response);
		}
	}
	
	public function update_deliveryboy_status()
	{
		$valid = Validator::make(Input::all(), ['delivery_boy_key' => 'required', 'status' => 'required']);

		if ($valid->fails())
        {
		   $errors = $this->modifyErrorStructure($valid->errors());
           $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		   return json_encode($response);
        }
        else
        {
			$status = (strtolower(Input::get('status')) == 'on') ? 1 : 0;
			DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->update(['availability' => $status]);
			$response = array('httpcode' => 200, 'status' => "success", 'message' => 'Status updated successfully', 'data' => new stdClass(), 'response_time' => date('Y-m-d g:i A'));
			return json_encode($response);
		}
	}
	
	 public function updateprofile() 
	 {
		$delivery_boy_key = Input::get('delivery_boy_key');
		$data = DB::table('deliveryboys')->where('deliveryboy_key', $delivery_boy_key)->first();
		$valid = Validator::make(Input::all(), ['mobile_number' => 'required|numeric|unique:deliveryboys,mobile,' . $data->id,
												'profile_image' => 'mimes:jpg,jpeg,png'
											   ]);
		if ($valid->fails()) 
        {
			$errors = $this->modifyErrorStructure($valid->errors());
            $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		    return json_encode($response);
        } 
        else 
        {
			$this->deliveryboy->mobile = Input::get('mobile_number');
			if(Input::file('profile_image') != '')
			{
				$dest = 'assets/uploads/deliveryboys';
				if($data->image != '' && file_exists($dest.'/'.$data->image))
				{
					unlink($dest.'/'.$data->image);
				}
				$image = Input::file('profile_image')->getClientOriginalName();
				Input::file('profile_image')->move($dest, $image);
				$this->deliveryboy->image = $image;
			}
			DB::table('deliveryboys')->where('deliveryboy_key', $delivery_boy_key)->update($this->deliveryboy['attributes']);
            
            $name = Input::get('delivery_boy_name');
			DB::table('deliveryboy_description')->where('deliveryboy_id', $data->id)->where('language', 'en')->update(['deliveryboy_name' => $name]);
			
            $response = array('httpcode' => 200, 'status' => "success", 'message' => 'Profile updated successfully', 'data' => new stdClass(), 'response_time' => date('Y-m-d g:i A'));
		    return json_encode($response);
        }
    }
    
    public function change_password()
    {
		$valid = Validator::make(Input::all(), ['delivery_boy_key' => 'required',
												'delivery_boy_password' => 'required',
												'new_password' => 'required|min:6',
											   ]);
		if ($valid->fails()) 
        {
			$errors = $this->modifyErrorStructure($valid->errors());
            $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		    return json_encode($response);
        } 
        else 
        {
			$password = base64_encode(Input::get('delivery_boy_password'));
			$newpassword = base64_encode(Input::get('new_password'));
			$count = DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->where('password', $password)->count();
			if($count)
			{
				DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->update(['password' => $newpassword]);
				$response = array('httpcode' => 200, 'status' => "success", 'message' => 'Password changed successfully', 'data' => new stdClass(), 'response_time' => date('Y-m-d g:i A'));
				return json_encode($response);
			}
			else
			{
				$response = array('httpcode' => 406, 'status' => "failure", 'message' => "Old password is wrong", 'data' => new stdClass());
				return json_encode($response);
			}
		}
	}
	
	public function logout()
	{
		$valid = Validator::make(Input::all(), ['delivery_boy_key' => 'required']);
		if ($valid->fails()) 
        {
			$errors = $this->modifyErrorStructure($valid->errors());
            $response = array('httpcode' => 406, 'status' => "failure", 'message' => $errors, 'data' => new stdClass());
		    return json_encode($response);
        } 
        else 
        {
			DB::table('deliveryboys')->where('deliveryboy_key', Input::get('delivery_boy_key'))->update(['is_logout' => 1]);
			$response = array('httpcode' => 200, 'status' => "success", 'message' => 'Logged out successfully', 'data' => new stdClass());
		    return json_encode($response);
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
	
	public function modifyErrorStructure($modelError) 
	{
		$temp[] = $modelError;
		$errors = [];
		foreach ($modelError->toArray() as $key => $value) 
		{ 
			$errors[$key] = $value[0];
		}
		return $errors;
	}
	
}
