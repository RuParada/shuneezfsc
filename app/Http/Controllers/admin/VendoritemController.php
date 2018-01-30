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

class VendoritemController extends Controller {

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
		
	}
	
	/*********** Get vendoritem***************/
	
	public function getvendoritems()
	{
		$items = $this->vendoritem->getvendoritems();
		$vendor = DB::table('vendor_description')->where('language', $this->current_language)->first();
		$categories = $this->category->getcategories();
		//$cuisines = $this->cuisine->getcuisines();
		return view('admin/vendoritems', array('items' => $items, 'vendor' => $vendor, 'categories' => $categories));
	}
	
	public function addvendoritem_form()
	{
		$languages = $this->language->getlanguages();
		$ingredients = $this->ingredient->getingredients();
		$categories = $this->category->getcategories();
		$execlusions = $this->execlusion->getActiveExeclusions();
		
		return view('admin/addvendor_item', array('languages' => $languages, 'categories' => $categories, 'ingredients' => $ingredients, 'execlusions' => $execlusions));
		
	}
	
	public function addvendoritem()
	{
		$items = Input::get('item_name');
		$valid = Validator::make(Input::all(),
									['category' => 'required',
									 'price' => 'required|numeric',
									 'image' => 'required|mimes:jpeg,jpg,png',
									 'weight' => 'numeric',
									 'serve_for' => 'numeric',
									 'preparation_time' => 'numeric',
									 'sort_number' => 'numeric',
									]);
		$array_valid = $this->vendoritem->rules($items);
		if($array_valid['error_count'])
		{
			return redirect('admin/addvendor_item')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/addvendor_item')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('vendor_items')->where('item_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->vendoritem->item_key = $key;
			$this->vendoritem->category_id = Input::get('category');
			$this->vendoritem->subcategory_id = (Input::get('subcategory') != '') ? Input::get('subcategory') : 0;
			$this->vendoritem->price = Input::get('price');
			$this->vendoritem->weight = Input::get('weight');
			$this->vendoritem->units = Input::get('unit');
			$this->vendoritem->serve_for = Input::get('serve_for');
			$this->vendoritem->availability = Input::get('availability');
			$this->vendoritem->featured = Input::get('featured');
			$this->vendoritem->status = Input::get('status');
			$this->vendoritem->is_ingredients = (Input::get('ingredient')[0] != '') ? 1 : 0;
			$this->vendoritem->approved = 1;
			$this->vendoritem->is_size = Input::get('is_size');
			$this->vendoritem->is_execlusion = Input::get('is_execlusion');
			$this->vendoritem->sort_number = (Input::get('sort_number') != '') ? Input::get('sort_number') : 0;
			
			if(Input::file('image') != '')
			{
				$image = str_random(6).Input::file('image')->getClientOriginalName();
				$dest = 'assets/uploads/vendor_items';
				Input::file('image')->move($dest,$image);
				$this->vendoritem->image = $image;
			}
			
			$this->vendoritem->save();
			
			$item_id = $this->vendoritem->id;
			
			$languages = Input::get('language');
			$items = Input::get('item_name');
			$description = Input::get('item_description');
			
			if(count($items) > 0)
			{
				for($i=0; $i<count($items); $i++)
				{
					DB::table('vendor_item_description')->insert(['item_id' => $item_id, 'item_name' => $items[$i], 'item_description' => $description[$i], 'language' => $languages[$i]]);
				}
			}
			
			$ingredients = Input::get('ingredient');
			$minimum = Input::get('minimum');
			$maximum = Input::get('maximum');
			$ingredient_sort = Input::get('ingredient_sort');
			$required = Input::get('required');
			$ingredientlist = Input::get('ingredient_list_id');
			$item_price = Input::get('item_price');
			
			//print_r($ingredientlist); exit;
			//echo $ingredientlist[$ingredient_id]; exit;
			if($ingredients[0] != '' && Input::get('is_ingredients'))
			{
				for($i=0; $i<count($ingredients); $i++)
				{
					if($ingredients[$i] != '')
					{
						$min = (isset($minimum[$i])) ? $minimum[$i] : 0;
						$max = (isset($maximum[$i])) ? $maximum[$i] : 0;
						$req = (isset($required[$i])) ? $required[$i] : 0;
						$sort = (isset($ingredient_sort[$i])) ? $ingredient_sort[$i] : 0;
						DB::table('vendor_item_ingredients')->insert(['item_id' => $item_id, 'ingredient_id' => $ingredients[$i], 'minimum' => $min, 'maximum' => $max, 'required' => $req, 'sort_number' => $sort]);
						$ingredient_id = $ingredients[$i];
						$item_ingredient_id = DB::getPdo()->lastInsertId();
						if(count($ingredientlist[$ingredient_id]) > 0)
						{
							for($j=0; $j<count($ingredientlist[$ingredient_id]); $j++)
							{
								DB::table('vendor_item_ingredientlist')->insert(['item_id' => $item_id, 'item_ingredient_id' => $item_ingredient_id, 'item_ingredientlist_id' => $ingredientlist[$ingredient_id][$j], 'price' => $item_price[$ingredient_id][$j]]);
							}
						}
					}
				}
			}

			$is_size = Input::get('is_size');
			$size = ($is_size) ? array_values(Input::get('size')) : [];
			$language = Input::get('language');
			if($is_size && count($size))
			{
				for($i=0; $i<count($size); $i++)
				{
					$price = $size[$i]['price'];
					DB::table('item_size')->insert(['item_id' => $item_id, 'price' => $price, 'created_at' => date('Y-m-d H:i:s')]);			
					$size_id = DB::getPdo()->lastInsertId();
					if(count($language) > 0)
					{
						for($j=0; $j<count($language); $j++)
						{
							$name = $size[$i]['name'][$language[$j]];
							DB::table('item_size_description')->insert(['item_id' => $item_id, 'item_size_id' => $size_id, 'size_name' => $name, 'language' => $language[$j]]);
						}
					}
				}
			}

			$is_execlusion = Input::get('is_execlusion');
			$execlusion_id = ($is_execlusion) ? array_values(Input::get('execlusion_id')) : [];
			if($is_execlusion && count($execlusion_id))
			{
				for($i=0; $i<count($execlusion_id); $i++)
				{
					DB::table('vendor_item_execlusions')->insert(['item_id' => $item_id, 'execlusion_id' => $execlusion_id[$i]]);
				}
			}
			
			
			return redirect('admin/vendoritems')->with('success', trans('messages.Item Add'));
		}
	}
	
	public function getvendoritem($id)
	{
		$item = DB::table('vendor_items')->where('id', $id)->first();
		$languages = $this->language->getlanguages();
		$categories = $this->category->getcategories();
		$execlusions = $this->execlusion->getActiveExeclusions();
		$vendor_execlusions = $this->execlusion->getVendorExeclusions($id);

		$subcategories = DB::table('subcategories')
						->join('subcategory_description', 'subcategories.id', '=', 'subcategory_description.subcategory_id')
						->SelectRaw(DB::getTablePrefix().'subcategories.id,'.DB::getTablePrefix().'subcategory_description.subcategory_name as subcategory')
						->where('subcategories.category_id', $item->category_id)
						->where('subcategories.is_delete', 0)
						->where('subcategory_description.language', $this->current_language)
						->get();
		$ingredients = $this->ingredient->getingredients();
		$item_ingredients = DB::table('vendor_item_ingredients')->where('item_id', $id)->orderby('sort_number', 'asc')->get();
		$data = [];
		if(count($item_ingredients))
		{
			$i = 0;
			foreach ($item_ingredients as $item_ingredient) 
			{
				$data[$i]['ingredients'] = DB::table('vendor_item_ingredients')->where('id', $item_ingredient->id)->first();
				$data[$i]['ingredients']->ingredientlists = DB::table('vendor_item_ingredientlist')
											->join('ingredientlist_description', 'vendor_item_ingredientlist.item_ingredientlist_id', '=', 'ingredientlist_description.ingredientlist_id')
											->SelectRaw(DB::getTablePrefix().'vendor_item_ingredientlist.*,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_name as ingredientlist, '.DB::getTablePrefix().'ingredientlist_description.ingredientlist_id')
											->where('vendor_item_ingredientlist.item_ingredient_id', $item_ingredient->id)
											->where('ingredientlist_description.language', $this->current_language) 
											->get();
				$i++;	
			}
		}
		//echo "<pre>"; print_r($data); exit;
		return view('admin/editvendor_item', array('item' => $item, 'languages' => $languages, 'categories' => $categories, 'subcategories' => $subcategories, 'ingredients' => $ingredients, 'item_ingredients' => $data, 'execlusions' => $execlusions, 'vendor_execlusions' => $vendor_execlusions));
	}
	
	public function updatevendoritem()
	{//echo '<pre>'; print_r(Input::all()); exit;
		$items = Input::get('item_name');
		$item_id = Input::get('id');
		$valid = Validator::make(Input::all(),
									['category' => 'required',
									 'price' => 'required|numeric',
									 'image' => 'mimes:jpeg,jpg,png',
									 'weight' => 'numeric',
									 'serve_for' => 'numeric',
									 'preparation_time' => 'numeric',
									 'sort_number' => 'numeric',
									]);
		$array_valid = $this->vendoritem->rules($items);
		if($array_valid['error_count'])
		{
			return redirect('admin/editvendor_item/'.$item_id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/editvendor_item/'.$item_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->vendoritem->category_id = Input::get('category');
			$this->vendoritem->subcategory_id = (Input::get('subcategory') != '') ? Input::get('subcategory') : 0;
			$this->vendoritem->price = Input::get('price');
			$this->vendoritem->weight = Input::get('weight');
			$this->vendoritem->units = Input::get('unit');
			$this->vendoritem->serve_for = Input::get('serve_for');
			$this->vendoritem->availability = Input::get('availability');
			$this->vendoritem->featured = Input::get('availability');
			$this->vendoritem->status = Input::get('availability');
			$this->vendoritem->is_ingredients = (Input::get('ingredient')[0] != '') ? 1 : 0;
			$this->vendoritem->approved = 1;
			$this->vendoritem->foodics_id = '';
			$this->vendoritem->is_size = Input::get('is_size');
			$this->vendoritem->is_execlusion = Input::get('is_execlusion');
			$this->vendoritem->sort_number = (Input::get('sort_number') != '') ? Input::get('sort_number') : 0;
			
			if(Input::file('image') != '')
			{
				$item = DB::table('vendor_items')->where('id', $item_id)->first();
				$dest = 'assets/uploads/vendor_items';
				if(file_exists($dest.'/'.$item->image))
				{
					unlink($dest.'/'.$item->image);
				}
				$image = str_random(6).Input::file('image')->getClientOriginalName();
				
				Input::file('image')->move($dest,$image);
				$this->vendoritem->image = $image;
			}
				
			DB::table('vendor_items')->where('id', $item_id)->update($this->vendoritem['attributes']);
			
			$languages = Input::get('language');
			$items = Input::get('item_name');
			$description = Input::get('item_description');
			
			DB::table('vendor_item_description')->where('item_id', $item_id)->delete();
			if(count($items) > 0)
			{
				for($i=0; $i<count($items); $i++)
				{
					DB::table('vendor_item_description')->insert(['item_id' => $item_id, 'item_name' => $items[$i], 'item_description' => $description[$i], 'language' => $languages[$i]]);
				}
			}
			
			$ingredients = Input::get('ingredient');
			$minimum = Input::get('minimum');
			$maximum = Input::get('maximum');
			$required = Input::get('required');
			$ingredient_sort = Input::get('ingredient_sort');
			$ingredientlist = Input::get('ingredient_list_id');
			$item_price = Input::get('item_price');
			$item_ingredients = DB::table('vendor_item_ingredients')->where('item_id', $item_id)->get();
			//print_r($required); exit;
			if(count($item_ingredients) > 0)
			{
				foreach($item_ingredients as $item_ingredient)
				{
					DB::table('vendor_item_ingredientlist')->where('item_ingredient_id', $item_ingredient->id)->delete();
				}
			}
			
			DB::table('vendor_item_ingredients')->where('item_id', $item_id)->delete(); 
			DB::table('vendor_item_ingredientlist')->where('item_id', $item_id)->delete(); 
			if($ingredients[0] != '' && Input::get('is_ingredients'))
			{ 
				for($i=0; $i<count($ingredients); $i++)
				{
					if($ingredients[$i] != '')
					{ 
						$min = (isset($minimum[$i])) ? $minimum[$i] : 0;
						$max = (isset($maximum[$i])) ? $maximum[$i] : 0;
						$req = (isset($required[$i])) ? $required[$i] : 0;
						$sort = (isset($ingredient_sort[$i])) ? $ingredient_sort[$i] : 0;
						DB::table('vendor_item_ingredients')->insert(['item_id' => $item_id, 'ingredient_id' => $ingredients[$i], 'minimum' => $min, 'maximum' => $max, 'required' => $req, 'sort_number' => $sort]);
						$ingredient_id = $ingredients[$i];
						$item_ingredient_id = DB::getPdo()->lastInsertId();
						if(count($ingredientlist[$ingredient_id]) > 0)
						{
							for($j=0; $j<count($ingredientlist[$ingredient_id]); $j++)
							{ 
								DB::table('vendor_item_ingredientlist')->insert(['item_id' => $item_id, 'item_ingredient_id' => $item_ingredient_id, 'item_ingredientlist_id' => $ingredientlist[$ingredient_id][$j], 'price' => $item_price[$ingredient_id][$j]]);
							}
						}
					}
				}
			}

			DB::table('vendor_item_execlusions')->where('item_id', $item_id)->delete();

			$is_execlusion = Input::get('is_execlusion');
			$execlusion_id = ($is_execlusion) ? array_values(Input::get('execlusion_id')) : [];
			if($is_execlusion && count($execlusion_id))
			{
				for($i=0; $i<count($execlusion_id); $i++)
				{
					DB::table('vendor_item_execlusions')->insert(['item_id' => $item_id, 'execlusion_id' => $execlusion_id[$i]]);
				}
			}
			
			
			return redirect('admin/vendoritems')->with('success', trans('messages.Item Update'));
		}
	}
	
	/**********Update Vendor Status************/

	public function change_vendoritemstatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('vendor_items')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}
	
	public function filtervendoritems()
	{
		$name = Input::get('name');
		$category = Input::get('category');
		$status = Input::get('status');
		
		$items = DB::table('vendor_items')
						->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id')
						->join('category_description', 'vendor_items.category_id', '=', 'category_description.category_id')
						->SelectRaw(DB::getTablePrefix().'vendor_items.*,'.DB::getTablePrefix().'vendor_item_description.item_name as item,'.DB::getTablePrefix().'category_description.category_name as category')
						->where('vendor_item_description.language', $this->current_language)
						->where('category_description.language', $this->current_language)
						->where(function($query) use($name, $status, $category)
						{
							if($name != '')
							{
								$query->where('vendor_item_description.item_name', 'like', '%'.$name.'%');
							}
							if($category != '')
							{
								$query->where('vendor_items.category_id', $category);
							}
							if($status != '')
							{
								if($status == 'deleted')
								{
									$query->where('vendor_items.is_delete', 1);
								}
								else
								{
									$query->where('vendor_items.status', $status);
								}
							}
							else
							{
								$query->where('vendor_items.is_delete', 0);
							}
						})
						->paginate(10);
						
		$categories = $this->category->getcategories();
		
		return view('admin/vendoritems', array('items' => $items, 'categories' => $categories));		
	}
	
	/************* Delete Vendoritem ***************/
	
	public function deletevendoritem($id)
	{
		DB::table('vendor_items')->where('id', $id)->update(['is_delete' => 1]);
		return redirect('admin/vendoritems')->with('success', trans('messages.Item Delete'));
	}
	
	/************* Restore Deleted Vendoritem ***************/
	
	public function restorevendoritem($id)
	{
		$item = DB::table('vendor_items')->where('id', $id)->first();
		$subcategory = DB::table('subcategories')->where('id', $item->subcategory_id)->first();
		$category = DB::table('categories')->where('id', $item->category_id)->first();
		if($category->is_delete == 0 && $subcategory->is_delete == 0)
		{
			DB::table('vendor_items')->where('id', $id)->update(['is_delete' => 0]);
			return redirect('admin/vendoritems')->with('success', trans('messages.Item Add'));
		}
		elseif($subcategory->is_delete == 1)
		{
			return Redirect::back()->with('delete_error', trans('messages.Restore Subategory'));
		}
		elseif($category->is_delete == 1)
		{
			return Redirect::back()->with('delete_error', trans('messages.Restore Category'));
		}
	}
	
	public function getvendorcategories()
	{
		$vendor_id = Input::get('vendor_id');
		$categoryesult = "<option value=''></option>";
		$categories = DB::table('vendor_categories')
						->join('categories', 'vendor_categories.category_id', '=', 'categories.id')
						->join('category_description', 'categories.id', '=', 'category_description.category_id')
						->SelectRaw(DB::getTablePrefix().'category_description.category_name as category, '.DB::getTablePrefix().'category_description.category_id as category_id')
						->where('category_description.language', $this->current_language)
						->where('categories.is_delete', 0)
						->where('vendor_categories.vendor_id', $vendor_id)
						->get();
		if(count($categories))
		{
			foreach ($categories as $category) 
			{
				$categoryesult .= "<option value='".$category->category_id."'>".ucfirst($category->category)."</option>";
			}
		}
		$result = array('category' => $categoryesult);
		return json_encode($result);
	}
	
	public function getvendorsubcategories()
	{
		$category_id = Input::get('category_id');
		$subcategoryesult = "<option value=''></option>";
		$subcategories = DB::table('subcategories')
						->join('subcategory_description', 'subcategories.id', '=', 'subcategory_description.subcategory_id')
						->SelectRaw(DB::getTablePrefix().'subcategories.id,'.DB::getTablePrefix().'subcategory_description.subcategory_name as subcategory')
						->where('subcategories.category_id', $category_id)
						->where('subcategories.is_delete', 0)
						->where('subcategory_description.language', $this->current_language)
						->get();
		if(count($subcategories))
		{
			foreach ($subcategories as $subcategory) 
			{
				$subcategoryesult .= "<option value='".$subcategory->id."'>".ucfirst($subcategory->subcategory)."</option>";
			}
		}
		$result = array('subcategories' => $subcategoryesult);
		return json_encode($result);
	}
	
	public function getingredientlist()
	{
		$id = Input::get('ingredient_id');
		$ingredientlist = DB::table('ingredientlist')
						  ->join('ingredientlist_description', 'ingredientlist.id', '=', 'ingredientlist_description.ingredientlist_id')
						  ->SelectRaw($this->prefix.'ingredientlist.id as ingredientlist_id,'.$this->prefix.'ingredientlist_description.ingredientlist_name as ingredientlist_name,'.$this->prefix.'ingredientlist.price as price,'.$this->prefix.'ingredientlist.ingredient_id')
						  ->where('ingredientlist_description.language', 'en')
						  ->where('ingredientlist.ingredient_id', $id)
						  ->get();
		$result = '';
		$minmax = '';

		if(count($ingredientlist) > 0)
		{
			$i = 1;
			foreach($ingredientlist as $ingredient)
			{
						
				$result .= '<div class="box-body ingredientlist" id="ingredientlist'.$i.'">
							<div class="form-group full_selectList col-md-3">
								<div class="col-sm-12">
									<input type="text" class="form-control col-md-3" value="'.$ingredient->ingredientlist_name.'"/>
									<input type="hidden" name="ingredient_list_id['.$ingredient->ingredient_id.'][]" value="'.$ingredient->ingredientlist_id.'">
								</div>
							</div>
							<div class="form-group full_selectList col-md-3">
								<div class="col-sm-12">
									<input type="text" class="form-control col-md-3 allowOnlyPrice" placeholder="'.trans('messages.Price').'" name="item_price['.$ingredient->ingredient_id.'][]" value="'.$ingredient->price.'"/>
								</div>
							</div>
							<input type="hidden" id="ing_count" value="'.count($ingredientlist).'">
							<a href="javascript:void(0);" class="remove-ingredient" title="Remove field" style="color:#C20C0C">
								<i class="fa fa-minus-circle fa-fw"></i>
							</a></div>';
			$i++;
			}
			//exit;
			$minmax .= '<div class="form-group full_selectList col-md-3">
							<div class="col-sm-12">
								<input type="text" id="min_val" name="minimum[]" class="minimum form-control col-md-3" placeholder="'.trans('messages.Minimum').'"/>
							</div>
						</div>
						<div class="form-group full_selectList col-md-3">
							<div class="col-sm-12">
								<input type="text" id="max_val" class="maximum form-control col-md-3 allowOnlyPrice" placeholder="'.trans('messages.Maximum').'" name="maximum[]"/>
							</div>
						</div>
						
						<input type="hidden" class="ing_count" value="'.count($ingredientlist).'">
						<div class="form-group full_selectList col-md-3">
							<div class="col-sm-12">
								<input type="hidden" value="0" class="required_disable" name="required[]">
								<input type="checkbox" value="1" name="required[]" class="required_control"/> '.trans('messages.required').'
							</div>
							<span class="errors has-error" style="display: none;color:red;"></span>
						</div>';
		}
		
		$return = array('ingredientlist' => $result, 'minmax' => $minmax);
		return json_encode($return);
	}

	public function savesize()
	{
		$item_id = Input::get('item_id');
		$name = json_decode(Input::get('name'));
		$languages = DB::table('languages')->where('status', 1)->get();
		
		
		$price = Input::get('price');
		DB::table('item_size')->insert(['item_id' => $item_id, 'price' => $price, 'created_at' => date('Y-m-d H:i:s')]);			
		$size_id = DB::getPdo()->lastInsertId();
		foreach ($languages as $language) 
		{
			$code = $language->code;
			$value = $name->$code;
			DB::table('item_size_description')->insert(['item_id' => $item_id, 'item_size_id' => $size_id, 'size_name' => $value, 'language' => $language->code]); 
		}

		return $size_id;
	}

	public function removesize()
	{
		$id = Input::get('id');
		DB::table('item_size')->where('id', $id)->delete();
		DB::table('item_size_description')->where('item_size_id', $id)->delete();
		
		return 1;
	}

	public function getFoodicsItems()
	{
		$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcHAiLCJhcHAiOjIyLCJidXMiOjIyLCJjb21wIjoxOCwic2NydCI6Il82OTdkYTg3ZyJ9.JoCY2Ma8gahU7ESjj9LijPfcnDrIeBAmkFlfarVQUUg';
		
		$header = array();
		$header[] = 'Authorization: Bearer '.$access_token;
		$header[] = 'Content-type: application/json; charset=utf-8';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/products");
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
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/modifiers");
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
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/inventory-items");
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
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/delivery-zones");
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
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/products/".$id);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$item = json_decode($response);	

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/categories/".$item->product->category->hid);
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
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/modifiers/".$modifier->hid);
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
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/modifiers/".$id);
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
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/products/".$id);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		$item = json_decode($response);	

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/categories/".$item->product->category->hid);
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
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/modifiers/".$modifier->hid);
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
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/inventory-items");
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
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dev-dash.foodics.com/api/v2/branches");
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
