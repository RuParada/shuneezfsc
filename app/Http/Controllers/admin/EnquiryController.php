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
use App\Enquiry;

class EnquiryController extends Controller {

	public function __construct(Enquiry $enquiry)
	{
		$this->middleware('adminauth');
		$this->enquiry = $enquiry;
		
	}
	    
	/*************Manage Contact Enquires****************/
	
	public function getenquires()
	{
		$enquires = DB::table('enquires')->orderby('id', 'desc')->paginate(10);
		return view('/admin/enquires', array('enquires' => $enquires));
	}
	
	public function viewenquiry($id)
	{
		$enquiry = DB::table('enquires')->where('id', $id)->first();
		return view('/admin/viewenquiry', array('enquiry' => $enquiry));
	}
	
	public function delete_enquiry($id)
	{
		DB::table('enquires')->where('id', $id)->delete();
		return redirect('/admin/enquires')->with('success' , 'Enquiry details deleted successfully...');
	}
	
	/**********Filter Enquires************/

	public function filterenquires()
	{
		$search = Input::get('name');
		$status = Input::get('status');
		
		$enquires = DB::table('enquires')
				->where(function($query) use($search, $status)
				{
					if($search != '')
					{
						$query->where('name', 'like', '%'.$search.'%')
							  ->OrWhere('mobile', 'like', '%'.$search.'%')
							  ->OrWhere('email', 'like', '%'.$search.'%');
					}
				})
				->orderby('id', 'desc')
				->paginate(10);
				
		return view('admin/enquires', array('enquires' => $enquires));
	}
}
