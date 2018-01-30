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
use App\Ingredient;
use App\Language;
use View;

class IngredientController extends Controller {

	public function __construct(Guard $auth, Ingredient $ingredient, Language $language)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->ingredient = $ingredient;
	    $this->language = $language;
		$this->prefix = DB::getTablePrefix();
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	
	/*********** Get Categories ***************/
	
	public function getingredients()
	{
		$ingredients = $this->ingredient->getingredients();

		//print_r($ingredients);exit;				 
		return view('admin/ingredients', array('ingredients' => $ingredients));
	}
	
	/*********** Add Ingredient Form *******************/
	
	public function addingredient_form()
	{
		$languages = $this->language->getlanguages();
		return view('admin/addingredient', array('languages' => $languages));
	}
	
	/************* Insert Ingredient *******************/
	
	Public function addingredient()
	{
		$ingredients = Input::get('ingredient_name');
		$valid = Validator::make(Input::all(),
								['status' => 'required']);
		$array_valid = $this->ingredient->rules($ingredients);
		if($array_valid['error_count'])
		{
			return redirect('admin/addingredient')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
								
		if($valid->fails())
		{
			return redirect('admin/addingredient')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{ 
		    $this->ingredient->status = Input::get('status');
			$this->ingredient->save();
			
			$ingredient_id = $this->ingredient->id;
			$language = Input::get('language');
			$ingredient_name = Input::get('ingredient_name');
			if(count($ingredient_name) > 0)
			{
				for($i=0; $i< count($ingredient_name); $i++)
				{ 
					DB::table('ingredient_description')->insert(['ingredient_id' => $ingredient_id, 'ingredient_name' => $ingredient_name[$i], 'language' => $language[$i]]);
				}
			}
			
			$is_ingredientlist = Input::get('ingredient_list');
			$ingredientlist = array_values(Input::get('ingredientlist'));
			if($is_ingredientlist && count($ingredientlist) > 0)
			{
				for($i=0; $i<count($ingredientlist); $i++)
				{
					$price = $ingredientlist[$i]['price'];
					DB::table('ingredientlist')->insert(['ingredient_id' => $ingredient_id, 'price' => $price, 'created_at' => date('Y-m-d H:i:s')]);			
					$ingredientlist_id = DB::getPdo()->lastInsertId();
					if(count($language) > 0)
					{
						for($j=0; $j<count($language); $j++)
						{
							$name = $ingredientlist[$i]['name'][$language[$j]];
							DB::table('ingredientlist_description')->insert(['ingredientlist_id' => $ingredientlist_id, 'ingredientlist_name' => $name, 'language' => $language[$j]]);
						}
					}
				}
			}
			return redirect('admin/ingredients')->with('success', trans('messages.Ingredients Add'));
		}	
	}
	
	/*************** Get Ingredient *********************/
	
	public function getingredient($id)
	{
		$languages = $this->language->getlanguages();
		$ingredient = DB::table('ingredients')->where('id', $id)->first();
		
		return view('admin/editingredient', array('languages' => $languages, 'ingredient' => $ingredient));
	}
	
	/************* Update Ingredient *******************/
	
	Public function updateingredient()
	{
		$ingredient_id = Input::get('id');
		$ingredients = Input::get('ingredient_name');
		$valid = Validator::make(Input::all(),
								['status' => 'required']);
		$array_valid = $this->ingredient->rules($ingredients);
		if($array_valid['error_count'])
		{
			return redirect('admin/editingredient/'.$ingredient_id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
								
		if($valid->fails())
		{
			return redirect('admin/editingredient/'.$ingredient_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{ 
		    $this->ingredient->status = Input::get('status');
			
			DB::table('ingredients')->where('id', $ingredient_id)->update($this->ingredient['attributes']);
			
			$language = Input::get('language');
			$ingredient_name = Input::get('ingredient_name');
			DB::table('ingredient_description')->where('ingredient_id', $ingredient_id)->delete();
			if(count($ingredient_name) > 0)
			{
				for($i=0; $i< count($ingredient_name); $i++)
				{ 
					DB::table('ingredient_description')->insert(['ingredient_id' => $ingredient_id, 'ingredient_name' => $ingredient_name[$i], 'language' => $language[$i]]);
				}
			}
			
			return redirect('admin/ingredients')->with('success', trans('messages.Ingredients Update'));
		}			
	}
	
	/**********Update Ingredient Status************/

	public function change_ingredientstatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('ingredients')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}
	
	/*********** Filter Ingredients ***************/
	
	public function filteringredient()
	{
		$ingredient = Input::get('name');
		$status = Input::get('status');
		
		$ingredients = DB::table('ingredients')
						->join('ingredient_description', 'ingredients.id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw($this->prefix.'ingredients.*,'.$this->prefix.'ingredient_description.ingredient_name as ingredient')
						->where(function($query) use($ingredient, $status)
						{
							if($ingredient != '')
							{
								$query->where('ingredient_description.ingredient_name', 'like', '%'.$ingredient.'%');
							}
							if($status != '')
							{
								$query->where('ingredients.status', $status);
							}
						})
						->where('ingredient_description.language', $this->current_language)
						->paginate(10);
						 
		return view('admin/ingredients', array('ingredients' => $ingredients));
	}
	
	/************* Delete Ingredient ***************/
	
	public function deleteingredient($id)
	{
		$data = DB::table('ingredientlist')->where('ingredient_id', $id)->get();
		if(count($data))
		{
			foreach($data as $row)
			{
				DB::table('ingredientlist_description')->where('ingredientlist_id', $row->id)->delete();
			}
		}
		DB::table('ingredientlist')->where('ingredient_id', $id)->delete();
		DB::table('ingredient_description')->where('ingredient_id', $id)->delete();
		$ingredients = DB::table('vendor_item_ingredients')->where('ingredient_id', $id)->get();
		if(count($ingredients))
		{
			foreach($ingredients as $ingredient)
			{
				DB::table('vendor_item_ingredientlist')->where('item_ingredient_id', $ingredient->id)->delete();
			}
		}
		DB::table('vendor_item_ingredients')->where('ingredient_id', $id)->delete();
		DB::table('ingredients')->where('id', $id)->delete();
		return redirect('admin/ingredients')->with('success', trans('messages.Ingredients Delete'));
	}
	
	
	public function deleteingredientlist($id)
	{
		DB::table('ingredientlist')->where('id', $id)->delete();
		DB::table('ingredientlist_description')->where('ingredientlist_id', $id)->delete();
		DB::table('vendor_item_ingredientlist')->where('item_ingredientlist_id', $id)->delete();
		
		return redirect('admin/ingredientlist')->with('success', trans('messages.Ingredientlist Delete'));;
	}
	
	public function removeingredientlist()
	{
		$id = Input::get('id');
		DB::table('ingredientlist')->where('id', $id)->delete();
		DB::table('ingredientlist_description')->where('ingredientlist_id', $id)->delete();
		DB::table('vendor_item_ingredientlist')->where('item_ingredientlist_id', $id)->delete();
		
		return 1;
	}
	
	public function ingredientlist()
	{
		$ingredients = DB::table('ingredientlist')
						->join('ingredientlist_description', 'ingredientlist.id', '=', 'ingredientlist_description.ingredientlist_id')
						->join('ingredient_description', 'ingredientlist.ingredient_id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw($this->prefix.'ingredientlist.*,'.$this->prefix.'ingredientlist_description.ingredientlist_name as ingredient_type,'.$this->prefix.'ingredient_description.ingredient_name as ingredient')
						->where('ingredientlist_description.language', $this->current_language)
						->where('ingredient_description.language', $this->current_language)
						->paginate(10);
		return view('admin/ingredientlist', array('ingredients' => $ingredients));
	}

	public function saveingredientlist()
	{
		$ingredient_id = Input::get('ingredient_id');
		$name_en = Input::get('name_en');
		$name_ar = Input::get('name_ar');
		$price = Input::get('price');

		DB::table('ingredientlist')->insert(['ingredient_id' => $ingredient_id, 'price' => $price, 'created_at' => date('Y-m-d H:i:s')]);			
		$ingredientlist_id = DB::getPdo()->lastInsertId();
		DB::table('ingredientlist_description')->insert(['ingredientlist_id' => $ingredientlist_id, 'ingredientlist_name' => $name_en, 'language' => 'en']);
		DB::table('ingredientlist_description')->insert(['ingredientlist_id' => $ingredientlist_id, 'ingredientlist_name' => $name_ar, 'language' => 'ar']);

		return 1;
	}	

}
