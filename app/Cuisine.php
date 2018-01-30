<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Cuisine extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cuisines';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['cuisine_key', 'status', 'created_by', 'updated_by'];
	
	public function getcuisines()
	{
		$cuisines = DB::table('cuisines')
						->join('cuisine_description', 'cuisines.id', '=', 'cuisine_description.cuisine_id')
						->SelectRaw(DB::getTablePrefix().'cuisines.*,'.DB::getTablePrefix().'cuisine_description.cuisine_name as cuisine')
						->where('cuisines.status', 1)
						->where('cuisine_description.language', 'en')
						->paginate(10);
						 
		return $cuisines;
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
