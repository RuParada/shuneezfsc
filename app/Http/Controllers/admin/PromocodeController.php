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
use App\Promocode;
use App\Language;
use App\User;
use View;
use PHPMailer;

include('mail/class.phpmailer.php');

class PromocodeController extends Controller {

	public function __construct(Guard $auth, Promocode $promocode, Language $language, User $user)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->promocode = $promocode;
	    $this->language = $language;
	    $this->user = $user;
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	
	/*********** Get promocode ***************/
	
	public function getpromocodes()
	{
		$promocodes = $this->promocode->getpromocodes();
						 
		return view('admin/promocodes', array('promocodes' => $promocodes));
	}
	
	/*********** Add promocode Form *******************/
	
	public function addpromocode_form()
	{
		return view('admin/addpromocode');
	}
	
	/************* Insert promocode *******************/
	
	Public function addpromocode()
	{
		$valid = Validator::make(Input::all(),
								 ['promocode' => 'required|unique:promocodes,promocode',
								  'amount' => 'required|numeric',
								  'expiry_date' => 'required',
								  'discount_type' => 'required']);
		if($valid->fails())
		{
			return redirect('admin/addpromocode')->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->promocode->promocode = Input::get('promocode');
			$this->promocode->amount = Input::get('amount');
			$this->promocode->expiry_date = date('Y-m-d', strtotime(Input::get('expiry_date')));
			$this->promocode->discount_type = Input::get('discount_type');
			$this->promocode->created_by = Session('admin_userid');
			$this->promocode->created_at = date('Y-m-d H:i:s');
			
			$this->promocode->save();
			
			return redirect('admin/promocodes')->with('success', trans('messages.Promocode Add'));
		}			
	}
	
	/*************** Get Promocode *********************/
	
	public function getpromocode($id)
	{
		$promocode = DB::table('promocodes')->where('id', $id)->first();
		
		return view('admin/editpromocode', array('promocode' => $promocode));
	}
	
	/************* Update promocode *******************/
	
	Public function updatepromocode()
	{
		$promocode_id = Input::get('id');
		$valid = Validator::make(Input::all(),
								 ['promocode' => 'required|unique:promocodes,promocode,'.$promocode_id,
								  'amount' => 'required|numeric',
								  'expiry_date' => 'required',
								  'discount_type' => 'required']);
		if($valid->fails())
		{
			return redirect('admin/updatepromocode/'.$promocode_id)->WithInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->promocode->promocode = Input::get('promocode');
			$this->promocode->amount = Input::get('amount');
			$this->promocode->expiry_date = date('Y-m-d', strtotime(Input::get('expiry_date')));
			$this->promocode->discount_type = Input::get('discount_type');
			$this->promocode->updated_by = Session('admin_userid');
			
			DB::table('promocodes')->where('id', $promocode_id)->update($this->promocode['attributes']);
			
			return redirect('admin/promocodes')->with('success', trans('messages.Promocode Update'));
		}			
	}
	
	/*********** Filter Categories ***************/
	
	public function filterpromocodes()
	{
		$promocode = Input::get('name');
		
		$promocodes = DB::table('promocodes')
						->where(function($query) use($promocode)
						{
							if($promocode != '')
							{
								$query->where('promocode', 'like', '%'.$promocode.'%');
							}
						})
						->paginate(10);
						 
		return view('admin/promocodes', array('promocodes' => $promocodes));
	}
	
	/************* Delete promocode ***************/
	
	public function deletepromocode($id)
	{
		DB::table('promocodes')->where('id', $id)->delete();
		return redirect('admin/promocodes')->with('success', trans('messages.Promocode Delete'));
	}
	
	public function sendpromocode_form($id)
	{
		$promocode = DB::table('promocodes')->where('id', $id)->first();
		$users = $this->user->getallusers();
		return view('admin/sendpromocode', array('promocode' => $promocode, 'users' => $users));
	}
	
	public function sendpromocode()
	{
		$email = Input::get('customer');
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
								 ['customer' => 'required']);
		if($valid->fails())
		{
			return redirect('admin/sendpromocode/'.$id)->with('error', $valid->errors());
		}
		else
		{
			DB::table('promocodes')->where('id', $id)->update(['is_used' => 1]);
			$promocode = DB::table('promocodes')->where('id', $id)->first();
			$subject = 'Promocode - Shuneez';
			$msg = 'Hi, Please find your promocode '.$promocode->promocode.' Expiry date '.date('d-m-Y', strtotime($promocode->expiry_date));
			if (in_array("All", $email))
			{
				$users = $this->user->getallusers();
				if(count($users))
				{
					foreach($users as $user)
					{
						DB::table('promocode_users')->insert(['customer_id' => $user->id, 'promocode_id' => $id]);
						$this->sendmail($user->email, $subject, $msg);
					}
				}
			}
			else
			{
				for($i=0; $i<count($email); $i++)
				{
					$user = DB::table('users')->where('email', $email[$i])->first();
					DB::table('promocode_users')->insert(['customer_id' => $user->id, 'promocode_id' => $id]);
					$this->sendmail($email[$i], $subject, $msg);
				}
			}
			return redirect('admin/promocodes')->with('success', trans('messages.Promocode Send'));
		}
	}
	
	public function sendmail($email, $subject, $msg)
	{
		$mail = '';
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
