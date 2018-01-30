<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Enquiry extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'enquires';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'mobile', 'subject', 'message'];
	
	

	public function getenquires()
	{	
		$enquires = DB::table('enquires')->orderby('id', 'desc')->paginate(10);
		return $enquires;
	}
}
