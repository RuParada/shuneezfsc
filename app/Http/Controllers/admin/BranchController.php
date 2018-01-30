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
use App\Branch;
use App\Vendor;
use App\Language;
use View;
use App\City;
use PHPMailer;
use Redirect;

class BranchController extends Controller {

	public function __construct(Guard $auth, Branch $branch, Vendor $vendor, Language $language, City $city)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->branch = $branch;
	    $this->vendor = $vendor;
	    $this->language = $language;
	    $this->city = $city;
	    $this->prefix = DB::getTablePrefix();
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		
	}
	
	/*********** Get Branches***************/
	
	public function getbranches()
	{
		$branches = $this->branch->getbranches();
		return view('admin/branches', array('branches' => $branches));
	}
	
	public function addbranch_form()
	{
		$languages = $this->language->getlanguages();
		$citylist = $this->city->getcities();
		$vendor = DB::table('vendor_description')->where('language', $this->current_language)->first();
		
		return view('admin/addbranch', array('languages' => $languages, 'vendor' => $vendor, 'citylist' => $citylist));
		
	}
	
	public function addbranch()
	{
		$branches = Input::get('branch');
		$valid = Validator::make(Input::all(),
									['email' => 'required|email|unique:branches,email|unique:staffs,email|unique:adminusers,email',
									 'mobile' => 'required|unique:branches,mobile|numeric|digits_between:9,13',
									 'distance' => 'required|numeric',
									 'delivery_fee' => 'required|numeric',
									 'additional_charge' => 'required|numeric',
									 'password' => 'required|min:6'
									]);
		$array_valid = $this->branch->rules($branches);
		if($array_valid['error_count'])
		{
			return redirect('admin/addbranch')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/addbranch')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$delivery_type = Input::get('deliveryfee_type');
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('branches')->where('branch_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->branch->branch_key = $key;
			$this->branch->email = Input::get('email');
			$this->branch->mobile = Input::get('mobile');
			$this->branch->password = base64_encode(Input::get('password'));
			$this->branch->status = Input::get('status');
			$this->branch->city = Input::get('city');
			$this->branch->street = Input::get('street');
			$this->branch->country = Input::get('country');
			$this->branch->zipcode = Input::get('zipcode');
			$this->branch->latitude = Input::get('latitude');
			$this->branch->longitude = Input::get('longitude');
			$this->branch->preorder = Input::get('preorder');
			$this->branch->deliveryfee_type = 'd';
			$this->branch->delivery_type = Input::get('delivery_type');
			$this->branch->delivery_fee = Input::get('delivery_fee');
			$this->branch->distance = Input::get('distance');
			$this->branch->additional_charge = Input::get('additional_charge');
			$this->branch->meta_keywords = Input::get('keywords');
			
			$this->branch->save();
			
			$branch_id = $this->branch->id;
			
			$languages = Input::get('language');
			$branch = Input::get('branch');
			
			if(count($branch) > 0)
			{
				for($i=0; $i<count($branch); $i++)
				{
					DB::table('branch_description')->insert(['branch_id' => $branch_id, 'branch_name' => $branch[$i], 'language' => $languages[$i]]);
				}
			}
			
			
			/*$citylist = Input::get('city_id');
			
			if($delivery_type == 'area')
			{
				if(count($citylist) > 0)
				{
					for($i=0; $i<count($citylist); $i++)
					{
						DB::table('branch_deliveryarea')->insert(['branch_id' => $branch_id, 'city_id' => $citylist[$i]]);
					}
				}
			}*/
			
			$email = Input::get('email');
			$password = Input::get('password');
			
			$msg = "Hi ,<br><br>You new branch was created successfully.<br><br> You can find Your credentials below: <br>Username: ".$email."<br>Password: ".$password." <br><br> Thank You,<br>The Shuneez Team";
			$subject = "The Shunnez Registration";
			$this->sendmail($email, $subject, $msg);
			return redirect('admin/branches')->with('success', trans('messages.Branch Add'));
		}
	}
	
	public function getbranch($id)
	{
		$branch = DB::table('branches')->where('id', $id)->first();
		$languages = $this->language->getlanguages();
		$vendor = DB::table('vendor_description')->where('language', $this->current_language)->first();
		$workingtimes = DB::table('vendor_timeslot')->where('branch_id', $id)->get();
		$deliveryareas = DB::table('branch_deliveryarea')->where('branch_id', $id)->get();
		$citylist = $this->city->getcities();
		return view('admin/editbranch', array('vendor' => $vendor, 'deliveryareas' => $deliveryareas, 'citylist' => $citylist, 'branch' => $branch, 'languages' => $languages, 'workingtimes' => $workingtimes));
	}
	
	public function updatebranch()
	{
		$branches = Input::get('branch');
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
									['mobile' => 'required|digits_between:9,13|numeric',
									 'email' => 'required|email|unique:staffs,email|unique:adminusers,email|unique:branches,mobile,'.$id,
									 'distance' => 'required|numeric',
									 'delivery_fee' => 'required|numeric',
									 'additional_charge' => 'required|numeric',
									 'password' => 'min:6'
									]);
		$array_valid = $this->branch->rules($branches);
		if($array_valid['error_count'])
		{
			return redirect('admin/editbranch/'.$id)->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/editbranch/'.$id)->WithInput(Input::all())->with('error', $valid->errors());
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
			
			return redirect('admin/branches')->with('success', trans('messages.Branch Update'));
		}
	}
	
	/**********Update branch Status************/

	public function change_branchstatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('branches')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}
	
	public function filterbranches()
	{
		$name = Input::get('name');
		$status = Input::get('status');
		
		$branches = DB::table('branches')
				->join('branch_description', 'branches.id', '=', 'branch_description.branch_id')
				->SelectRaw(DB::getTablePrefix().'branches.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
				->where('branch_description.language', $this->current_language)
				->where(function($query) use($name, $status)
				{
					if($name != '')
					{
						$query->where('branch_description.branch_name', 'like', '%'.$name.'%');
					}
					if($status != '')
					{
						if($status == 'deleted')
						{
							$query->where('branches.is_delete', 1);
						}
						else
						{
							$query->where('branches.status', $status);
						}
					}
					else
					{
						$query->where('branches.is_delete', 0);
					}
				})
				->paginate(10);
				
		return view('admin/branches', array('branches' => $branches));
	}
	
	/************* Delete branch ***************/
	
	public function deletebranch($id)
	{
		DB::table('branches')->where('id', $id)->update(['is_delete' => 1]);
		return redirect('admin/branches')->with('success', trans('messages.Branch Delete'));
	}
	
	/************* Restore Deleted branch ***************/
	
	public function restorebranch($id)
	{
		DB::table('branches')->where('id', $id)->update(['is_delete' => 0]);
		return redirect('admin/branches')->with('success', trans('messages.Branch Restore'));
	}

	public function branch_workingtime($id)
	{
		$workingtimes = $this->branch->getworkingtimes($id);
		$branch = $this->branch->getbranch($id);
		return view('admin/branch_workingtime', array('workingtimes' => $workingtimes, 'branch' => $branch));
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
	
	public function sendmail($email, $subject, $msg)
	{
		include('mail/class.phpmailer.php');
		
		$mail = new PHPMailer(); // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->IsHTML(true);
		$mail->Username = "order@shuneezfsc.com";
		$mail->Password = "shuneez123";
		$mail->SetFrom("order@shuneezfsc.com");
		$mail->Subject = $subject;
		$mail->Body = $msg;
		$mail->AddAddress($email);
		$mail->Send();
		
		return 1;
	}

}
