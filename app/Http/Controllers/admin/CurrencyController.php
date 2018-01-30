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
use App\Currency;

class CurrencyController extends Controller {

	public function __construct(Currency $currency)
	{
		$this->middleware('adminauth');
        $this->currency = $currency; 
	}
        
    /*************** Get Currency List ****************/ 

	public function getcurrencies()
	{
		$currencies = $this->currency->getcurrencies();
		return view('admin/currencies', array('currencies' => $currencies));
	}
	
	public function addcurrency_form()
	{
		return view('admin/addcurrency');
	}

	public function addcurrency()
	{
		$input = Input::all();
		$input['currency'] = trim($input['currency_name']);
		$valid = Validator::make($input,
								['currency_name' => 'required|unique:currencies,currency_name',
								 'currency_symbol' => 'required',
								 'currency_code' => 'required']);
		if($valid->fails())
		{
			return redirect('admin/addcurrency')->with('error', $valid->errors());
		}
		else
		{
			$default = (Input::get('default_currency') != '') ? 1 : 0;
			$this->currency->currency_name = Input::get('currency_name');
			$this->currency->currency_symbol = Input::get('currency_symbol');
			$this->currency->currency_code = Input::get('currency_code');
			$this->currency->currency_position = Input::get('currency_position');
			$this->currency->status = Input::get('status');
			$this->currency->default_currency = $default;
			$this->currency->created_by = Session('admin_userid');
			
			if($default == 1)
			{
				DB::table('currencies')->where('default_currency', 1)->update(['default_currency' => 0]);
			}
			$this->currency->save();

			return redirect('admin/currencies')->with('success', trans('messages.Currency Add'));
		}
	}
	
	public function getcurrency($id)
	{
		$currency = DB::table('currencies')->where('id', $id)->first();			
		return view('admin/editcurrency', array('currency' => $currency));
	}

	public function updatecurrency()
	{
		$id = Input::get('id');
		$input = Input::all();
		$input['currency'] = trim($input['currency_name']);
		$valid = Validator::make($input,
								['currency_name' => 'required|unique:currencies,currency_name,'.$id,
								 'currency_symbol' => 'required',
								 'currency_code' => 'required']);
		if($valid->fails())
		{
			return redirect('admin/editcurrency/'.$id)->with('error', $valid->errors());
		}
		else
		{
			$default = (Input::get('default_currency') != '') ? 1 : 0;
			$this->currency->currency_name = Input::get('currency_name');
			$this->currency->currency_symbol = Input::get('currency_symbol');
			$this->currency->currency_code = Input::get('currency_code');
			$this->currency->currency_position = Input::get('currency_position');
			$this->currency->status = Input::get('status');
			$this->currency->default_currency = Input::get('default_currency');
			$this->currency->updated_by = Session('admin_userid');
			
			if($default == 1)
			{
				DB::table('currencies')->where('default_currency', 1)->update(['default_currency' => 0]);
			}
			
			DB::table('currencies')->where('id', $id)->update($this->currency['attributes']);

			return redirect('admin/currencies')->with('success', trans('messages.Currency Update'));
		}
	}

	public function change_currencystatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('currencies')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}

	public function deletecurrency($id)
	{
		DB::table('currencies')->where('id', $id)->delete();
		return redirect('admin/currencies')->with('success', trans('messages.Currency Delete'));
	}
	
	public function filtercurrencies()
	{
		$status = Input::get('status');
		$currency = Input::get('name');
		
		$currencies = DB::table('currencies')
				  ->where(function($query) use ($status, $currency)
				  {
					  if($currency != '')
					  {
						  $query->where('currency_name', 'like', '%'.$currency.'%')
						  ->OrWhere('currency_code', 'like', '%'.$currency.'%');
					  }
					  if($status != '')
					  {
						 $query->where('status', $status);
					  }
				  })
				  ->paginate(10);
						   
		return view('admin/currencies', array('currencies' => $currencies));
	}
            
}
