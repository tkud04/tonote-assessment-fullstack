<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Contracts\HelperContract; 
use Illuminate\Support\Facades\Auth;
use Session; 
use Validator; 
use Carbon\Carbon; 
use App\Models\User;

class LoginController extends Controller {

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
	public function getSignup()
    {
       $user = null;
       $ret = ['status' => "ok", 'message' => "Nothing happened"];

		if(Auth::check())
		{
			$user = Auth::user();
            $ret['message'] = "A user is already logged in";
		}

        return json_encode($ret);
    }

    
    /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getLogin(Request $request)
    {
       $user = null;
       $ret = ['status' => "ok", 'message' => "Nothing happened"];
       $req = $request->all();
       //$return = isset($req['return']) ? $req['return'] : '/';
		
		if(Auth::check())
		{
			$user = Auth::user();
			$ret['message'] = "A user is already logged in";
		}

        return json_encode($ret);
    }


	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
    public function postLogin(Request $request)
    {
        $req = $request->all();
        $ret = ['status' => "error", 'message' => "Nothing happened"];
        #dd($req);
        
        $validator = Validator::make($req, [
                             'pass' => 'required|min:6',
                             'email' => 'required|email'
         ]);
         
         if($validator->fails())
         {
             $messages = $validator->messages();
             $ret['message'] = "Validation error";
         }
         
         else
         {
			
         	$remember = true; 
            
         	//authenticate this login
            if(Auth::attempt(['email' => $req['id'],'password' => $req['pass'],'status'=> "ok"],$remember))
            {
            	//Login successful               
               $user = Auth::user();          
               # dd($user); 
				              
                 $ret = ['status' => "ok", 'message' => "{$user->email} logged in successfully"];
            }
			
			else
			{
				$ret['message'] = "Invalid email or password";
			}
         }
         
         return json_encode($ret);
    }


    
 
	
    public function postSignup(Request $request)
    {
        $req = $request->all();
        #dd($req);
        $ret = ['status' => "error", 'message' => "Nothing happened"];
        
        $validator = Validator::make($req, [
                             'fname' => 'required',
                             'lname' => 'required',
                             'password' => 'required|confirmed',
                             'email' => 'required|email', 
                             'role' => 'required',  
                             #'g-recaptcha-response' => 'required',
                           # 'terms' => 'accepted',
         ]);
         
         if($validator->fails())
         {
             $messages = $validator->messages();
             //dd($messages);
             $ret['message'] = "Validation error";
         }
         
         else
         {
			 #dd($req);
           $req['status'] = "ok";       			
            
                       #dd($req);            

            $user =  $this->helpers->createUser($req); 
            
            $ret = ['status' => "ok", 'message' => "{$user->email} signed up successfully"];
          }

          return json_encode($ret);
    }
	
	 public function getForgotPassword()
    {
    	$user = null;
		
		if(Auth::check())
		{
			$user = Auth::user();
			return redirect()->intended('/');
		}
		$signals = $this->helpers->signals;
         return view('reset-password', compact(['user']));
    }
    
    /**
     * Send username to the given user.
     * @param  \Illuminate\Http\Request  $request
     */
    public function postForgotPassword(Request $request)
    {
    	$req = $request->all(); 
        $validator = Validator::make($req, [
                             'id' => 'required'
                  ]);
                  
        if($validator->fails())
         {
             $messages = $validator->messages();
             //dd($messages);
             
             return redirect()->back()->withInput()->with('errors',$messages);
         }
         
         else{
         	$ret = $req['id'];

                $user = User::where('email',$ret)
                                  ->orWhere('phone',$ret)->first();

                if(is_null($user))
                {
                        return redirect()->back()->withErrors("No admin account exists with that email or phone number!","errors"); 
                }
                
                //get the reset code 
                $code = $this->helpers->getPasswordResetCode($user);
              
                //Configure the smtp sender
                $sender = $this->helpers->emailConfig;              
                $sender['sn'] = 'KloudTransact Support'; 
                #$sender['se'] = 'kloudtransact@gmail.com'; 
                $sender['em'] = $user->email; 
                $sender['subject'] = 'Reset Your Password'; 
                $sender['link'] = 'www.kloudtransact.com'; 
                $sender['ll'] = url('reset').'?code='.$code; 
                
                //Send password reset link
                $this->helpers->sendEmailSMTP($sender,'emails.password','view');                                                         
            session()->flash("forgot-password-status","ok");           
            return redirect()->intended('login');

      }
                  
    }    

    
    public function getLogout()
    {
        $ret = ['status' => "error", 'message' => "Nothing happened"];
        if(Auth::check())
        {  
           Auth::logout();      
           $ret = ['status' => "ok", 'message' => "{$user->email} logged out successfully"]; 	
        }
        
        return json_encode($ret);
    }

}