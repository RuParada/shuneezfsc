<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class Inotification extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'inotifications';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable = ['id', 'delivery_method', 'order_status', 'auto', 'message', 'created_at', 'updated_at'];

	public function getOrderMess()
	{
		//$language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		//$message = DB::table('vendor_items')
						//->join('orders');
						//->join('vendor_item_description', 'vendor_items.id', '=', 'vendor_item_description.item_id');
						 
		//return $message;
	}

	public function getMessages()
	{
		$notifications = DB::table('inotifications')->orderby('id', 'desc')->paginate(10);
		return $notifications;
	}

	public function save($delivery_method,$order_status,$message,$auto)
	{
		/*DB::table('notifications')->insert([							
        								[
        									'delivery_method'=>$delivery_method,
        									'order_status'=>$order_status,
								            'message'=>$message,
								            'auto'=>$auto
        								]
									]);*/
		Inotification::create([
            					'delivery_method'=>$delivery_method,
								'order_status'=>$order_status,
					            'message'=>$message,
					            'auto'=>$auto
					        ]);
	}
    
}
