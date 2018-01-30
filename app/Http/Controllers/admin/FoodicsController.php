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
use App\Vendoritem;
use App\Vendor;
use App\Language;
use App\Category;
use App\Cuisine;
use App\Ingredient;
use App\Execlusion;
use App\Addresstype;
use App\Branch;
use View;
use Redirect;

class FoodicsController extends Controller {

	public function __construct(Guard $auth, Vendoritem $vendoritem, Vendor $vendor, Language $language, Category $category, Cuisine $cuisine, Ingredient $ingredient, Addresstype $addresstype, Execlusion $execlusion, Branch $branch)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->vendoritem = $vendoritem;
	    $this->addresstype = $addresstype;
	    $this->vendor = $vendor;
	    $this->language = $language;
	    $this->category = $category;
	    $this->cuisine = $cuisine;
	    $this->ingredient = $ingredient;
	    $this->execlusion = $execlusion;
	    $this->branch = $branch;
	    $this->prefix = DB::getTablePrefix();
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	    $settings = DB::table('settings')->get();
		foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		$this->config_data = $config_data;
		
	}
	
	
	public function getFoodicsItems()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/products");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$details = json_decode($response);

		$items = $this->vendoritem->getAllItems();

		return view('admin/item_integration', array('products' => $details, 'items' => $items));
	}

	public function updateFoodicsItem()
	{
		$item_id = Input::get('item_id');
		$foodics_id = Input::get('foodics_id');

		$isExist = DB::table('vendor_items')->where('id', '!=', $item_id)->where('foodics_id', $foodics_id)->count();
		
		if($isExist)
		{
			$return = array('success' => 0, 'msg' => trans('messages.This foodics item already mapped with another item'));
			return json_encode($return);
		}
		else
		{
			$details = $this->getFoodicsProduct($foodics_id);	
			
			DB::table('vendor_items')->where('id', $item_id)->update(['foodics_id' => $foodics_id]);
			
			$sizelist = DB::table('item_size')->where('item_id', $item_id)->get();
			if(count($sizelist))
			{
				$i = 0;
				foreach ($sizelist as $size) 
				{
					$size_id = (isset($details->product->sizes[$i])) ? $details->product->sizes[$i]->hid : '';
					DB::table('item_size')->where('id', $size->id)->update(['foodics_id' => $size_id]);
					$i++;
				}
			}

			$ingredients = DB::table('vendor_item_ingredients')->where('item_id', $item_id)->get();
			if(count($ingredients))
			{
				$i = 0;
				foreach ($ingredients as $ingredient) 
				{
					$ingredient_foodics_id = (isset($details->product->modifiers[$i])) ? $details->product->modifiers[$i]->hid : '';
					DB::table('vendor_item_ingredients')->where('id', $ingredient->id)->update(['foodics_id' => $ingredient_foodics_id]);
					$ingredientlists = DB::table('vendor_item_ingredientlist')->where('item_ingredient_id', $ingredient->id)->get();
					if(count($ingredientlists))
					{
						$j = 0;
						foreach ($ingredientlists as $ingredientlist) 
					 	{
					 		$ingredientlist_foodics_id = (isset($details->product->modifiers[$i]->options[$j])) ? $details->product->modifiers[$i]->options[$j]->hid : '';
					 		DB::table('vendor_item_ingredientlist')->where('id', $ingredientlist->id)->update(['foodics_id' => $ingredientlist_foodics_id]);
					 		$j++;
					 	}
					}
					$i++;
				}
			}

			$return = array('success' => 1, 'msg' => trans('messages.Integrated successfully'));
			return json_encode($return);
		}
	}

	public function removeFoodicsItem($id)
	{
		DB::table('vendor_items')->where('id', $id)->update(['foodics_id' => '']);
		return redirect('admin/item_integration')->with('success', trans('messages.Foodics integration removed successfully'));
	}

	public function getFoodicsIngreidents()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/modifiers");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$details = json_decode($response);// echo "<pre>"; print_r($details); exit;

		$ingredients = $this->ingredient->getAllIngredients();

		return view('admin/ingredient_integration', array('products' => $details, 'ingredients' => $ingredients));
	}

	public function updateFoodicsIngreident()
	{
		$ingredient_id = Input::get('ingredient_id');
		$foodics_id = Input::get('foodics_id');

		$isExist = DB::table('ingredients')->where('id', '!=', $ingredient_id)->where('foodics_id', $foodics_id)->count();
		
		if($isExist)
		{
			$return = array('success' => 0, 'msg' => trans('messages.This foodics item already mapped with another item'));
			return json_encode($return);
		}
		else
		{
			DB::table('ingredients')->where('id', $ingredient_id)->update(['foodics_id' => $foodics_id]);
			$return = array('success' => 1, 'msg' => trans('messages.Integrated successfully'));
			return json_encode($return);
		}
	}

	public function removeFoodicsIngredient($id)
	{
		DB::table('ingredients')->where('id', $id)->update(['foodics_id' => '']);
		return redirect('admin/ingredient_integration')->with('success', trans('messages.Foodics integration removed successfully'));
	}

	public function getFoodicsIngredientList()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/inventory-items");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$details = json_decode($response);// echo "<pre>"; print_r($details); exit;

		$ingredientlist = $this->ingredient->getAllIngredientList();

		return view('admin/ingredientlist_integration', array('products' => $details, 'ingredientlist' => $ingredientlist));
	}

	public function updateFoodicsIngredientList()
	{
		$ingredientlist_id = Input::get('ingredientlist_id');
		$foodics_id = Input::get('foodics_id');

		$isExist = DB::table('ingredientlist')->where('id', '!=', $ingredientlist_id)->where('foodics_id', $foodics_id)->count();
		
		if($isExist)
		{
			$return = array('success' => 0, 'msg' => trans('messages.This foodics item already mapped with another item'));
			return json_encode($return);
		}
		else
		{
			DB::table('ingredientlist')->where('id', $ingredientlist_id)->update(['foodics_id' => $foodics_id]);
			$return = array('success' => 1, 'msg' => trans('messages.Integrated successfully'));
			return json_encode($return);
		}
	}

	public function removeFoodicsIngredientList($id)
	{
		DB::table('ingredientlist')->where('id', $id)->update(['foodics_id' => '']);
		return redirect('admin/ingredientlist_integration')->with('success', trans('messages.Foodics integration removed successfully'));
	}

	public function getFoodicsAddressType()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/delivery-zones");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$details = json_decode($response);// echo "<pre>"; print_r($details); exit;

		$addresstype = $this->addresstype->getAllAddressType();

		return view('admin/addresstype_integration', array('delivery_zones' => $details, 'addresstype' => $addresstype));
	}

	public function updateFoodicsAddressType()
	{
		$addresstype_id = Input::get('addresstype_id');
		$foodics_id = Input::get('foodics_id');

		$isExist = DB::table('addresstype')->where('id', '!=', $addresstype_id)->where('foodics_id', $foodics_id)->count();
		
		if($isExist)
		{
			$return = array('success' => 0, 'msg' => trans('messages.This foodics item already mapped with another item'));
			return json_encode($return);
		}
		else
		{
			DB::table('addresstype')->where('id', $addresstype_id)->update(['foodics_id' => $foodics_id]);
			$return = array('success' => 1, 'msg' => trans('messages.Integrated successfully'));
			return json_encode($return);
		}
	}

	public function removeFoodicsAddressType($id)
	{
		DB::table('addresstype')->where('id', $id)->update(['foodics_id' => '']);
		return redirect('admin/addresstype_integration')->with('success', trans('messages.Foodics integration removed successfully'));
	}

	public function viewItem()
	{
		$id = Input::get('id');
		$type = Input::get('type');

		if($type == 's')
		{
			$item = DB::table('vendor_items')
					->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
					->join('category_description', 'vendor_items.category_id', '=', 'category_description.category_id')
					->select('vendor_items.*', 'vendor_item_description.item_name', 'category_name')
					->where('vendor_items.id', $id)
					->where('category_description.language', $this->current_language)
					->where('vendor_item_description.language', $this->current_language)
					->first(); 
			
			if($item->is_size)
			{
				$item->sizelist = DB::table('item_size')
								->join('item_size_description', 'item_size.id', '=', 'item_size_description.item_size_id')
								->select('item_size.*', 'item_size_description.size_name')
								->where('language', $this->current_language)
								->where('item_size.item_id', $id)
								->get(); 
			}
			if($item->is_ingredients)
			{
				$item->ingredients = DB::table('vendor_item_ingredients')
									->join('ingredient_description', 'vendor_item_ingredients.ingredient_id', '=', 'ingredient_description.ingredient_id')
									->select('vendor_item_ingredients.*', 'ingredient_description.ingredient_name')
									->where('item_id', $id)
									->where('language', $this->current_language)
									->get();
				foreach ($item->ingredients as $ingredient) 
				{
				 	$ingredient->ingredientlists = DB::table('vendor_item_ingredientlist')
				 								->join('ingredientlist_description', 'vendor_item_ingredientlist.item_ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
												->select('vendor_item_ingredientlist.*', 'ingredientlist_description.ingredientlist_name')
												->where('item_id', $id)
												->where('item_ingredient_id', $ingredient->id)
												->where('language', $this->current_language)
												->get();
				} 
			}
			$itemview = view('admin/view_itemdetails', array('item' => $item));
			echo $itemview->render();
		}
		else
		{
			$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
			$header = array();
			$header[] = 'Authorization: Bearer '.$access_token;
			$header[] = 'Content-type: application/json; charset=utf-8';
			
			$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url."/api/v2/products/".$id);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$item = json_decode($response);	

			$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url."/api/v2/categories/".$item->product->category->hid);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$category = json_decode($response);	

			$item->product->category_name = $category->category->name->en;

			if(count($item->product->modifiers))
			{
				$i = 0;
				foreach($item->product->modifiers as $modifier)
				{
					$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url."/api/v2/modifiers/".$modifier->hid);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
					curl_setopt($ch, CURLOPT_POST, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

					$response = curl_exec($ch);
					curl_close($ch);
					$ingredient = json_decode($response);
					//echo '<pre>'; print_r($ingredient); exit;
					if(count($ingredient->modifier->options))
					{
						$j = 0;
						foreach($ingredient->modifier->options as $option)
						{
							$option->name = $option->name->en;
							$item->product->modifiers[$i]->options[$j] = $option;
							$j++;
						}
					}
					$i++;
				}
			}

			$itemview = view('admin/view_foodicsitem', array('item' => $item));
			echo $itemview->render();
		}
	}

	public function viewIngredient()
	{
		$id = Input::get('id');
		$type = Input::get('type');

		if($type == 's')
		{
			$ingredient = DB::table('ingredients')
								->join('ingredient_description', 'ingredients.id', '=', 'ingredient_description.ingredient_id')
								->select('ingredients.*', 'ingredient_description.ingredient_name')
								->where('ingredients.id', $id)
								->where('language', $this->current_language)
								->first();
			$ingredient->ingredientlists = DB::table('ingredientlist')
			 								->join('ingredientlist_description', 'ingredientlist.id', '=', 'ingredientlist_description.ingredientlist_id')
											->select('ingredientlist.*', 'ingredientlist_description.ingredientlist_name')
											->where('ingredient_id', $ingredient->id)
											->where('language', $this->current_language)
											->get();
			$itemview = view('admin/view_ingredient', array('ingredient' => $ingredient));
			echo $itemview->render();
		}
		else
		{
			$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
			$header = array();
			$header[] = 'Authorization: Bearer '.$access_token;
			$header[] = 'Content-type: application/json; charset=utf-8';
			
			$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url."/api/v2/modifiers/".$id);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$ingredient = json_decode($response);	
			//echo '<pre>'; print_r($ingredient); exit;
			$itemview = view('admin/viewfoodics_ingredient', array('ingredient' => $ingredient));
			echo $itemview->render();
		}
	}

	public function getFoodicsProduct($id)
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/products/".$id);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$item = json_decode($response);	

		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/categories/".$item->product->category->hid);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$category = json_decode($response);	

		$item->product->category_name = $category->category->name->en;

		if(count($item->product->modifiers))
		{
			$i = 0;
			foreach($item->product->modifiers as $modifier)
			{
				$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/modifiers/".$modifier->hid);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_POST, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

				$response = curl_exec($ch);
				curl_close($ch);
				$ingredient = json_decode($response);
			
				if(count($ingredient->modifier->options))
				{
					$j = 0;
					foreach($ingredient->modifier->options as $option)
					{
						$option->name = $option->name->en;
						$option->price = $option->price;
						$item->product->modifiers[$i]->options[$j] = $option;
						$j++;
					}
				}
				$i++;
			}
		}
		return $item;
	}

	public function getFoodicsExeclusions()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/inventory-items");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$details = json_decode($response);// echo "<pre>"; print_r($details); exit;

		$execlusions = $this->execlusion->getActiveExeclusions();

		return view('admin/execlusion_integration', array('products' => $details, 'execlusions' => $execlusions));
	}

	public function updateFoodicsExeclusion()
	{
		$execlusion_id = Input::get('execlusion_id');
		$foodics_id = Input::get('foodics_id');

		$isExist = DB::table('execlusions')->where('id', '!=', $execlusion_id)->where('foodics_id', $foodics_id)->count();
		
		if($isExist)
		{
			$return = array('success' => 0, 'msg' => trans('messages.This foodics item already mapped with another item'));
			return json_encode($return);
		}
		else
		{
			DB::table('execlusions')->where('id', $execlusion_id)->update(['foodics_id' => $foodics_id]);
			DB::table('vendor_item_execlusions')->where('execlusion_id', $execlusion_id)->update(['foodics_id' => $foodics_id]);
			$return = array('success' => 1, 'msg' => trans('messages.Integrated successfully'));
			return json_encode($return);
		}
	}

	public function removeFoodicsExeclusion($id)
	{
		DB::table('execlusions')->where('id', $id)->update(['foodics_id' => '']);
		DB::table('vendor_item_execlusions')->where('execlusion_id', $id)->update(['foodics_id' => '']);
		return redirect('admin/execlusion_integration')->with('success', trans('messages.Foodics integration removed successfully'));
	}

	public function getFoodicsBranch()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$url = ( $this->config_data['is_foodics_production'] == 1 ) ? $this->config_data['foodics_production_url'] : $this->config_data['foodics_test_url'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/v2/branches");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$details = json_decode($response); //echo "<pre>"; print_r($details->branches); exit;

		$branches = $this->branch->getActiveBranches();

		return view('admin/branch_integration', array('products' => $details, 'branches' => $branches));
	}

	public function updateFoodicsBranch()
	{
		$branch_id = Input::get('branch_id');
		$foodics_id = Input::get('foodics_id');

		$isExist = DB::table('branches')->where('id', '!=', $branch_id)->where('foodics_id', $foodics_id)->count();
		
		if($isExist)
		{
			$return = array('success' => 0, 'msg' => trans('messages.This foodics branch already mapped with another branch'));
			return json_encode($return);
		}
		else
		{
			DB::table('branches')->where('id', $branch_id)->update(['foodics_id' => $foodics_id]);
			$return = array('success' => 1, 'msg' => trans('messages.Integrated successfully'));
			return json_encode($return);
		}
	}

	public function removeFoodicsBranch($id)
	{
		DB::table('branches')->where('id', $id)->update(['foodics_id' => '']);
		return redirect('admin/branch-integration')->with('success', trans('messages.Foodics integration removed successfully'));
	}
}
