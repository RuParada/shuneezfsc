<?php namespace App\Http\Controllers\branch;

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
use App\Branch;
use App\Deliveryboy;
use App\Vendor;
use App\Language;
use View;
use App\City;
use PHPMailer;
use Redirect;

class BranchController extends Controller {

	public function __construct(Guard $auth, Branch $branch, Vendor $vendor, Language $language, City $city, Deliveryboy $deliveryboy)
	{
		$this->middleware('branchauth');
		$this->auth = $auth;
	    $this->branch = $branch;
	    $this->vendor = $vendor;
	    $this->language = $language;
	    $this->city = $city;
	    $this->deliveryboy = $deliveryboy;
	    $this->prefix = DB::getTablePrefix();
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		
	}
	
	public function changelanguage($language)
	{
		$_SESSION['language'] = ($language != '') ? $language : 'en';
		return Redirect::back();
	}
	
	
	public function dashboard()
	{
		$deliveryboys = $this->deliveryboy->getbranch_deliveryboys(Session('branch_id'));
		$total['overall'] = DB::table('orders')
							->where('branch_id', Session('branch_id'))
							->where('payment_status', 's')
							->sum('order_total');
		$total['month'] = DB::table('orders')
							->where('branch_id', Session('branch_id'))
							->whereMonth('created_at', '=', date('m'))
							->where('payment_status', 's')
							->sum('order_total');
		$total['today'] = DB::table('orders')
							->where('branch_id', Session('branch_id'))
							->whereDay('created_at', '=', date('d'))
							->where('payment_status', 's')
							->sum('order_total');
		$deliverboy_orders = DB::select("SELECT d.id, dd.deliveryboy_name, accepted_order, cancelled_order
								FROM sh_deliveryboys as d
								LEFT JOIN 
								(
								SELECT
								d.id,
								count(dr1.id) as accepted_order
								FROM sh_deliveryboys as d
								LEFT JOIN sh_deliveryboy_request as dr1 ON d.id = dr1.deliveryboy_id AND dr1.status = 'a'
								LEFT JOIN sh_orders as o ON dr1.order_id = o.id AND o.order_status = 'c'
								GROUP BY d.id) as accept on d.id=accept.id
								LEFT JOIN 
								(
								SELECT
								d.id,
								count(dr2.id) as cancelled_order
								FROM sh_deliveryboys as d
								LEFT JOIN sh_deliveryboy_request as dr2 ON d.id = dr2.deliveryboy_id AND dr2.status = 'd'
								LEFT JOIN sh_orders as o ON dr2.order_id = o.id AND o.order_status = 'c'
								GROUP BY d.id) as cancel on d.id=cancel.id
								JOIN sh_deliveryboy_description as dd ON d.id = dd.deliveryboy_id AND dd.language = '".$this->current_language."'
								AND d.branch_id = ".Session('branch_id')."");
		return view('branch/dashboard', array('sale' => $total, 'deliveryboys' => $deliveryboys, 'deliverboy_orders' => $deliverboy_orders));
	}
	
	public function getbranch()
	{
		$id = Session('branch_id');
		$branch = DB::table('branches')->where('id', $id)->first();
		$languages = $this->language->getlanguages();
		$vendor = DB::table('vendor_description')->where('language', $this->current_language)->first();
		$workingtimes = DB::table('vendor_timeslot')->where('branch_id', $id)->get();
		return view('branch/editbranch', array('vendor' => $vendor, 'branch' => $branch, 'languages' => $languages, 'workingtimes' => $workingtimes));
	}
	
	public function updatebranch()
	{
		$branches = Input::get('branch');
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
									['email' => 'required|unique:branches,email,'.$id,
									 'mobile' => 'required|numeric|unique:branches,mobile,'.$id,
									 'distance' => 'required|numeric',
									 'delivery_fee' => 'required|numeric',
									 'additional_charge' => 'required|numeric',
									 'password' => 'min:6'
									]);
		$array_valid = $this->branch->rules($branches);
		if($array_valid['error_count'])
		{
			return redirect('branch/editbranch/'.$id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('branch/editbranch/'.$id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			
			$this->branch->email = Input::get('email');
			$this->branch->mobile = Input::get('mobile');
			if(Input::get('password') != '')
			{
				$this->branch->password = base64_encode(Input::get('password'));
			}
			$this->branch->status = Input::get('status');
			$this->branch->city = Input::get('city');
			$this->branch->street = Input::get('street');
			$this->branch->country = Input::get('country');
			$this->branch->zipcode = Input::get('zipcode');
			$this->branch->latitude = Input::get('latitude');
			$this->branch->longitude = Input::get('longitude');
			$this->branch->preorder = Input::get('preorder');
			$this->branch->delivery_type = Input::get('delivery_type');
			$this->branch->deliveryfee_type = 'd';
			$this->branch->delivery_fee = Input::get('delivery_fee');
			$this->branch->distance = Input::get('distance');
			$this->branch->additional_charge = Input::get('additional_charge');
			$this->branch->meta_keywords = Input::get('keywords');
			
			DB::table('branches')->where('id', $id)->update($this->branch['attributes']);
			
			$languages = Input::get('language');
			$branch = Input::get('branch');
			
			DB::table('branch_description')->where('branch_id', $id)->delete();
			if(count($branch) > 0)
			{
				for($i=0; $i<count($branch); $i++)
				{
					DB::table('branch_description')->insert(['branch_id' => $id, 'branch_name' => $branch[$i], 'language' => $languages[$i]]);
				}
			}
			
			$branch = $this->branch->getbranch($id);
			Session::put('new_name', $branch->branch); 
			return redirect('branch/editbranch')->with('success', trans('messages.Branch Update'));
		}
	}

	public function branch_workingtime($id)
	{
		$id = Session('branch_id');
		$workingtimes = $this->branch->getworkingtimes($id);
		$branch = $this->branch->getbranch($id);
		
		return view('branch/branch_workingtime', array('workingtimes' => $workingtimes, 'branch_id' => $id, 'branch' => $branch));
	}

	public function delete_timeslot($id)
	{
		DB::table('vendor_timeslot')->where('id', $id)->delete();
		return Redirect::back()->with('success', trans('messages.Timeslot Delete'));
	}

	public function update_branch_workingtime()
	{
		$branch_id = Input::get('branch_id');
		$days = explode('|', Input::get('day'));
		$start_time = Input::get('start_time');
		$close_time = Input::get('close_time');

		for($i=0; $i<count($start_time); $i++)
		{
			$is_exist = DB::table('vendor_timeslot')->where(['branch_id' => $branch_id, 'working_day' => $days[0], 'start_time' => date('H:i', strtotime($start_time[$i])), 'close_time' => date('H:i', strtotime($close_time[$i]))])->count();
			if($is_exist == 0)
			{
				DB::table('vendor_timeslot')->insert(['branch_id' => $branch_id, 'working_day' => $days[0], 'start_time' => date('H:i', strtotime($start_time[$i])), 'close_time' => date('H:i', strtotime($close_time[$i])), 'sort_number' => $days[1]]);
			}
		}

		return Redirect::back()->with('success', trans('messages.Timeslot Update'));
	}
	
	public function logout()
	{
		unset($_SESSION['language']);
		Session::flush();
		return redirect('/admin');
	}

}
