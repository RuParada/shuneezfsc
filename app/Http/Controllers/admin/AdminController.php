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
use App\Adminuser;
use App\Branch;
use App\Deliveryboy;
use View;
use Redirect;

class AdminController extends Controller {

	public function __construct(Guard $auth, Adminuser $adminuser, Branch $branch, Deliveryboy $deliveryboy)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->adminuser = $adminuser;
		$this->branch = $branch;
		$this->deliveryboy = $deliveryboy;
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		$settings = DB::table('settings')->get();
	    foreach ($settings as $setting) 
		{
			$config_data[$setting->setting_name] = $setting->setting_value;
		}
		
		$this->config_data = $config_data;
		View::share (['config_data'=> $config_data, 'default_currency' => getdefault_currency()]);
	}
	
	public function changelanguage($language)
	{
		$_SESSION['language'] = ($language != '') ? $language : 'en';
		return Redirect::back();
	}
	
	public function dashboard($branch_id = '')
	{
		$branches = $this->branch->getbranches();
		$deliveryboys = $this->deliveryboy->getdelivery_boys();
		$total['overall'] = DB::table('orders')
							->SelectRaw('sum(order_total) as order_total, count(id) as order_count')
							->where(function($query) use($branch_id)
							{
								if($branch_id != '')
								{
									$query->where('branch_id', $branch_id);
								}
							})
							->where('payment_status', 's')
							->first();
		$total['month'] = DB::table('orders')
							->SelectRaw('sum(order_total) as order_total, count(id) as order_count')
							->where(function($query) use($branch_id)
							{
								if($branch_id != '')
								{
									$query->where('branch_id', $branch_id);
								}
							})
							->whereMonth('created_at', '=', date('m'))
							->where('payment_status', 's')
							->first();
		$total['today'] = DB::table('orders')
							->SelectRaw('sum(order_total) as order_total, count(id) as order_count')
							->where(function($query) use($branch_id)
							{
								if($branch_id != '')
								{
									$query->where('branch_id', $branch_id);
								}
							})
							->whereDay('created_at', '=', date('d'))
							->where('payment_status', 's')
							->first();
		$deliverboy_orders = DB::select("SELECT d.id, dd.deliveryboy_name, accepted_order, cancelled_order
								FROM sh_deliveryboys as d
								LEFT JOIN 
								(
								SELECT
								d.id,
								count(dr1.id) as accepted_order
								FROM sh_deliveryboys as d
								LEFT JOIN sh_deliveryboy_request as dr1 ON d.id = dr1.deliveryboy_id AND dr1.status = 'a'
								GROUP BY d.id) as accept on d.id=accept.id
								LEFT JOIN 
								(
								SELECT
								d.id,
								count(dr2.id) as cancelled_order
								FROM sh_deliveryboys as d
								LEFT JOIN sh_deliveryboy_request as dr2 ON d.id = dr2.deliveryboy_id AND dr2.status = 'd'
								GROUP BY d.id) as cancel on d.id=cancel.id
								JOIN sh_deliveryboy_description as dd ON d.id = dd.deliveryboy_id AND dd.language = '".$this->current_language."'
								"); 
		return view('admin/dashboard', array('sale' => $total, 'branches' => $branches, 'deliveryboys' => $deliveryboys, 'deliverboy_orders' => $deliverboy_orders));
	}

	/*********** Update Admin User Profile ************/
	
	public function updateadmin()
	{
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
								['name' => 'required',
								 'email' => 'required|email|unique:adminusers,email,'.$id,
								 'username' => 'required',
								 'password' => 'min:5']);
		if($valid->fails())
		{
			return redirect('admin/settings')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->adminuser->name = Input::get('name');
			$this->adminuser->email = Input::get('email');
			$this->adminuser->username = Input::get('username');
			if(Input::get('password') != '')
			{
				$this->adminuser->password = base64_encode(Input::get('password'));
			}
			DB::table('adminusers')->where('id', $id)->update($this->adminuser['attributes']);
			$details = array('new_name' => Input::get('name'), 'new_email' => Input::get('email'), 'new_username' => Input::get('username'));
			Session::put($details); 
			return redirect('admin/settings')->with('success', 'Profile Updated successfully...');
		}
	}

	/***** Site Settings & Admin Profile *****/

	public function settings()
	{
		$settings = DB::table('settings')->get();
        $smtp_settings = DB::table('smtp_settings')->get();
		return view('admin/settings', array('settings' => $settings, 'smtp_settings' => $smtp_settings));
	}

	public function updatesite_settings()
	{
		$valid = Validator::make(Input::all(),
								 ['email' => 'required|email',
								  'mobile' => 'required|numeric',
								  'vat' => 'required|numeric',
								  'start_time' => 'required|numeric',
								  'end_time' => 'required|numeric',
								  'dook_company_id' => 'required',
								  'dook_access_token' => 'required',
								  'dook_order_status' => 'required',
								  'dook_fleet_owner_id' => 'required',
								  'foodics_access_token' => 'required',
								 ]);
		if($valid->fails())
		{
			return redirect('admin/settings')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{

			$post = Input::except(array('_token'));
			//$post['service_tax'] = (isset($post['service_tax'])) ? $post['service_tax'] : 0;
			$post['vat'] = (isset($post['vat'])) ? $post['vat'] : 0;
			$post['delivery_time'] = (isset($post['delivery_time'])) ? $post['delivery_time'] : 0;
			$post['pickup_time'] = (isset($post['pickup_time'])) ? $post['pickup_time'] : 0;
			$post['deliveryboy_radius'] = (isset($post['deliveryboy_radius'])) ? $post['deliveryboy_radius'] : 0;
			$post['order_accept_timelimit'] = (isset($post['order_accept_timelimit'])) ? ($post['order_accept_timelimit'] * 60) : 0;
			$post['auto_allocation'] = (isset($post['auto_allocation'])) ? $post['auto_allocation'] : 0;
			$post['email'] = (isset($post['email'])) ? $post['email'] : '';
			$post['mobile'] = (isset($post['mobile'])) ? $post['mobile'] : '';
            $post['copyright'] = (isset($post['copyright'])) ? $post['copyright'] : '';
			$post['address'] = (isset($post['address'])) ? $post['address'] : '';
            $post['meta_keyword'] = (isset($post['meta_keyword'])) ? $post['meta_keyword'] : '';
            $post['meta_description'] = (isset($post['meta_description'])) ? $post['meta_description'] : '';
			$post['facebook'] = (isset($post['facebook'])) ? $post['facebook'] : '';
			$post['twitter'] = (isset($post['twitter'])) ? $post['twitter'] : '';
			$post['googleplus'] = (isset($post['googleplus'])) ? $post['googleplus'] : '';
			//$post['youtube'] = (isset($post['youtube'])) ? $post['youtube'] : '';
			//print_r($post); exit;
			foreach($post as $key=>$value) 
			{
 				$setting_value['setting_value'] = $value;
 				DB::table('settings')->where('setting_name', $key)->update($setting_value);
			}
			return redirect('admin/settings')->with('success', 'Site settings updated successfully...');
		}
	}
        
        /******* Update SMTP Settings ********/

	public function updatesmtp_settings()
	{
		$valid = Validator::make(Input::all(),
                                        ['smtp_username' => 'required',
                                         'smtp_password' => 'required',
                                         'host' => 'required',
                                         'port' => 'required|numeric',
                                         'security' => 'required',
                                        ]);
		if($valid->fails())
		{
			return redirect('admin/settings')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{

			$post = Input::except(array('_token'));
			$post['smtp_username'] = (isset($post['smtp_username'])) ? $post['smtp_username'] : '';
			$post['smtp_password'] = (isset($post['smtp_password'])) ? $post['smtp_password'] : '';
			$post['host'] = (isset($post['host'])) ? $post['host'] : '';
            $post['port'] = (isset($post['port'])) ? $post['port'] : '';
            $post['security'] = (isset($post['security'])) ? $post['security'] : '';
			
//print_r($post); exit;
			foreach($post as $key=>$value) 
			{
 				$setting_value['setting_value'] = $value;
 				DB::table('smtp_settings')->where('setting_name', $key)->update($setting_value);
			}
			return redirect('admin/settings')->with('success', 'SMTP settings updated successfully...');
		}
	}
        
        /******* Update Image Settings ********/

	public function updateimage_settings()
	{
		$valid = Validator::make(Input::all(),
                                        ['logo' => 'mimes:jpg,jpeg,png',
                                         'favicon' => 'mimes:jpg,jpeg,png,ico',
                                         'image' => 'mimes:jpg,jpeg,png',
                                         'banner' => 'mimes:jpg,jpeg,png',
                                        ]);
		if($valid->fails())
		{
			return redirect('admin/settings')->with('error', $valid->errors());
		}
		else
		{
        	$image = '';
            $dest = 'assets/uploads/settings';
			if(Input::file('logo') != '')
            {
                $image['logo'] = str_random(6).Input::file('logo')->getClientOriginalName();
                Input::file('logo')->move($dest, $image['logo']);
            }
            if(Input::file('favicon') != '')
            {
                $image['favicon'] = str_random(6).Input::file('favicon')->getClientOriginalName();
                Input::file('favicon')->move($dest, $image['favicon']);
            }
            if(Input::file('image') != '')
            {
                $image['noimage'] = str_random(6).Input::file('image')->getClientOriginalName();
                Input::file('image')->move($dest, $image['noimage']);
            }
            if(Input::file('banner') != '')
            {
                $image['banner'] = str_random(6).Input::file('banner')->getClientOriginalName();
                Input::file('banner')->move($dest, $image['banner']);
            }
            
            if($image != '')
            {
                foreach($image as $key=>$value) 
                {
                        $setting_value['setting_value'] = $value;
                        DB::table('settings')->where('setting_name', $key)->update($setting_value);
                }
            }
			return redirect('admin/settings')->with('success', 'Image settings updated successfully...');
		}
	}
	
	public function logout()
	{
		Session::flush();
		unset($_SESSION['language']);
		return redirect('/admin');
	}
	
	public function viewnotifications()
	{
		return view('admin/view_notifications');
	}
	public function notifications()
	{
		$notifications = DB::table('notifications')->orderby('id', 'desc')->get();
		return view('admin/notifications', array('notifications' => $notifications));
	}
	public function changestatus()
	{
		DB::table('notifications')->update(['IsView' => 1]);
	}
}
