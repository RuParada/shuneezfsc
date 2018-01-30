<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Staff extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'staffs';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['staff_key', 'branch_id', 'name', 'email', 'mobile', 'status', 'address'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password'];
	
	public function getstaffs() {
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $staffs = DB::table('staffs')
				  ->join('branch_description', 'staffs.branch_id', '=', 'branch_description.branch_id')
				  ->SelectRaw(DB::getTablePrefix().'staffs.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
				  ->where('branch_description.language', $language)
				  ->orderby('staffs.id', 'desc')
				  ->paginate(10);
		return $staffs;
    }
    
    public function getbranch_staffs($branch_id) {
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
        $staffs = DB::table('staffs')
				  ->join('branch_description', 'staffs.branch_id', '=', 'branch_description.branch_id')
				  ->SelectRaw(DB::getTablePrefix().'staffs.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
				  ->where('branch_description.language', $language)
				  ->where('staffs.branch_id', $branch_id)
				  ->orderby('staffs.id', 'desc')
				  ->paginate(10);
		return $staffs;
    }

}
