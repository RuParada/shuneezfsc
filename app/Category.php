<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Category extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'categories';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['category_key', 'image', 'is_delete', 'status', 'created_by', 'updated_by'];
	
	

	public function getcategories()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$categories = DB::table('categories')
						->join('category_description', 'categories.id', '=', 'category_description.category_id')
						->SelectRaw(DB::getTablePrefix().'categories.*,'.DB::getTablePrefix().'category_description.category_name as category')
						->where('category_description.language', $language)
						->where('categories.is_delete', 0)
						->paginate(10);
						 
		return $categories;
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
