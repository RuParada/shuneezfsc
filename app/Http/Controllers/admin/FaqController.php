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
use App\Faq;
use App\Language;

class FaqController extends Controller {

	public function __construct(Faq $faq, Language $language)
	{
		$this->middleware('adminauth');
		$this->faq = $faq;
		$this->language = $language;
		$this->current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en';
	}
	  
	/************* Manage FAQ's*****************/
	
	public function getfaqs()
	{
		$faqs = $this->faq->getfaqs();
		return view('admin/faq', array('faqs' => $faqs));
	}
	
	public function addfaq_form()
	{
		$languages = $this->language->getlanguages();
		return view('admin/addfaq', array('languages' => $languages));
	}
	
	public function addfaq()
	{
		$questions = Input::get('question');
		$answers = Input::get('answer');
		$valid = Validator::make(Input::all(),
										['order' => 'required|integer']);
		$array_valid = $this->faq->rules($questions);
		if($array_valid['error_count'])
		{
			return redirect('admin/addfaq')->WithInput(Input::all())->with('array_valid', $array_valid['array_error']);
		}
		$array_valid = $this->faq->rules($answers);
		if($array_valid['error_count'])
		{
			return redirect('admin/addfaq')->WithInput(Input::all())->with('answer_valid', $array_valid['array_error']);
		}
		if($valid->fails())
		{
			return redirect('admin/addfaq')->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->faq->order = Input::get('order');
			$this->faq->status = Input::get('status');
			$this->faq->created_by = Session('admin_userid');
			$this->faq->save();
			
			$faq_id = $this->faq->id;
			$language = Input::get('language');
			$question = Input::get('question');
			$answer = Input::get('answer');
			
			if(count($question))
			{
				for($i=0; $i<count($question); $i++)
				{
					DB::table('faq_description')->insert(['faq_id' => $faq_id, 'question' => $question[$i], 'answer' => $answer[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/faqs')->with('success', trans('messages.Faq Add'));
		}
	}
		
	/**********Update FAQ Status************/

	public function change_faqstatus()
	{
		$id = Input::get('id');
		$status = (Input::get('status') == 0) ? 1 : 0;
		DB::table('faq')->where('id', $id)->update(['status' => $status]);
		$result = array('success' => 1, 'msg' => trans('messages.Status Change'));
		return json_encode($result);
	}

	/*********Get FAQ**********/

	public function getfaq($id)
	{
		$languages = $this->language->getlanguages();
		$faq = DB::table('faq')->where('id', $id)->first();
        return view('admin/editfaq', array('faq' => $faq, 'languages' => $languages));
	}

	/*********Update FAQ**********/

	public function updatefaq()
	{
		$id = Input::get('id');
		$valid = Validator::make(Input::all(),
                                        ['order' => 'required|integer'
                                        ]);
		if($valid->fails())
		{
			return redirect('admin/editfaq/'.$id)->withInput(Input::all())->with('error', $valid->errors());
		}
		else
		{
			$this->faq->order = Input::get('order');
			$this->faq->status = Input::get('status');
			$this->faq->updated_by = Session('admin_userid');
			DB::table('faq')->where('id', $id)->update($this->faq['attributes']);
			
			$language = Input::get('language');
			$question = Input::get('question');
			$answer = Input::get('answer');
			
			DB::table('faq_description')->where('faq_id', $id)->delete();
			if(count($question))
			{
				for($i=0; $i<count($question); $i++)
				{
					DB::table('faq_description')->insert(['faq_id' => $id, 'question' => $question[$i], 'answer' => $answer[$i], 'language' => $language[$i]]);
				}
			}
			return redirect('admin/faqs')->with('success', trans('messages.Faq Update'));
		}
	}

	/*********Delete FAQ**********/

	public function deletefaq($id)
	{
		$faq = DB::table('faq')->where('id', $id)->delete();
		$faq = DB::table('faq_description')->where('faq_id', $id)->delete();
		return redirect('admin/faqs')->with('success', trans('messages.Faq Delete'));
	}
	
	/**********Filter Faq************/

	public function filterfaqs()
	{
		$search = Input::get('name');
		$status = Input::get('status');
		
		$faqs = DB::table('faq')
				->join('faq_description', 'faq.id', '=', 'faq_description.faq_id')
				->where(function($query) use($search)
				{
					if($search != '')
					{
						$query->where('faq_description.question', 'like', '%'.$search.'%')
							  ->OrWhere('faq_description.answer', 'like', '%'.$search.'%');
                                        }
				})
                                ->where(function($query) use($status)
				{
					if($status != '')
					{
						$query->where('faq.status', $status);
					}
				})
				->where('faq_description.language', $this->current_language) 
				->orderby('faq.id', 'desc')
				->paginate(10);
				
		return view('admin/faq', array('faqs' => $faqs));
	}
        
 }
