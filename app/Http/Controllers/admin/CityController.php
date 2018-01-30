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
use App\City;
use View;

class CityController extends Controller {

	public function __construct(Guard $auth, City $city)
	{
		$this->middleware('adminauth');
		$this->auth = $auth;
	    $this->city = $city;
	    $this->prefix = DB::getTablePrefix();
	    $this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
		
	}
	
	public function getcities()
	{
		$cities = $this->city->getcities();		
		return view('admin/citylist', array('cities'=> $cities));
	}

	public function addcity()
	{
		$input = Input::all();
		$input['city'] = trim($input['city']);
		$state_id = Input::get('state_id');
		$city = Input::get('city');
		$valid = Validator::make($input,
								['city' => 'required|unique:city,city']);
		if($valid->fails())
		{
			return redirect("/admin/citylist")->with("error", $valid->errors());
		}
		else
		{
			DB::table('city')->insert(['city' => $city, 'status' => Input::get('status')]);
			return redirect('/admin/citylist')->with("success", "New city added successfully...");
		}
	}

	public function getcity($id)
	{
		$city = DB::table("city")->where('id', $id)->first();
		return view("admin/editcity", array("city" => $city));
	}

	public function updatecity()
	{
		$input = Input::all();
		$input['city'] = trim($input['city']);
		$id = Input::get('id');
		$city = Input::get('city');
		$status = Input::get('status');
		$valid = Validator::make($input,
								['city' => 'required|unique:city,city,'.$id]);
		if($valid->fails())
		{
			return redirect("/admin/citylist")->with("error", $valid->errors());
		}
		else
		{
			DB::table('city')->where("id", $id)->update(['city' => $city, 'status' => $status]);
			return redirect('/admin/citylist')->with("success", "City updated successfully...");
		}
	}

	public function deletecity($id)
	{
		DB::table("city")->where("id", $id)->delete();
		return redirect("/admin/citylist")->with("success", "City has been deleted successfully...");
	}
	
	public function change_citystatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 1) ? 0 : 1;
		DB::table('city')->where('id', $id)->update(['status' => $status]);

		$result = array('success' => 1, 'msg' => 'Status changed successfully...');
		return json_encode($result);
	}

	public function filtercity()
	{
		$status = Input::get('status');
		$city = Input::get('name');

		$cities = DB::table('city')
				  ->where(function($query) use ($status, $city)
				  {
					  if($city != '')
					  {
						  $query->where('city.city', 'like', '%'.$city.'%');
					  }
					  if($status != '')
					  {
						  $query->where('city.status', $status);
					  }
				  })
				  ->paginate(10);
		return view("admin/citylist", array("cities" => $cities));
	}
}
