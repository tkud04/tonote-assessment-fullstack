<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Contracts\HelperContract; 
use Illuminate\Support\Facades\Auth;
use Session; 
use Validator; 
use Carbon\Carbon; 

class MainController extends Controller {

	protected $helpers; //Helpers implementation
    
    public function __construct(HelperContract $h)
    {
    	$this->helpers = $h;                     
    }

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getIndex()
    {
       $user = null;

		if(Auth::check())
		{
			$user = Auth::user();
		}

		
		$signals = $this->helpers->signals;
        #dd($user);
    	//return view('index',compact(['user','courses','signals']));
        return json_encode(['status' => "ok", 'message' => "Employee Management System"]);
    }


}