<?php

namespace App\Http\Controllers\branch;

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
use App\Staff;
use App\Branch;
use DateTimeZone;
use DateTime;
use PHPMailer;
use Redirect;

class StaffController extends Controller {

    public function __construct(Guard $auth, Staff $staff, Branch $branch) {
        $this->middleware('branchauth');
        $this->auth = $auth;
        $this->staff = $staff;
        $this->branch = $branch;
        $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
    }

    /*     * ******** Get staffs *********** */

    public function getstaffs() {
        $staffs = $this->staff->getbranch_staffs(Session('branch_id'));
		return view('branch/staffs', array('staffs' => $staffs));
    }

    public function addstaff_form() {
	   return view('branch/addstaff');
    }

    public function addstaff() {
        $valid = Validator::make(Input::all(), ['branch' => 'required',
                    'name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                    'email' => 'required|email|unique:staffs,email',
                    'password' => 'required|min:6',
                    'mobile' => 'required|digits_between:9,13|unique:staffs,mobile']);
        if ($valid->fails()) {
            return redirect('branch/addstaff')->withInput(Input::all())->with('error', $valid->errors());
        } else {
			Random :
			$key = str_random(16);
			
			$key_exits = DB::table('staffs')->where('staff_key', $key)->count();
			if ($key_exits) { goto Random; }
			
			$this->staff->staff_key = $key;
            $this->staff->branch_id = Input::get('branch');
            $this->staff->name = Input::get('name');
            $this->staff->email = Input::get('email');
            $this->staff->mobile = Input::get('mobile');
            $this->staff->address = Input::get('address');
            $this->staff->status = Input::get('status');
            $this->staff->password = base64_encode(Input::get('password'));
            
            $this->staff->save();

            $name = Input::get('name');
            $email = Input::get('email');
            $password = Input::get('password');

			$msg = trans('messages.Hi'). " ".$name.",<br><br>".trans('messages.Account Creation')."<br><br> ".trans('messages.Find Credentials').": <br>".trans('messages.username').": ".$email."<br>".trans('messages.password').": ".$password." <br><br> ".trans('messages.Thank You').",<br>The Shuneez Team";
			$subject = "The Shunnez Registration";
			sendmail($email, $subject, $msg);
            return redirect('branch/staffs')->with('success', trans('messages.Staff Add'));
        }
    }

    /*     * ********Update staff Status*********** */

    public function change_staffstatus() {
        $id = Input::get('id');
        $status = (Input::get('status') == 0) ? 1 : 0;
        DB::table('staffs')->where('id', $id)->update(['status' => $status]);
        $result = array('success' => 1, 'msg' => trans('messages.Status Change'));
        return json_encode($result);
    }

    /*     * *******Get staff********* */

    public function getstaff($id) {
        $staff = DB::table('staffs')->where('id', $id)->first();
        return view('branch/editstaff', array('staff' => $staff));
    }

    /*     * *******Update staff********* */

    public function updatestaff() {
        $id = Input::get('id');
        $valid = Validator::make(Input::all(), ['branch' => 'required',
                    'name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                    'email' => 'required|email|unique:staffs,email,' . $id,
                    'mobile' => 'required|digits_between:9,13|unique:staffs,mobile,' . $id,
                    'password' => 'min:6',
                    ]);
        if ($valid->fails()) {
            return redirect('branch/getstaff/' . $id)->withInput(Input::all())->with('error', $valid->errors());
        } else {
            $this->staff->branch_id = Input::get('branch');
            $this->staff->name = Input::get('name');
            $this->staff->email = Input::get('email');
            $this->staff->mobile = Input::get('mobile');
            $this->staff->address = Input::get('address');
            $this->staff->status = Input::get('status');
            
            if(Input::get('password') != '')
            {
				$this->staff->password = base64_encode(Input::get('password'));
			}

            DB::table('staffs')->where('id', $id)->update($this->staff['attributes']);
            if(Session('is_manager'))
            {
				return redirect('admin/staffs')->with('success', trans('messages.Staff Update'));
			}
			else
			{
				Session::put('new_staff_name', Input::get('name'));
				return Redirect::back()->with('success', trans('messages.Staff Update'));
			}
            return redirect('branch/staffs')->with('success', trans('messages.Staff Update'));
        }
    }

    /*     * *******Delete staff********* */

    public function deletestaff($id) {
        $staff = DB::table('staffs')->where('id', $id)->first();
        DB::table('staffs')->where('id', $id)->delete();
        /* $name = $user->name;
          $email = $user->email;
          Mail::send([],
          array('email' => $email,'name' => $name), function($message) use ($email,$name)
          {
          $mail_body = "Hello {name},<br><br>You account has been deleted by admin. <br><br> Thank You,<br>The DoctorWeb Team";
          $mail_body = str_replace("{name}", $name, $mail_body);
          $message->setBody($mail_body, 'text/html');
          $message->to($email);
          $message->subject('DoctorWeb Account Delete');
          }); */
        return redirect('branch/staffs')->with('success', trans('messages.Staff Delete'));
    }

    /*     * ********Filter staffs*********** */

    public function filterstaffs() {
        $search = Input::get('name');
        $status = Input::get('status');

        $staffs = DB::table('staffs')
				->join('branch_description', 'staffs.branch_id', '=', 'branch_description.branch_id')
				->SelectRaw(DB::getTablePrefix().'staffs.*,'.DB::getTablePrefix().'branch_description.branch_name as branch')
				->where(function($query) use($search, $status) {
                    if ($search != '') {
                        $query->where('name', 'like', '%' . $search . '%')
                        ->OrWhere('mobile', 'like', '%' . $search . '%')
                        ->OrWhere('email', 'like', '%' . $search . '%');
					}
                })
                ->where(function($qry) use($status) {
                    if ($status != '') {
                        $qry->where('status', $status);
                    }
                })
                ->where('staffs.branch_id', Session('branch_id'))
                ->where('branch_description.language', $this->current_language)
                ->orderby('id', 'desc')
                ->paginate(10);

        return view('branch/staffs', array('staffs' => $staffs));
    }
}
