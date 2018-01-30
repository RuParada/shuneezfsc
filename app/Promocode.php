<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Promocode extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'promocodes';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['promocode', 'validity', 'discount_type', 'amount', 'status'];
	
	

	public function getpromocodes()
	{
		$promocodes = DB::table('promocodes')->orderby('id', 'desc')->paginate(10);
						 
		return $promocodes;
	}
}
