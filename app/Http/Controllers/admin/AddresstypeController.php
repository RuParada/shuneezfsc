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
use View;
use App\Addresstype;

class AddresstypeController extends Controller {

	public function __construct(Addresstype $addresstype)
	{
		$this->middleware('adminauth');
        $this->addresstype = $addresstype; 
	}
        
    /*************** Get Address type List ****************/ 

	public function getaddresstype()
	{
		$addresstype = $this->addresstype->getaddresstype();
		return view('admin/addresstype', array('addresstype' => $addresstype));
	}
	
	public function add_addresstype_form()
	{
		return view('admin/add_addresstype');
	}

	public function add_addresstype()
	{
		$valid = Validator::make(Input::all(),
								['addresstype' => 'required|unique:addresstype,addresstype']);
		if($valid->fails())
		{
			return redirect('admin/add_addresstype')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->addresstype->addresstype = Input::get('addresstype');
			$this->addresstype->status = Input::get('status');
			$this->addresstype->created_by = Session('admin_userid');

			$this->addresstype->save();
			
			return redirect('admin/addresstype')->with('success', trans('messages.Address Type Add'));
		}
	}
	
	public function getaddress($id)
	{
		$addresstype = DB::table('addresstype')->where('id', $id)->first();			
		return view('admin/edit_addresstype', array('addresstype' => $addresstype));
	}

	public function update_addresstype()
	{
		$id = Input::get('id');
		$input = Input::all();
		$input['addresstype'] = trim($input['addresstype']);
		$valid = Validator::make($input,
								['addresstype' => 'required|unique:addresstype,addresstype,'.$id]);
		if($valid->fails())
		{
			return redirect('admin/edit_addresstype/'.$id)->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->addresstype->addresstype = Input::get('addresstype');
			$this->addresstype->status = Input::get('status');
			$this->addresstype->created_by = Session('admin_userid');
			
			DB::table('addresstype')->where('id', $id)->update($this->addresstype['attributes']);

			return redirect('admin/addresstype')->with('success', trans('messages.Address Type Update'));
		}
	}

	public function change_addresstypestatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('addresstype')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}

	public function delete_addresstype($id)
	{
		DB::table('addresstype')->where('id', $id)->delete();
		return redirect('admin/addresstype')->with('success', trans('messages.Address Type Delete'));
	}
	
	public function filter_addresstype()
	{
		$status = Input::get('status');
		$addresstype = Input::get('name');
		
		$addresstype = DB::table('addresstype')
				  ->where(function($query) use ($status, $addresstype)
				  {
					  if($addresstype != '')
					  {
						  $query->where('addresstype', 'like', '%'.$addresstype.'%');
					  }
					  if($status != '')
					  {
						 $query->where('status', $status);
					  }
				  })
				  ->paginate(10);
						   
		return view('admin/addresstype', array('addresstype' => $addresstype));
	}
            
}
