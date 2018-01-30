<?php namespace App\Http\Controllers;
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
use App\Category;
use App\User;
use App\Branch;
use App\Vendoritem;
use View;
use Cart;
use URL;
use Auth;
use Redirect;
use App\Order;
use PHPMailer;
use App\Execlusion;

class ShopController extends Controller {

	public function __construct(Guard $auth, Language $language, Branch $branch, Vendoritem $vendoritem, Category $category, User $user, Order $order, Execlusion $execlusion)
	{
		//$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->language = $language;
	    $this->vendoritem = $vendoritem;
	    $this->user = $user;
	    $this->order = $order;
	    $this->branch = $branch;
	    $this->category = $category;
	    $this->execlusion = $execlusion;
	    $this->prefix = DB::getTablePrefix();
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	    $settings = DB::table('settings')->get();
	    $languages = $this->language->getlanguages();
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		
		$this->config_data = $config_data;
		View::share (['config_data'=> $config_data, 'languages' => $languages, 'default_currency' => getdefault_currency()]);
	}
	
	public function listings()
	{
		$valid = Validator::make(Input::all(),['keyword' => 'required']);
		if($valid->fails())
		{
			return Redirect::back()->WithInput(Input::All())->with('error', $valid->errors());
		}
		else
		{
			Session::forget('orders');
			Cart::destroy();
			if(Input::get('branch_id') != '')
			{
				$branch_id = Input::get('branch_id');
			}
			else
			{
				$keyword = Input::get('keyword');
				$branch = DB::table('branches')
					->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
					->SelectRaw(DB::getTablePrefix().'branches.id,'.DB::getTablePrefix().'branch_description.branch_name as branch')
					->where(function($query) use($keyword)
					{
						$query->where('branch_description.branch_name', 'like', '%'.$keyword.'%')
								->OrWhere('branches.street', 'like', '%'.$keyword.'%')
								->OrWhere('branches.city', 'like', '%'.$keyword.'%')
								->OrWhere('branches.meta_keywords', 'like', '%'.$keyword.'%');
					})
					->where('branches.status', 1)
					->where('branches.is_delete', 0)
					->first();
				$branch_id = (count($branch)) ? $branch->id : 0;
				
			}
			$branch = DB::table('branches')->where('id', $branch_id)->first();
			$delivery_fee = (count($branch)) ? $branch->delivery_fee : 0;
			Session::put(['orders.branch_id' => $branch_id, 'orders.delivery_type' => Input::get('delivery_type'), 'orders.branch_delivery_fee' => $delivery_fee]);
			
			return redirect('listings');
		}
	}

	public function getitems()
	{
		if(!Session::has('orders'))
		{
			return redirect('/');
		}
		else
		{
			$date = getdate();
			$day = strtolower($date['weekday']);
			$time = date('H:i:s');
			$branch = $this->branch->getsearchbranch(Session('orders.branch_id'), Session('orders.delivery_type'));
			if(count($branch) > 0) {
				Session::put(['orders.branch_id' => $branch->id]);
			}
			//print_r($branch); exit;
			$item = [];
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
					$item[$i]['items'] = DB::table('vendor_items')
										 ->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
										 ->SelectRaw($this->prefix.'vendor_items.*,'.$this->prefix.'vendor_item_description.item_name')
										 ->where('vendor_items.category_id', $category->id)
										 ->where('vendor_items.is_delete', 0)
										 ->where('vendor_items.status', 1)
										 ->where('vendor_item_description.language', $this->current_language)
										 ->orderby('vendor_items.sort_number', 'asc')
										 ->get();
					$i++;
				}
			}
			//echo '<pre>'; print_r($branch); exit;
			return view('listings', array('items' => $item, 'branch' => $branch));
		}
	}
	
	public function selectingredient()
	{
		$id= Input::get('id');
		$ingredients = DB::table('vendor_item_ingredients')
						->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw($this->prefix.'vendor_item_ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name')
						->where('vendor_item_ingredients.item_id', $id)
						->where('ingredient_description.language', $this->current_language)
						->orderby('sort_number', 'asc')
						->get();
		$execlusions = $this->execlusion->getVendorExeclusions($id);
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
				$data[$i]['ingredientlists'] = DB::table('vendor_item_ingredientlist')
									->join('ingredientlist_description', 'vendor_item_ingredientlist.item_ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
									->SelectRaw(DB::getTablePrefix().'vendor_item_ingredientlist.*,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_name,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_id')
									->where('vendor_item_ingredientlist.item_ingredient_id', $ingredient->id)
									->where('ingredientlist_description.language', $this->current_language)
									->get();
				$i++;
			}
		}
		$vendor_item = DB::table('vendor_items')
						 ->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
						 ->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item,'.DB::getTablePrefix().'vendor_item_description.item_description as description')
						 ->where('vendor_items.id', $id)
						 ->where('vendor_item_description.language', $this->current_language)
						 ->first();
		
		$size_list = DB::table('item_size')
						->join('item_size_description', 'item_size.id', '=', 'item_size_description.item_size_id')
						->select('item_size.*', 'size_name as size')
						->where('item_size.item_id', $id)
						->where('language', $this->current_language)
						->get();
		
		
	   $ingredien = view('ingredientlist', array('items' => $data, 'vendor_item' => $vendor_item, 'size_list' => $size_list, 'execlusions' => $execlusions));
	   echo $ingredien->render();
	  
	}
	
	public function addtocart()
    {
		$id = Input::get('id');
        $quantity = 1;
        $item =  DB::table('vendor_items')
					->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
					->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item')
					->where('vendor_item_description.language', $this->current_language)
					->where('vendor_items.id', $id)
					->first();
        Cart::add(array('id' => $item->id, 'name' => $item->item, 'qty' => $quantity, 'price' => $item->price, 'options' => array('image' => $item->image, 'notes' => '', 'is_ingredients' => $item->is_ingredients, 'is_size' => 0, 'is_execlusion' => 0)));
        
		$branch = DB::table('branches')->where('id', Session('orders.branch_id'))->first();
		
		if($branch->deliveryfee_type == 'distance')
		{
			$delivery_fee = (Session('orders.distance') > $branch->distance) ? $branch->delivery_fee + $branch->additional_charge : $branch->delivery_fee; 
		}
		else
		{
			$delivery_fee = $branch->delivery_fee; 
		}
		
		$delivery_fee = (Session('orders.delivery_type') == 'd') ? $delivery_fee : 0;
		
		if(!Session::has('orders.ingredient_total'))
		{
			Session::put(['orders.ingredient_total' => 0]);
		}
		
		Session::put(['orders.delivery_fee' => $delivery_fee]);
		
		return 1;
    } 
    
    public function additem()
	{
		$item_id = Input::get('item_id');
		$quantity = Input::get('quantity');
		$item =  DB::table('vendor_items')
					->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
					->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item')
					->where('vendor_item_description.language', $this->current_language)
					->where('vendor_items.id', $item_id)
					->first();
					//print_r($item); exit;
		$is_ingredients = (count(Input::get('ingredient_list')) > 0) ? 1 : 0;
		$ingredient_list = Input::get('ingredient_list');
		$ingredient[] = '';
		$ingredient_id[] = '';
		$ingredient_price[] = '';
		$ingredient_total = 0;
		if(count($ingredient_list))
		{
			for($i=0; $i<count($ingredient_list); $i++)
			{
				$data = explode('|', $ingredient_list[$i]);
				$ingredient[$i] = $data[0];
				$ingredient_id[$i] = $data[1];
				$ingredient_price[$i] = $data[2];
				$ingredient_total += $data[2];
			}
		}
		$ingredient_total = $ingredient_total * $quantity;	
		$is_size = (Input::get('size') != '') ? 1 : 0;
		$size = explode('|', Input::get('size')); 
		$size_name = ( $is_size ) ? $size[0] : '';
		$size_id = ( $is_size ) ? $size[1] : '';
		$size_price = ( $is_size ) ? $size[2] : '';

		$is_execlusion = (count(Input::get('execlusion_id'))) ? 1 : 0;
		$execlusionlist = Input::get('execlusion_id');

		$execlusion = [];
		$execlusion_id = [];
		if(count($execlusionlist))
		{
			for($i=0; $i<count($execlusionlist); $i++)
			{
				$data = explode('|', $execlusionlist[$i]);
				$execlusion[$i] = $data[0];
				$execlusion_id[$i] = $data[1];
			}
		}

		Cart::add(array('id' => $item->id, 'name' => $item->item, 'qty' => $quantity, 'price' => $item->price, 'options' => array('image' => $item->image, 'notes' => Input::get('message'), 'is_ingredients' => $is_ingredients, 'ingredientlist' => $ingredient, 'ingredientlist_id' => $ingredient_id, 'ingredient_price' => $ingredient_price, 'ingredient_total' => $ingredient_total, 'is_size' => $is_size, 'size' => $size_name, 'size_id' => $size_id, 'size_price' => $size_price, 'is_execlusion' => $is_execlusion, 'execlusions' => $execlusion, 'execlusion_id' => $execlusion_id)));
        //echo '<pre>'; print_r(Cart::content()); exit;
		
		$branch = DB::table('branches')->where('id', Session('orders.branch_id'))->first();
		
		if($branch->deliveryfee_type == 'distance')
		{
			$delivery_fee = (Session('orders.distance') > $branch->distance) ? $branch->delivery_fee + $branch->additional_charge : $branch->delivery_fee; 
		}
		else
		{
			$delivery_fee = $branch->delivery_fee; 
		}
		$delivery_fee = (Session('orders.delivery_type') == 'd') ? $delivery_fee : 0;
		
		Session::put(['orders.delivery_fee' => $delivery_fee]);
		
		return 1;
	}
	
	
	public function get_lat_long($address)
	{
    	$address = str_replace(" ", "+", $address);
    	$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false");
    	$json = json_decode($json);
		//print_r($json); exit;
    	$lat = isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'}) ? $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'} : 0;
    	$long = isset($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'}) ? $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'} : 0;
    	return $lat.','.$long;
	}
	
	public function update_cart()
    {
    	$rowid = Input::get('rowid');
        $qty = Input::get('qty');
        Cart::update($rowid, array('qty' => $qty));
        return 1;
    }

    public function remove_product($rowid)
    {
        Cart::remove($rowid);
        return redirect('listings');
    }
    
    public function cart()
    {
		if(!Session::has('orders'))
		{
			return redirect('/');
		}
		else
		{
			return view('cart');
		}
	}
	
	public function getdelivery_time()
	{
		$delivery = Input::get('delivery'); 
		$delivery_time = Input::get('delivery_time');
		$date = Input::get('delivery_date');
		$delivery_date = date('Y-m-d H:i:s', strtotime($date.$delivery_time));
		$current_date = date('Y-m-d H:i:s');
		$delivery_type = Input::get("delivery_type");
		if(Input::get('delivery_type') != Session('orders.delivery_type'))
		{
			$date = getdate();
			$day = strtolower($date['weekday']);
			$time = date('H:i:s');
			$branch = [];
			$branch = $this->branch->getsearchbranch(Session('orders.branch_id'), Input::get("delivery_type"));
			if(count($branch) > 0) {
				Session::put(['orders.branch_id' => $branch->id]);
			}
			if(count($branch) == 0)
			{
				$error = ($delivery_type == 'p') ? 'pickup' : 'delivery';
				return redirect('/cart')->withInput(Input::all())->with('service_error', 'Sorry this branch is not providing '.$error);
			}
		}
		if($delivery == 1)
		{
			$valid = Validator::make(Input::all(),['delivery_date' => 'required', 'delivery_time' => 'required']);
			
			if($valid->fails())
			{
				return redirect('/cart')->withInput(Input::all())->with('error', $valid->errors());
			}
			else
			{
				$day = strtolower(date('l', strtotime($date)));
				
				$valid_time = DB::table('vendor_timeslot')
								->where('branch_id', Session('orders.branch_id'))
								->where('working_day', $day)
								->where('start_time', '<', $delivery_time)
								->where('close_time', '>', $delivery_time)
								->count();
				if(strtotime($current_date) >= strtotime($delivery_date))
				{
					return redirect('/cart')->withInput(Input::all())->with('time_error', 'Delivery time should be greater than current time');
				}
				elseif($valid_time == 0)
				{
					return redirect('/cart')->withInput(Input::all())->with('time_error', 'Please select another delivery time');
				}
				else
				{
					Session::put(['orders.delivery_date' => $delivery_date, 
								'orders.delivery_type' => Input::get('delivery_type'), 
								'orders.subtotal' => Input::get('subtotal'), 
								'orders.vat' => Input::get('vat'), 
								'orders.total' => Input::get('total')]);
					return redirect('checkout');
				}
			}
		}
		else
		{
			Session::put(['orders.delivery_date' => $current_date, 
								'orders.delivery_type' => Input::get('delivery_type'), 
								'orders.subtotal' => Input::get('subtotal'), 
								'orders.vat' => Input::get('vat'), 
								'orders.total' => Input::get('total')]);
			return redirect('checkout');
		}
	}
	
	public function checkout()
    {
		if(!Session::has('orders'))
		{
			return redirect('/');
		}
		else
		{
			$address_books = [];
			$default_address = [];
			$branch = [];
			if(Session('orders.delivery_type') == 'p')
			{
				$branch = DB::table('branches')
							->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
							->SelectRaw($this->prefix.'branches.*,'.$this->prefix.'branch_description.branch_name as branch')
							->where('branches.id', Session('orders.branch_id'))
							->first();
			}
			if($this->auth->id() != '')
			{
				$default_address = DB::table('user_addressbook')->where('customer_id', $this->auth->id())->where('default_address', 1)->first();
				$address_books = DB::table('user_addressbook')->where('customer_id', $this->auth->id())->get();
			}
			return view('checkout', array('address_books' => $address_books, 'default_address' => $default_address, 'branch' => $branch));
		}
	}
	
	public function changedefault_address()
	{
		$user_id = $this->auth->id();
		$address_id = Input::get('address_id');
		DB::table('user_addressbook')->where('customer_id', $user_id)->update(['default_address' => 0]);
		DB::table('user_addressbook')->where('id', $address_id)->update(['default_address' => 1]);
		
		$default_address = DB::table('user_addressbook')->where('id', $address_id)->first();
		
		$address = explode(',', $default_address->address);
		$result = '';
		for($i=0; $i<count($address); $i++)
		{
			$comma = ($i == 0) ? '<br>' : ',<br>';
			$result .= $comma.$address[$i]; 
		}
		$result .= '<input type="hidden" name="address_id" value="'.$default_address->id.'">';
		return $result;
	}
	
	public function payment()
	{	
		$total = Session('orders.total');
		$payment_type = Input::get('payment_type');
		$user_id = Input::get('user_id');
		$address_id = Input::get('address_id');
		Random :
		$key = str_random(16);
		$addresskey = str_random(16);
		
		if($user_id == '')
		{
			 $valid = Validator::make(Input::all(), 
					['first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'mobile' => 'required|digits_between:9,13|unique:users,mobile',
                    'delivery_address' => 'required',
                    'terms' => 'required']);
            if($valid->fails())
			{
				return redirect('/checkout')->WithInput(Input::All())->with('error', $valid->errors());
			}
			else
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
				$user_id = $this->user->id;
				
				$address_key_exits = DB::table('user_addressbook')->where('address_key', $addresskey)->count();
				if ($address_key_exits) { goto Random; }
				DB::table('user_addressbook')->insert(['customer_id' => $user_id, 'address' => Input::get('delivery_address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
				$address_id = DB::getPdo()->lastInsertId();
			}
		}
		elseif($user_id != '' && $address_id == '' && Session('orders.delivery_type') == 'd')
		{
			 $valid = Validator::make(Input::all(), 
					['delivery_address' => 'required']);
            if($valid->fails())
			{
				return redirect('/checkout')->WithInput(Input::All())->with('error', $valid->errors());
			}
			else
			{
				$address_key_exits = DB::table('user_addressbook')->where('address_key', $addresskey)->count();
				if ($address_key_exits) { goto Random; }
				DB::table('user_addressbook')->insert(['address_key' => $addresskey, 'customer_id' => $user_id, 'address' => Input::get('delivery_address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude'), 'created_at' => date('Y-m-d H:i:s')]);
				$address_id = DB::getPdo()->lastInsertId();
			}
		}
		
				$user = DB::table('users')->where('id', $user_id)->first();
				$user_addressbook = DB::table('user_addressbook')->where('id', $address_id)->first();
		        
				InvoiceRandom:
				$invoice = mt_rand(100000,999999);

				OrderRandom:
				$order_key = mt_rand(100000,999999);

				$invoice_exits = DB::table('orders')->where('invoice_number', $invoice)->count();
				if ($invoice_exits) { goto InvoiceRandom; }

				$order_key_exits = DB::table('orders')->where('order_key', $order_key)->count();
				if ($order_key_exits) { goto OrderRandom; }
				
				if(Session('orders.delivery_type') == 'd')
				{
					$address = DB::table('user_addressbook')->where('id', $address_id)->first();
					$this->order->address_key = $address->address_key;
					$this->order->address_id = $address_id;
				}
				
				$otp = mt_rand(100000,999999);
				$this->order->order_key = $order_key;
				$this->order->sub_total = Session('orders.subtotal');
				$this->order->vat = Session('orders.vat');
				$this->order->vat_percentage = $this->config_data['vat'];
				$this->order->order_total = Session('orders.total');
				$this->order->customer_key = $user->customer_key;
				$this->order->customer_id = $user->id;
				$this->order->customer_first_name = $user->first_name;
				$this->order->customer_last_name = $user->last_name;
				$this->order->customer_email = $user->email;
				$this->order->customer_mobile = $user->mobile;
				$this->order->branch_id = Session('orders.branch_id');
				$this->order->delivery_type = Session('orders.delivery_type');
				$this->order->order_datetime = 	Session('orders.delivery_date');
				$this->order->delivery_fee = 	Session('orders.delivery_fee');											
				$this->order->otp = $otp;
				$this->order->invoice_number = $invoice;
				$this->order->order_status = 'p';
				$this->order->payment_type = $payment_type;
				$this->order->created_by = 'u';
				
				$this->order->save();
				$order_id = $this->order->id;
				$overall_price = 0;
				
				$items = Cart::content(); //echo '<pre>'; print_r($items); exit;
				$products = [];
				foreach($items as $item)
				{
					$notes = ($item->options->notes != '') ? $item->options->notes : '';
					DB::table('order_itemdetails')->insert(['order_id' => $order_id, 'item_id' => $item->id, 'quantity' => $item->qty, 'price' => $item->price, 'is_ingredients' => $item->options->is_ingredients, 'is_size' => $item->options->is_size, 'is_execlusion' => $item->options->is_execlusion, 'notes' => $notes]);
					
					$order_item_id = DB::getPdo()->lastInsertId();
					if($item->options->is_ingredients == 1)
					{
						$order_item_id = DB::getPdo()->lastInsertId();
						for($i=0; $i<count($item['options']['ingredientlist']); $i++)
						{
							DB::table('order_ingredientdetails')->insert(['order_itemdetails_id' => $order_item_id, 'order_id' => $order_id, 'item_id' => $item->id, 'ingredientlist_id' => $item['options']['ingredientlist_id'][$i], 'price' => $item['options']['ingredient_price'][$i]]);
						}
					}
					if($item->options->is_size == 1)
					{
						DB::table('order_item_sizedetails')->insert(['order_item_id' => $order_item_id, 'order_id' => $order_id, 'item_id' => $item->id, 'size_id' => $item['options']['size_id'], 'price' => $item['options']['size_price']]);
					}
					if($item->options->is_execlusion == 1)
					{
						for($i=0; $i<count($item->options->execlusion_id); $i++)
						{
							DB::table('order_execlusion_details')->insert(['item_details_id' => $order_item_id, 'order_id' => $order_id, 'item_id' => $item->id, 'execlusion_id' => $item['options']['execlusion_id'][$i]]);
						}
					}

					
					$foodicsitem = DB::table('vendor_items')->select('foodics_id')->where('id', $item->id)->first();
					if($foodicsitem->foodics_id != '')
					{
						$item_size = DB::table('item_size')->select('foodics_id')->where('id', $item['options']['size_id'])->first();
						$notes = ($item->options->notes != '') ? $item->options->notes : 'no notes';
						$price = $item->price + $item->options->size_price;
						$options = [];
						$execlusions = [];
						$ingredient_final_price = 0;
						if($item->options->is_ingredients)
						{
							for($i=0; $i<count($item->options->ingredientlist); $i++)
							{
								$ingredientlist = DB::table('vendor_item_ingredientlist')->select('foodics_id')->where('item_id',  $item->id)->where('item_ingredientlist_id',  $item['options']['ingredientlist_id'][$i])->first();
								$ingredient_final_price = $item['options']['ingredient_price'][$i] * $item->qty;
								$options[] = array
												(
													'hid' => $ingredientlist->foodics_id,
													'original_price' => $item['options']['ingredient_price'][$i],
													'quantity' => $item->qty,
													'final_price' => $ingredient_final_price	
												);
							}
						}
						$final_price = (($price * $item->qty) + $ingredient_final_price);
						$overall_price += $final_price;
						if($item->options->is_execlusion)
						{
							for($i=0; $i<count($item->options->execlusions); $i++)
							{
								$execlusion = DB::table('vendor_item_execlusions')->select('foodics_id')->where('item_id',  $item->id)->where('execlusion_id',  $item['options']['execlusion_id'][$i])->first();
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
											'quantity' => 1,
											'removed_ingredients' => $execlusions,
											'final_price' => $final_price,
											'options' => $options
										);
					}
				}
				$foodics_delivery_fee = Session('orders.delivery_fee');
				if(count($products))
				{
					$vat_tax = (($overall_price * $this->config_data['vat']) / 100); 
					$total_price = $overall_price + Session('orders.delivery_fee') + $vat_tax;
					$type = (Input::get('order_type') == 'd') ? 4 : 3;
					$access_token = $this->config_data['foodics_access_token'];
					$delivery_address = [];
					if($type == 4)
					{
						$delivery_address = array(
							        "address" => $user_addressbook->address,
							        "delivery_zone_hid" => '_96765a78',
							        "notes" => '',
							        "latitude" => $user_addressbook->latitude,
							        "longitude" => $user_addressbook->longitude
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
							    "due_time" => date('Y-m-d H:i:s', strtotime(Session('orders.delivery_date'))),
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
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/orders");
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
				if(Input::get('subscribe') == 1)
				{
					$email_count = DB::table('newsletter_subscribers')->where('email', $user->email)->count();
					if($email_count == 0)
					{
						DB::table('newsletter_subscribers')->insert(['user_id' => $user->id, 'name' => $name, 'email' => $user->email]);
					} 
						
				}

				Session::put(['payment.order_id' => $order_id, 'payment.total' => Session('orders.total'), 'payment.email' => $user->email]);
				Cart::destroy();
				Session::forget('orders');
				$mobile = valid_mobile($user->mobile);
				if($payment_type == 0)
				{
					$subject = 'OTP - Shuneez';
					$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
					sendSMS(2, $mobile, $msg);
					Session::put(['mobile' => $mobile, 'order_id' => $order_id]);
					return redirect('/verify_otp');
				}
				else
				{
					return redirect('payfort');
				}
	}
	
	public function verification()
	{
		if(Session::has('mobile'))
		{
			return view('verification');
		}
		else
		{
			return redirect('/');
		}
	}
	
	public function saveaddress()
	{
		$address = Input::get('address');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		Random :
		$addresskey = str_random(16);
		$address_key_exits = DB::table('user_addressbook')->where('address_key', $addresskey)->count();
		if ($address_key_exits) { goto Random; }
		
		DB::table('user_addressbook')->where('customer_id', $this->auth->id())->update(['default_address' => 0]);
		DB::table('user_addressbook')->insert(['customer_id' => $this->auth->id(), 'address_key' => $addresskey, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'default_address' => 1, 'created_at' => date('Y-m-d H:i:s')]);
		return 1;
	}
	
	public function resendotp()
	{
		$order_id = Input::get('order_id');
		$mobile = Input::get('mobile');
		
		$otp = mt_rand(100000,999999);
		DB::table('orders')->where('id', $order_id)->update(['otp' => $otp]);
		
		$msg = trans('frontend.OTP MESSAGE').' : '.$otp.' '.trans('frontend.Shuneez'); 
		sendSMS(2, $mobile, $msg);
		
		return 1;
	}
	
	public function verifyotp()
	{
		$otp = Input::get('otp');
		$order_id = Input::get('order_id');
		
		$verify = DB::table('orders')->where('id', $order_id)->where('otp', $otp)->first();
		if(count($verify))
		{
			DB::table('orders')->where('id', $order_id)->where('otp', $otp)->update(['is_verified' => 1, 'order_status' => 'c', 'confirmed_at' => date('Y-m-d H:i:s')]);
			$mobile = valid_mobile($verify->customer_mobile);
			$msg = 'Hi, Your order id - '.$verify->invoice_number.' has been confirmed.';
			sendSMS(2, $mobile, $msg);
			return redirect('/')->with('success', 'Your order placed successfully');
			
		}
		else
		{
			return Redirect::back()->withInput(Input::all())->with('error', 'Invalid verification code');
		}
			
	}
	
	public function autocomplete_branch()
	{
		$date = getdate();
		$day = strtolower($date['weekday']);
		$time = date('H:i:s'); //echo $time; exit;
		$keyword = Input::get('term');
		$branchAvailables = DB::table('branches')
							->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
							->select('branch_description.branch_name')
							->where(function($query) use($keyword)
								{
									if($keyword != '')
									{
										$query->where('branch_description.branch_name', 'like', '%'.$keyword.'%')
												->OrWhere('street', 'like', '%'.$keyword.'%')
												->OrWhere('street', 'city', '%'.$keyword.'%')
												->OrWhere('country', 'like', '%'.$keyword.'%');
									}
								})
							->where('branch_description.language', $this->current_language)
							->get();
		if(count($branchAvailables))
		{
			$data = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->join('vendor_timeslot', 'branches.id', '=', 'vendor_timeslot.branch_id')
						->SelectRaw(DB::getTablePrefix().'branches.id,'.DB::getTablePrefix().'branch_description.branch_name as branch')
						->where(function($query) use($keyword)
						{
							$query->where('branch_description.branch_name', 'like', '%'.$keyword.'%')
									->OrWhere('branches.street', 'like', '%'.$keyword.'%')
									->OrWhere('branches.city', 'like', '%'.$keyword.'%')
									->OrWhere('branches.meta_keywords', 'like', '%'.$keyword.'%');
						})
						->where('vendor_timeslot.working_day', $day)
						->where('vendor_timeslot.start_time', '<=', $time)
						->where('vendor_timeslot.close_time', '>=', $time)
						->where('branch_description.language', $this->current_language)
						->groupby('branch_description.branch_id')
						->get();
			$branch = [];
			if(count($data) > 0)
			{
				foreach ($data as $row) 
				{
					$branch['value'] = $row->branch;
					$branch['id'] = $row->id;
					$row_set[] = $branch;
				}
			}
			else
			{
				$row_set[] = $branchAvailable->branch_name.' - '.trans('frontend.Closed now');
			}
		}
		else
		{
			$row_set[] = trans('messages.No data found');
		}
		return json_encode($row_set);
	}
	
	public function payfort()
	{
		include('PayfortIntegration.php');
		$objFort = new PayfortIntegration();
		return view('payfort/index', array('objFort' =>$objFort));
	}

	public function update_orderstatus()
	{
		$order_id = Input::get('order_id');
		$status = Input::get('status');
		DB::table('orders')->where('order_number', $order_id)->update(['order_status', $status]);
		$order = DB::table('orders')->where('order_number', $order_id)->first();
		$user = DB::table('users')->where('customer_key', $order->customer_key)->first();
		$name = $order->customer_first_name.' '.$order->customer_last_name;
		$email = $order->customer_email;
		$subject = 'Order Delivery -'.$order->invoice_number;
		$msg = trans('frontend.Hello').' '.$name.' '.trans('frontend.Out Delivery Message').' '.trans('frontend.Shuneez');
		$mobile = valid_mobile($order->customer_mobile);
		sendSMS(2, $mobile, $msg);
		sendPushNotification($msg, $user->device_id);

		$response = array('status' => "success", 'msg' => 'Order status updated successfully');
		return json_encode($response);
	}
	
	
}
