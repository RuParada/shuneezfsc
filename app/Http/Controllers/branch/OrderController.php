<?php namespace App\Http\Controllers\branch;

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
use App\User;
use App\Ingredient;
use App\Branch;
use App\Category;
use App\Order;
use App\Deliveryboy;
use URL;
use Cart;
use View;
use Redirect;
use App\Execlusion;

class OrderController extends Controller {

	public function __construct(Guard $auth, User $user, Order $order, Ingredient $ingredient, Branch $branch, Category $category, Deliveryboy $deliveryboy, Execlusion $execlusion)
	{
		$this->middleware('branchauth');
		$this->auth = $auth;
		$this->user = $user;
		$this->ingredient = $ingredient;
		$this->order = $order;
		$this->branch = $branch;
		$this->category = $category;
		$this->deliveryboy = $deliveryboy;
		$this->prefix = DB::getTablePrefix();
		$this->execlusion = $execlusion;
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	    $settings = DB::table('settings')->get();
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		View::share (['config_data'=> $config_data]);
		$this->config_data = $config_data;
	}
	
	/**************Get Orders******************/

	public function getorders()
	{
		Session::forget('flag');
		Cart::destroy();
		$orders = $this->order->getbranch_orders(Session('branch_id'));
		
		return view('branch/orders', array('orders' => $orders));
	}

	public function createorder_form()
	{
		$users = DB::table('users')->orderby('first_name', 'asc')->get();
		$categories = $this->category->getcategories();
		$ingredients = $this->ingredient->getingredients();
		$deliveryboys = $this->deliveryboy->getbranch_deliveryboys(Session('branch_id'));
		$branch = $this->branch->find(Session('branch_id'));
		return view('branch/createorder', array('users' => $users, 'ingredients' => $ingredients, 'categories' => $categories, 'deliveryboys' => $deliveryboys, 'branch' => $branch));
	}

	public function getcustomer()
	{
		$id = Input::get('customer_id');
		$user = DB::table('users')->where('id', $id)->first();
		$result = array('user' => $user);
		return json_encode($user);
	}
	
	public function createorder()
	{
		$customer_id = Input::get('customer_id');
		$items = Cart::content();
		$products = [];
		if($customer_id != '')
		{
			$valid = Validator::make(Input::all(),
									['customer_first_name' => 'required',
									 'customer_email' => 'email|unique:users,email,'.$customer_id,
									 'customer_mobile' => 'required|numeric|unique:users,mobile,'.$customer_id,
									 'address' => 'required',
									 'ordertime' => 'required',
									 'orderdate' => 'required',
									 'branch' => 'required',
									 ]);
		}
		else
		{
			$valid = Validator::make(Input::all(),
									['customer_first_name' => 'required',
									 'customer_email' => 'email|unique:users,email',
									 'customer_mobile' => 'required|numeric|unique:users,mobile',
									 'address' => 'required',
									 'ordertime' => 'required',
									 'orderdate' => 'required',
									 'branch' => 'required',
									]);
		}


		if($valid->fails())
		{
			return redirect('branch/createorder')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			if(count($items) == 0)
			{
				return redirect('branch/createorder')->withInput(Input::all())->with('item_error', trans('messages. Please select atleast one item to cart'));
			}
			else
			{
				Random :
					$key = str_random(16);
					$address_key = str_random(16);
					
				if($customer_id == '')
				{
					$key_exits = DB::table('users')->where('customer_key', $key)->count();
					if ($key_exits) { goto Random; }
					$this->user->customer_key = $key;
					$this->user->first_name = Input::get('customer_first_name');
					$this->user->last_name = Input::get('customer_last_name');
					$this->user->email = Input::get('customer_email');
					$this->user->mobile = Input::get('customer_mobile');
					$this->user->status = 1;
					$this->user->password = bcrypt(str_random(6));				
					
					$this->user->save();
					
					$customer_id = $this->user->id;
				}
					$user = DB::table('users')->where('id', $customer_id)->first();
					$address_key = str_random(16);
					
					$key_exits = DB::table('user_addressbook')->where('address_key', $address_key)->count();
					if ($key_exits) { goto Random; }
					
					DB::table('user_addressbook')->insert(['address_key' => $address_key, 'customer_id' => $customer_id, 'address' => Input::get('address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
					$address_id = DB::getPdo()->lastInsertId();
					$user_addressbook = DB::table('user_addressbook')->where('id', $address_id)->first();
					$branch = DB::table('branches')->where('id', Input::get('branch'))->first();
					
					$i = 1;
					$ingredient_total = 0;
					$sub_total = 0;
					foreach($items as $item)
					{
					  $ingredient_subtotal = 0; 
					  if($item->options->is_ingredients) 
					  { 
						for($i=0; $i<count($item->options->ingredientlist); $i++) 
						{ 
							$ingredient_subtotal += $item->options->ingredient_price[$i];
						}
					  }
					  $ingredient_total += $ingredient_subtotal*$item->qty;

					  $sub_total += ($item->options->is_size) ? $item->qty*($item->price + $item->options->size_price) : $item->qty*$item->price;  
				    }
					
					$sub_total = $sub_total + $ingredient_total;
					$delivery_fee = (Input::get('order_type') == 'd') ? $branch->delivery_fee : 0;
					$vat_tax = ((($sub_total + $ingredient_total) * $this->config_data['vat']) / 100); 
					$total = $sub_total + $delivery_fee + $ingredient_total + $vat_tax;

					InvoiceRandom:
					$invoice = mt_rand(100000,999999);

					OrderRandom:
					$order_key = mt_rand(100000,999999);

					$invoice_exits = DB::table('orders')->where('invoice_number', $invoice)->count();
					if ($invoice_exits) { goto InvoiceRandom; }

					$order_key_exits = DB::table('orders')->where('order_key', $order_key)->count();
					if ($order_key_exits) { goto OrderRandom; }
					
					$this->order->invoice_number = $invoice;
					$this->order->order_key = $order_key;
					$this->order->order_total = $total;
					$this->order->sub_total = $sub_total;
					$this->order->delivery_fee = (!Input::get('send_delivery_fee')) ? $delivery_fee : 0;
					$this->order->vat_percentage = $this->config_data['vat'];
					$this->order->vat = $vat_tax;
					$this->order->customer_key = $user->customer_key;
					$this->order->customer_id = $user->id;
					$this->order->customer_first_name = $user->first_name;
					$this->order->customer_last_name = $user->last_name;
					$this->order->customer_email = $user->email;
					$this->order->customer_mobile = $user->mobile;
					$this->order->branch_id = Input::get('branch');
					$this->order->delivery_type = Input::get('order_type');
					$this->order->notes = Input::get('notes');
					$order_date = Input::get('orderdate');
					$order_time = Input::get('ordertime');
					$order_time = date("H:i", strtotime($order_time));
					$order_datetime = $order_date." ".$order_time.":00";
					$this->order->order_datetime = 	date('Y-m-d H:i:s', strtotime($order_datetime));	

					$this->order->address_key = $address_key;
					$this->order->address_id = $address_id;
					$this->order->order_status = (Input::get('deliveryboy') != '') ? 'as' : 'a';
					$this->order->created_by = 'a';
					
					if(Input::get('order_type') == 'd')
					{
						$this->order->deliveryboy_id = Input::get('deliveryboy');
					}
					
					$this->order->save();
					$order_id = $this->order->id;
					$overall_price = 0;
					foreach($items as $item)
					{
						DB::table('order_itemdetails')->insert(['order_id' => $order_id, 'item_id' => $item->id, 'quantity' => $item->qty, 'price' => $item->price, 'is_ingredients' => $item->options->is_ingredients, 'is_size' => $item->options->is_size, 'is_execlusion' => $item->options->is_execlusion]);
						$order_item_id = DB::getPdo()->lastInsertId();
						if($item->options->is_ingredients == 1)
						{
							for($i=0; $i<count($item->options->ingredientlist); $i++)
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
							$final_price = ($item->options->is_ingredients) ? (($item->price + $item->options->size_price) * $item->qty) + $item->options->ingredient_total : (($item->price + $item->options->size_price) * $item->qty);
							$overall_price += $final_price;
							$options = [];
							$execlusions = [];
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
												'quantity' => $item->qty,
												'removed_ingredients' => $execlusions,
												'final_price' => $final_price,
												'options' => $options
											);
						}
					}
					$foodics_delivery_fee = (Input::get('send_delivery_fee')) ? $delivery_fee : 0;
					if(count($products))
					{
						$vat_tax = (($overall_price * $this->config_data['vat']) / 100); 
						$total_price = $overall_price + $delivery_fee + $vat_tax;
						$type = (Input::get('order_type') == 'd') ? 4 : 3;
						$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
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
								    "due_time" => date('Y-m-d H:i:s', strtotime($order_datetime)),
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
						DB::table('orders')->where('id', $order_id)->update(['foodics_id' => $details->order_hid]);
					}
					
					$mobile = valid_mobile($user->mobile);
					//echo $mobile; exit;
					if(Input::get('order_type') == 'd')
					{
						if(Input::get('deliveryboy') != '')
						{
							DB::table('deliveryboy_request')->insert(['order_id' => $order_id, 'deliveryboy_id' => Input::get('deliveryboy'), 'status' => 'n', 'created_at' => date('Y-m-d H:i:s')]);
						}
						$url = URL::to('update_address/'.$address_key.'/'.$order_key);
						if(Input::get('send_sms') == 1)
						{
							$msg = trans('frontend.Hello').' '.$user->first_name.$user->last_name.' '.trans('frontend.Delivery Message').' '.$url.' '.trans('frontend.Shuneez');
						}
						else
						{
							$msg = trans('frontend.Hello').' '.$user->first_name.$user->last_name.' '.trans('frontend.Confirmation Message').' '.trans('frontend.Shuneez');
						}
						sendSMS(2, $mobile, $msg);
						 
					}
					else
					{
						$msg = trans('frontend.Hello').' '.$user->first_name.$user->last_name.' '.trans('frontend.Confirmation Message').' '.trans('frontend.Shuneez');
						sendSMS(2, $mobile, $msg);
					}
					Cart::destroy();
					if(Input::get('deliveryboy') != '')
					{
						$deliveryboy = $this->deliveryboy->getdeliveryboy(Input::get('deliveryboy'));
						$device_id = $deliveryboy->device_token;
						$branch = $this->branch->getbranch(Input::get('branch'));
						$message = 'Hi '.$deliveryboy->deliveryboy_name.' Admin assigned a new Order for you. The branch details are, Shop Name: '.$branch->branch.', Shop Email : '.$branch->email.'. Thanks & Regards, Shuneez team';
						sendMessage($message, $device_id);
					}
					return redirect('branch/orders')->with('success', trans('messages.Order Placed'));
				}
		}
	}
	

	/*Edit Order function */
	public function updateorder()
	{
		//echo "<pre>";print_r(Input::get());
		$order_id = Input::get('order_id');
		$customer_id = Input::get('customer_id');
		$items = Cart::content();
		
		$valid = Validator::make(Input::all(),
									['customer_first_name' => 'required',
									 'customer_email' => 'email|unique:users,email,'.$customer_id,
									 'customer_mobile' => 'required|numeric|unique:users,mobile,'.$customer_id,
									 'address' => 'required',
									 'ordertime' => 'required',
									 'orderdate' => 'required',
									 'branch' => 'required']);
		if($valid->fails())
		{	
			return redirect('branch/editorder/'.$order_id)->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$address_id = Input::get('address_id');
			DB::table('user_addressbook')->where('id', $address_id)->update(['customer_id' => $customer_id, 'address' => Input::get('address'), 'latitude' => Input::get('latitude'), 'longitude' => Input::get('longitude')]);
			$branch = DB::table('branches')->where('id', Input::get('branch'))->first();
			
			$i = 1;
			$ingredient_total = 0;
			foreach($items as $item)
			{
			  $ingredient_subtotal = 0; 
			  if($item->options->is_ingredients) 
			  { 
				for($i=0; $i<count($item->options->ingredientlist); $i++) 
				{ 
					$ingredient_subtotal += $item->options->ingredient_price[$i];
				}
			  }
			  $ingredient_total += $ingredient_subtotal*$item->qty; 
			}
			
			$user = DB::table('users')->where('id', $customer_id)->first();
			$sub_total = Cart::total() + $ingredient_total;
			$delivery_fee = (Input::get('order_type') == 'd') ? $branch->delivery_fee : 0;
			$vat_tax = (((Cart::total() + $ingredient_total) * $this->config_data['vat']) / 100); 
			$total = Cart::total() + $delivery_fee + $ingredient_total + $vat_tax;
			
			$this->order->order_total = $total;
			$this->order->sub_total = $sub_total;
			$this->order->delivery_fee = $delivery_fee;
			$this->order->vat_percentage = $this->config_data['vat'];
			$this->order->vat = $vat_tax;
			$this->order->customer_key = $user->customer_key;
			$this->order->customer_id = $user->id;
			$this->order->customer_first_name = Input::get('customer_first_name');
			$this->order->customer_last_name = Input::get('customer_last_name');
			$this->order->customer_email = Input::get('customer_email');
			$this->order->customer_mobile = Input::get('customer_mobile');
			$this->order->branch_id = Input::get('branch');
			$this->order->delivery_type = Input::get('order_type');
			$this->order->notes = Input::get('notes');
			$order_date = Input::get('orderdate');
			$order_time = Input::get('ordertime');
			$order_time = date("H:i", strtotime($order_time));
			$order_datetime = $order_date." ".$order_time.":00";
			$this->order->order_datetime = 	date('Y-m-d H:i:s', strtotime($order_datetime));											
			$this->order->address_id = $address_id;
			$this->order->order_status = (Input::get('deliveryboy') != '') ? 'as' : 'a';
			
			if(Input::get('order_type') == 'd')
			{
				$this->order->deliveryboy_id = Input::get('deliveryboy');
			}
			
			DB::table('orders')->where('id', $order_id)->update($this->order['attributes']);
			
			DB::table('order_itemdetails')->where('order_id', $order_id)->delete();
			DB::table('order_ingredientdetails')->where('order_id', $order_id)->delete();
			DB::table('order_item_sizedetails')->where('order_id', $order_id)->delete();
			DB::table('order_execlusion_details')->where('order_id', $order_id)->delete();
			foreach($items as $item)
			{
				DB::table('order_itemdetails')->insert(['order_id' => $order_id, 'item_id' => $item->id, 'quantity' => $item->qty, 'price' => $item->price, 'is_ingredients' => $item->options->is_ingredients]);
				if($item->options->is_ingredients == 1)
				{
					$order_item_id = DB::getPdo()->lastInsertId();
					for($i=0; $i<count($item->options->ingredientlist); $i++)
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
			}
			if(Input::get('deliveryboy') != '')
			{
				DB::table('deliveryboy_request')->where('id', $order_id)->update(['order_id' => $order_id, 'deliveryboy_id' => Input::get('deliveryboy'), 'status' => 'n']);
				 
			}
			Cart::destroy();

			return redirect('branch/orders')->with('success', trans('messages.Order Updated Successfully'));
		}
	}
	public function getbranch_deliveryboys()
	{
		$branch_id = Input::get('branch_id');
		$deliveryboys = "<option value=''></option>";
		$data = $this->deliveryboy->getbranch_deliveryboys($branch_id);
		if(count($data))
		{
			foreach ($data as $row) 
			{
				$deliveryboys .= "<option value='".$row->id."'>".ucfirst($row->name)."</option>";
			}
		}
		$result = array('deliveryboys' => $deliveryboys);
		return json_encode($result);
	}
	
		/* To get branch details for the order_type
	*/
	public function getbranch_delivery()
	{
		$delivery_type = Input::get('order_type');
		$branches = "<option value=''></option>";
		$data = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->SelectRaw(DB::getTablePrefix().'branches.id,'.DB::getTablePrefix().'branch_description.branch_name as branchname')
						->where('branches.delivery_type', $delivery_type)
						->orWhere('branches.delivery_type', 'b')
						->where('branches.is_delete', 0)
						->where('branch_description.language', $this->current_language)
						->groupBy('branch_description.branch_id')
						->get();
		if(count($data))
		{
			foreach ($data as $row) 
			{
				$branches .= "<option value='".$row->id."'>".ucfirst($row->branchname)."</option>";
			}
		}
		$result = array('branches' => $branches);
		return json_encode($result);
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
		
	   $ingredien = view('branch/selectingredient', array('ingredients' => $data, 'vendor_item' => $vendor_item, 'size_list' => $size_list, 'execlusions' => $execlusions));
	   echo $ingredien->render();
	  
	}
	
	public function getcategory_items()
	{
		$category_id = Input::get('category_id');
		$items = '';
		$data = DB::table('vendor_items')
				->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
				->SelectRaw($this->prefix.'vendor_items.*,'.$this->prefix.'vendor_item_description.item_name as item')
				->where('vendor_items.category_id', $category_id)
				->where('vendor_item_description.language', $this->current_language)
				->get();
		if(count($data) > 0)
		{
			$items .= '<div class="col-md-12">';
			foreach($data as $row)
			{
				$image = ($row->image != '') ? 'assets/uploads/vendor_items/'.$row->image : 'assets/admin/images/not-found.png';
				$link = ($row->is_ingredients == 1) ? '<a href="javascript:selectingredient('.$row->id.');" class="get_ingredient">' : '<a href="#cartbox" onclick="addtocart('.$row->id.');">';
				$items .= '<div class="col-sm-4">
							
							<label for="upload_img" class="upload_lbl">
							'.$link.' <img src="'.URL::to($image).'" class="roundedimg"></a></label>
							<label class="full_row" style="height: 36px;" for="image">'.$row->item.'</label>
							<input type="hidden" name="item_id" value="'.$row->id.'"></div>';

			}
			$items .= '</div>';
		}
		$result = array('items' => $items);
		return json_encode($result);
	}
	
	public function addtocart()
    {
		$items = '<div class="pro_title_1"><span class="pro_title_txt_1">'.trans("messages.Item Name").'<p style="float:right;">'.trans("messages.Quantity").'</p> </span></div>
				  <div class="pro_price_1"><span class="pro_title_txt_2">'.trans("messages.Price").'</span></div>';
        $id = Input::get('id');
        $quantity = 1;
        $item =  DB::table('vendor_items')
					->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
					->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item')
					->where('vendor_item_description.language', $this->current_language)
					->where('vendor_items.id', $id)
					->first();
        Cart::add(array('id' => $item->id, 'name' => $item->item, 'qty' => $quantity, 'price' => $item->price, 'options' => array('image' => $item->image, 'is_ingredients' => $item->is_ingredients, 'is_size' => 0, 'is_execlusion' => 0)));
        
        
       /* $products = Cart::content();
		if(count($products) > 0)
		{
			foreach($products as $product)
			{
				$items .= '<div class="pro_title_1"><span >'.$product->name.'<p style="float:right;">
				<button type="button" class="b_plu" onclick="updateqty(\''.$product->rowid.'\',\'add\');">+</button>
				<input type="text" id="qty_'.$product->rowid.'" style="width:30px;" value='.$product->qty.'>
                <button type="button" class="b_plu" onclick="updateqty(\''.$product->rowid.'\',\'add\');">-</button></p> </span></div>
				<div class="pro_price_1"><span class="pro_title_txt_2">'.$product->qty*$product->price.'</span></div>';
			}
		}
		$items .= '<div class="pro_title_1"><span class="pro_title_txt_1"><p style="float:right;">Total</p> </span></div>
				   <div class="pro_price_1"><span class="pro_title_txt_2">'.Cart::total().'</span></div>';
        $result = array('items' => $items);
		return json_encode($result);*/
		return 1;
    } 
	
	public function additem()
	{
		$items = '<div class="pro_title_1"><span class="pro_title_txt_1">'.trans("messages.Item Name").'<p style="float:right;">'.trans("messages.Quantity").'</p> </span></div>
				  <div class="pro_price_1"><span class="pro_title_txt_2">'.trans("messages.Price").'</span></div>';
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
		//print_r($execlusionlist); exit;
		Cart::add(array('id' => $item->id, 'name' => $item->item, 'qty' => $quantity, 'price' => $item->price, 'options' => array('image' => $item->image, 'notes' => Input::get('message'), 'is_ingredients' => $is_ingredients, 'ingredientlist' => $ingredient, 'ingredientlist_id' => $ingredient_id, 'ingredient_price' => $ingredient_price, 'ingredient_total' => $ingredient_total, 'is_size' => $is_size, 'size' => $size_name, 'size_id' => $size_id, 'size_price' => $size_price, 'is_execlusion' => $is_execlusion, 'execlusions' => $execlusion, 'execlusion_id' => $execlusion_id)));
        
		return 1;
	}

	/* ADD AND REMOVE ITEM QUANTITY */
	public function add_remove_quantity()
	{
		$type = Input::get('type');
		$rowid 	= Input::get('rowid');
	    $qty 	= Input::get('quantity');
		if($type == 'edit')
		{
			$item = Cart::get($rowid);
			$this->updateqty(Input::get('order_id'), $item->id, $qty);
		}
		Cart::update($rowid, array('qty' => $qty));
	    return 1;
	}

	/* DELETE CART ITEM */

	public function delete_cartitem()
	{
		$type = Input::get('type');
		$rowid = Input::get('rowid');
		if($type == 'edit')
		{
			$item = Cart::get($rowid);
			$this->delete_item(Input::get('order_id'), $item->id);
		}
		Cart::remove($rowid);
	    return 1;
		 
	}

	/* GET ORDER DETAILS FOR EDIT */
 	public function editorder($id) 
 	{
		$data = [];
        $order = DB::table('orders')
				->leftJoin('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
				->leftJoin('branch_description', 'orders.branch_id', '=', 'branch_description.branch_id')
				->SelectRaw(DB::getTablePrefix().'orders.*,'.DB::getTablePrefix().'user_addressbook.address,'.DB::getTablePrefix().'user_addressbook.latitude,'.DB::getTablePrefix().'user_addressbook.longitude,'.DB::getTablePrefix().'branch_description.branch_name')			
				->where('orders.id', $id)
				->first();
		
		$items = DB::table('order_itemdetails')
				->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
				->join('vendor_items', 'order_itemdetails.item_id', '=', 'vendor_items.id')
				->select('order_itemdetails.id', 'order_itemdetails.item_id', 'vendor_item_description.item_name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients', 'vendor_items.image', 'order_itemdetails.is_execlusion', 'order_itemdetails.is_size')
				->where('order_itemdetails.order_id', $id)
				->where('vendor_item_description.language', $this->current_language)
				->get();
		  $i = 0;
		  foreach($items as $item)
		  {
			$data['items'][$i] = DB::table('order_itemdetails')
								->join('vendor_item_description', 'order_itemdetails.item_id', '=', 'vendor_item_description.item_id')
								->select('order_itemdetails.id', 'vendor_item_description.item_name','order_itemdetails.price','order_itemdetails.quantity','order_itemdetails.is_ingredients')
								->where('order_itemdetails.item_id', $item->item_id)
								->where('order_itemdetails.order_id', $id)
								->where('vendor_item_description.language', $this->current_language)
								->first();
			if($item->is_size)
			{
				$sizedetails = DB::table('order_item_sizedetails')
											->join('item_size_description', 'order_item_sizedetails.size_id', '=', 'item_size_description.item_size_id')
											->select('size_name as size', 'price as size_price', 'item_size_id as size_id')
											->where('language', $this->current_language)
											->where('order_item_id', $data['items'][$i]->id)
											->first();

				$size_name = $sizedetails->size;
				$size_id = $sizedetails->size_id;
				$size_price = $sizedetails->size_price;
			}
			else
			{
				$size_name = '';
				$size_id = '';
				$size_price = '';
			}
			$execlusion = [];
			$execlusion_id = [];
			if($item->is_execlusion)
			{
				$execlusions = DB::table('order_execlusion_details')
											->join('execlusion_description', 'order_execlusion_details.execlusion_id', '=', 'execlusion_description.execlusion_id')
											->select('execlusion_name', 'execlusion_description.execlusion_id')
											->where('language', $this->current_language)
											->where('item_details_id', $data['items'][$i]->id)
											->get();
				foreach($execlusions as $row)
				{
					$execlusion[] =  $row->execlusion_name;
					$execlusion_id[] = $row->execlusion_id;
				}
			}
			if($item->is_ingredients == 1)
			{
				$data['items'][$i]->ingredients = DB::table('order_ingredientdetails')
												->join('ingredientlist_description', 'order_ingredientdetails.ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
												->select('order_ingredientdetails.price as ingredient_price', 'ingredientlist_description.ingredientlist_name as ingredient_name', 'ingredientlist_description.ingredientlist_id as ingredient_id')
												->where('order_ingredientdetails.order_itemdetails_id', $data['items'][$i]->id)
												->where('order_ingredientdetails.order_id', $id)
												->where('ingredientlist_description.language', $this->current_language)
												->get();
				$j=0;
				$ingredient_name = [];
				$ingredient_id = [];
				$ingredient_price = []; //print_r($data['items'][$i]->ingredients); exit;
				foreach($data['items'][$i]->ingredients as $ingredient)
				{
					$ingredient_name[$j] = $ingredient->ingredient_name;
					$ingredient_id[$j] = $ingredient->ingredient_id;
					$ingredient_price[$j] = $ingredient->ingredient_price;
					$j++;
				}
					if(!Session::has('flag'))
					{
						Cart::add(array('id' => $item->item_id, 'name' => $item->item_name, 'qty' => $item->quantity, 'price' => $item->price, 'options' => array('image' => $item->image, 'is_ingredients' => $item->is_ingredients, 'ingredientlist' => $ingredient_name, 'ingredientlist_id' => $ingredient_id, 'ingredient_price' => $ingredient_price, 'is_size' => $item->is_size, 'size' => $size_name, 'size_id' => $size_id, 'size_price' => $size_price, 'is_execlusion' => $item->is_execlusion, 'execlusions' => $execlusion, 'execlusion_id' => $execlusion_id)));
					}
			}
			else
			{
				if(!Session::has('flag'))
				{
					Cart::add(array('id' => $item->item_id, 'name' => $item->item_name, 'qty' => $item->quantity, 'price' => $item->price, 'options' => array('image' => $item->image, 'is_ingredients' => $item->is_ingredients, 'ingredientlist' => [], 'ingredientlist_id' => [], 'ingredient_price' => [], 'is_size' => $item->is_size, 'size' => $size_name, 'size_id' => $size_id, 'size_price' => $size_price, 'is_execlusion' => $item->is_execlusion, 'execlusions' => $execlusion, 'execlusion_id' => $execlusion_id)));
				}
				$is_ingredients[] = $item->is_ingredients;
				$data['items'][$i]->ingredients = [];
			}
			$i++;
		}
		$categories = $this->category->getcategories();
		$branches = $this->branch->getbranches();
		$ingredients = $this->ingredient->getingredients();
		$deliveryboys = $this->deliveryboy->getbranch_deliveryboys($order->branch_id);
		//echo '<pre>'; print_r(Cart::content()); exit;
		Session::put('flag', 1);
		return view('branch/editorder', array('order' => $order, 'data' => $data, 'ingredients' => $ingredients, 'branches' => $branches, 'categories' => $categories, 'deliveryboys' => $deliveryboys));
    }
    
    public function updateqty($order_id, $item_id, $quantity)
    {
		//$id = Input::get('id');
		//$quantity = Input::get('quantity');
		DB::table('order_itemdetails')->where('order_id', $order_id)->where('item_id', $item_id)->update(['quantity' => $quantity]);
		return 1;
	}
	
	public function delete_item($order_id, $item_id)
    {
		//$id = Input::get('id');
		
		$item = DB::table('order_itemdetails')->where('order_id', $order_id)->where('item_id', $item_id)->first();
		if($item->is_ingredients)
		{
			DB::table('order_ingredientdetails')->where('order_id', $order_id)->where('item_id', $item_id)->delete();
		}
		DB::table('order_itemdetails')->where('order_id', $order_id)->where('item_id', $item_id)->delete();
		return 1;
	}
	
	public function getorder_branches()
	{
		$delivery_type = Input::get('delivery_type');
		$latitude = Input::get('latitude');
		$longitude = Input::get('longitude');
		$order_date = Input::get('order_date');
		$order_time = date('H:i', strtotime(Input::get('order_time')));
		
		$date = date('Y-m-d', strtotime($order_date));
		$day = strtolower(date('l', strtotime($date)));
		//echo $day; exit;
		$branches = DB::select('SELECT sh_branches.*,sh_branch_description.branch_name as branch,
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
					AND sh_vendor_timeslot.start_time <= "'.$order_time.'"
					AND sh_vendor_timeslot.close_time >= "'.$order_time.'"
					AND (sh_branches.delivery_type = "'.$delivery_type.'" OR sh_branches.delivery_type = "b") 
					GROUP BY sh_branches.id
					HAVING distance
					ORDER BY distance;');
		$result = '<option value=""></option>';
		if(count($branches) > 0)
		{
			foreach($branches as $branch)
			{
				$result .= '<option value='.$branch->id.' data-delivery='.$branch->delivery_fee.'>'.$branch->branch.' - '.round($branch->distance).' KM</option>';
			}
			
			$return = array('msg' => 1, 'branches' => $result);
			return json_encode($return);
		}
		else
		{
			$return = array('msg' => 0);
			return json_encode($return);
		}
	}
	
	public function update_orderstatus()
	{
		$order_id = Input::get('order_id');
		$status = Input::get('order_status');
		
		DB::table('orders')->where('id', $order_id)->update(['order_status' => $status]);
		$order = DB::table('orders')->where('id', $order_id)->first();
		$msg = trans('frontend.Hello').' '.$order->customer_first_name.$order->customer_last_name.' '.trans('frontend.Confirmation Message').' '.trans('frontend.Shuneez');
		$mobile = valid_mobile($order->customer_mobile);
		if($status == 'a')
		{
			DB::table('orders')->where('id', $order_id)->update(['accepted_at' => date('Y-m-d H:i:s')]);
			sendSMS(2, $mobile, $msg);
		}
		return Redirect::back()->with('success', trans('messages.Order status updated successfully'));
	}
	
	public function assign_order($id)
	{
		$order = DB::table('orders')
				 ->join('branches', 'orders.branch_id', '=', 'branches.id')
				 ->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
				 ->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
				 ->select('user_addressbook.latitude', 'user_addressbook.longitude', 'branch_description.branch_name', 'branches.email')
				 ->where('orders.id', $id)
				 ->first();
		
		if($this->config_data['auto_allocation'] == 1)
		{
			$deliveryboy = DB::select('SELECT sh_deliveryboys.*,sh_deliveryboy_description.deliveryboy_name
						   ( 3959 * acos( cos( radians('.$order->latitude.') ) * cos( radians( sh_deliveryboys.latitude ) ) 
						   * cos( radians(sh_deliveryboys.longitude) - radians('.$order->longitude.')) + sin(radians('.$order->latitude.')) 
						   * sin( radians(sh_deliveryboys.latitude)))) <= '.$this->config_data['deliveryboy_radius'].' AS distance
							FROM sh_deliveryboys
							JOIN sh_deliveryboy_description ON sh_deliveryboys.id = sh_deliveryboy_description.deliveryboy_id
							WHERE
							sh_deliveryboys.status = 1 
							AND sh_deliveryboys.is_delete = 0
							AND sh_deliveryboys.availability = 1
							AND sh_deliveryboys.is_logout = 0
							HAVING distance
							ORDER BY distance LIMIT 0,1;'); 
			if(count($deliveryboy))
			{
				DB::table('orders')->where('id', $id)->update(['deliveryboy_id' => $deliveryboy[0]->id, 'order_status' => 'as']);
				DB::table('deliveryboy_request')->insert(['order_id' => $id, 'deliveryboy_id' => $deliveryboy[0]->id, 'status' => 'n', 'created_at' => date('Y-m-d H:i:s')]); 
				$message = 'Hi '.$deliveryboy[0]->deliveryboy_name.' Admin assigned a new Order for you. The branch details are, Shop Name: '.$order->branch_name.'<br>Shop Email : '.$order->email.'</p><br><p>Thanks & Regards,</p><p>Shuneez team</p>';
				$device_id = $deliveryboy[0]->device_token;
				sendMessage($message, $device_id);
				return redirect('branch/orders')->with('success', trans('messages.Deliveryboy assigned successfully'));
			}
			else
			{
				return redirect('branch/orders')->with('error', trans('messages.No deliveryboy found'));
			}
		}
		else
		{
			$deliveryboys = DB::select('SELECT sh_deliveryboys.*,sh_deliveryboy_description.deliveryboy_name as deliveryboy,
						   ( 3959 * acos( cos( radians('.$order->latitude.') ) * cos( radians( sh_deliveryboys.latitude ) ) 
						   * cos( radians(sh_deliveryboys.longitude) - radians('.$order->longitude.')) + sin(radians('.$order->latitude.')) 
						   * sin( radians(sh_deliveryboys.latitude)))) <= '.$this->config_data['deliveryboy_radius'].' AS distance
							FROM sh_deliveryboys
							JOIN sh_deliveryboy_description ON sh_deliveryboys.id = sh_deliveryboy_description.deliveryboy_id
							WHERE
							sh_deliveryboys.status = 1 
							AND sh_deliveryboys.is_delete = 0
							AND sh_deliveryboys.availability = 1
							AND sh_deliveryboys.is_logout = 0
							AND sh_deliveryboy_description.language = "'.$this->current_language.'" 
							HAVING distance
							ORDER BY distance;');
			if(count($deliveryboys))
			{
				//echo'<pre>'; print_r($deliveryboys); exit;
				return view('branch/assign_deliveryboy', array('deliveryboys' => $deliveryboys, 'order_id' => $id));
			}
			else
			{
				return redirect('branch/orders')->with('error', trans('messages.No deliveryboy found'));
			}
		}
		
	}
	
	public function assign_deliveryboy($id, $order_id)
	{
		$order = DB::table('orders')
				 ->join('branches', 'orders.branch_id', '=', 'branches.id')
				 ->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
				 ->join('user_addressbook', 'orders.address_id', '=', 'user_addressbook.id')
				 ->select('user_addressbook.latitude', 'user_addressbook.longitude', 'branch_description.branch_name', 'branches.email')
				 ->where('orders.id', $order_id)
				 ->first();
		$deliveryboy = $this->deliveryboy->getdeliveryboy($id);
		DB::table('orders')->where('id', $order_id)->update(['deliveryboy_id' => $id, 'order_status' => 'as']);
		DB::table('deliveryboy_request')->insert(['order_id' => $id, 'deliveryboy_id' => $id, 'status' => 'n', 'created_at' => date('Y-m-d H:i:s')]); 
		$message = 'Hi '.$deliveryboy->deliveryboy_name.' Admin assigned a new Order for you. The branch details are, Shop Name: '.$order->branch_name.'<br>Shop Email : '.$order->email.'</p><br><p>Thanks & Regards,</p><p>Shuneez team</p>';
		$device_id = $deliveryboy->device_token;
		sendMessage($message, $device_id);
		return redirect('branch/orders')->with('success', trans('messages.Deliveryboy assigned successfully'));
	}
	
}
