<?php

namespace App\Http\Controllers\admin;

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
use DateTimeZone;
use DateTime;

class AdminuserController extends Controller {

    public function __construct(Guard $auth, Adminuser $adminuser) {
        $this->middleware('adminauth');
        $this->auth = $auth;
        $this->adminuser = $adminuser;
    }

    /*     * ******** Get Admin Users *********** */

    public function getadminusers() {
        $adminusers = $this->adminuser->getadminusers();
		return view('admin/adminusers', array('adminusers' => $adminusers));
    }

    public function addadminuser_form() {
       return view('admin/addadminuser');
    }

    public function addadminuser() {
        $valid = Validator::make(Input::all(), ['name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
					'email' => 'required|email|unique:adminusers,email|unique:staffs,email|unique:branches,email',
                    'password' => 'required|min:6',
                    'username' => 'required|unique:adminusers,username']);
        if ($valid->fails()) {
            return redirect('admin/addadminuser')->withInput(Input::all())->with('error', $valid->errors());
        } else {
			$this->adminuser->name = Input::get('name');
            $this->adminuser->username = Input::get('username');
            $this->adminuser->email = Input::get('email');
            $this->adminuser->status = Input::get('status');
            $this->adminuser->add_privilege = (Input::get('add_privilege') != '') ? Input::get('add_privilege') : 0 ;
            $this->adminuser->edit_privilege = (Input::get('edit_privilege') != '') ? Input::get('edit_privilege') : 0;
            $this->adminuser->delete_privilege = (Input::get('delete_privilege') != '') ? Input::get('delete_privilege') : 0;
            $this->adminuser->password = base64_encode(Input::get('password'));
            
            $this->adminuser->save();

            $name = Input::get('name');
            $email = Input::get('email');
            $password = Input::get('password');

			/* $this->SendSMS($message, $mobile);

              Mail::send([],
              array('pass' => $password,'email' => $email,'name' => $name), function($message) use ($password,$email,$name)
              {
              $mail_body = "Hello {name},<br><br>You account has been succesfully created by admin. <br><br> You can find your credentials below: <br>Username: {email}<br>Password: {password} <br><br> Thank You,<br>The DoctorWeb Team";
              $mail_body = str_replace("{password}", $password, $mail_body);
              $mail_body = str_replace("{email}", $email, $mail_body);
              $mail_body = str_replace("{name}", $name, $mail_body);
              $message->setBody($mail_body, 'text/html');
              $message->to($email);
              $message->subject('DoctorWeb Registration');
              }); */
            return redirect('admin/adminusers')->with('success', trans('messages.Adminuser Add'));
        }
    }

    /*     * ********Update User Status*********** */

    public function change_adminuserstatus() {
        $id = Input::get('id');
        $status = (Input::get('status') == 0) ? 1 : 0;
        DB::table('adminusers')->where('id', $id)->update(['status' => $status]);
        $result = array('success' => 1, 'msg' => trans('messages.Status Change'));
        return json_encode($result);
    }

    /*     * *******Get User********* */

    public function getadminuser($id) {
        $adminuser = DB::table('adminusers')->where('id', $id)->first();
        return view('admin/editadminuser', array('adminuser' => $adminuser));
    }

    /*     * *******Update User********* */

    public function updateadminuser() {
        $id = Input::get('id');
        $valid = Validator::make(Input::all(), ['name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                    'email' => 'required|email|unique:staffs,email|unique:branches,email|unique:adminusers,email,' . $id,
                    'username' => 'required|unique:adminusers,username,' . $id,
                    'password' => 'min:6'
                    ]);
        if ($valid->fails()) {
            return redirect('admin/getadminuser/' . $id)->withInput(Input::all())->with('error', $valid->errors());
        } else {
            $this->adminuser->name = Input::get('name');
            $this->adminuser->username = Input::get('username');
            $this->adminuser->email = Input::get('email');
            $this->adminuser->status = Input::get('status');
            $this->adminuser->add_privilege = (Input::get('add_privilege') != '') ? Input::get('add_privilege') : 0 ;
            $this->adminuser->edit_privilege = (Input::get('edit_privilege') != '') ? Input::get('edit_privilege') : 0;
            $this->adminuser->delete_privilege = (Input::get('delete_privilege') != '') ? Input::get('delete_privilege') : 0;
            if(Input::get('password') != '')
            {
				$this->adminuser->password = base64_encode(Input::get('password'));
			}

            DB::table('adminusers')->where('id', $id)->update($this->adminuser['attributes']);
            return redirect('admin/adminusers')->with('success', trans('messages.Adminuser Update'));
        }
    }

    /*     * *******Delete User********* */

    public function deleteadminuser($id) {
        DB::table('adminusers')->where('id', $id)->delete();
        return redirect('admin/adminusers')->with('success', trans('messages.Adminuser Delete'));
    }

    
    public function filteradminusers() {
        $search = Input::get('name');
        $status = Input::get('status');

        $adminusers = DB::table('adminusers')
				->where('superadmin', 0)
				->where(function($query) use($search, $status) {
                    if ($search != '') {
                        $query->where('name', 'like', '%' . $search . '%')
                        ->OrWhere('username', 'like', '%' . $search . '%')
                        ->OrWhere('email', 'like', '%' . $search . '%');
                    } 
                })
                ->where(function($qry) use($status) {
                    if ($status != '') {
                      $qry->where('status', $status);
                    }
                })
                ->orderby('id', 'desc')
                ->paginate(10);

        return view('admin/adminusers', array('adminusers' => $adminusers));
    }
}
