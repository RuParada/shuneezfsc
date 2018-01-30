<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_key', 'first_name', 'last_name', 'email', 'mobile', 'status', 'is_delete', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];
    
    public function getusers() {
        $users = DB::table('users')->where('is_delete', 0)->orderby('id', 'desc')->orderby('id', 'desc')->paginate(10);

        return $users;
    }
    public function getallusers() {
        $users = DB::table('users')->where('is_delete', 0)->where('status', 1)->orderby('id', 'desc')->get();

        return $users;
    }

}
