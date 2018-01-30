<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Ingredient extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ingredients';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['status'];
	
	

	public function getingredients()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$ingredients = DB::table('ingredients')
						->join('ingredient_description', 'ingredients.id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw(DB::getTablePrefix().'ingredients.*,'.DB::getTablePrefix().'ingredient_description.ingredient_name as ingredient')
						->where('ingredient_description.language', $language)
						->orderby('ingredients.id', 'desc')
						->paginate(10);
						 
		return $ingredients;
	}

	public function getAllIngredients()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$ingredients = DB::table('ingredients')
						->join('ingredient_description', 'ingredients.id', '=', 'ingredient_description.ingredient_id')
						->SelectRaw(DB::getTablePrefix().'ingredients.*,'.DB::getTablePrefix().'ingredient_description.ingredient_name as ingredient')
						->where('ingredient_description.language', $language)
						->orderby('ingredients.id', 'desc')
						->get();
						 
		return $ingredients;
	}

	public function getAllIngredientList()
	{

		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$ingredientlist = DB::table('ingredientlist')
						->join('ingredientlist_description', 'ingredientlist.id', '=', 'ingredientlist_description.ingredientlist_id')
						->SelectRaw(DB::getTablePrefix().'ingredientlist.*,'.DB::getTablePrefix().'ingredientlist_description.ingredientlist_name as ingredientlist')
						->where('ingredientlist_description.language', $language)
						->orderby('ingredientlist.id', 'desc')
						->get();
						 
		return $ingredientlist;
	}
	
	public function rules($inputs)
	{
	  $error_count = 0;
	  foreach($inputs as $key => $val)
	  {
		$array_error[$key] = ($val == '') ? 'required': '';
		if($val == '')
		{
			$error_count = 1;
		}
	  }

	  return array('array_error' => $array_error, 'error_count' => $error_count);
	}
}
