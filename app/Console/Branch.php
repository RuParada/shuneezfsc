<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Branch extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;
	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'branches';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['vendor_id', 'branch_key', 'email', 'mobile', 'password', 'street', 'country', 'city', 'zipcode', 'latitude', 'longitude', 'image', 'is_delete', 'status', 'created_by', 'updated_by'];
	
	protected $hidden = ['password', 'remember_token'];

	public function getbranches()
	{
		$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$branches = DB::table('branches')
						->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
						->Join('vendor_description', 'branches.vendor_id', '=', 'vendor_description.vendor_id')
						->SelectRaw(DB::getTablePrefix().'branches.*,'.DB::getTablePrefix().'branch_description.branch_name as branch,'.DB::getTablePrefix().'vendor_description.vendor_name as vendor')
						->where('branch_description.language', $language)
						->where('vendor_description.language', $language)
						->where('branches.is_delete', 0)
						->paginate(10);
		return $branches;
	}
}
