<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Contracts\HelperContract; 
use Illuminate\Support\Facades\Auth;
use Session; 
use Validator; 
use Carbon\Carbon; 

class APIController extends Controller {

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
	public function getIndex(Request $request)
    {
       $req = $request->all();
       $user = null;

       if(isset($req['token']))
       {
           $user = $this->helpers->getUserViaToken($req['token']);

          if($user == null)
		  {
			$ret = ['status' => "error", 'message' => "Invalid token"];
		  }
          else
          {
            $ret = ['status' => "ok", 'message' => "API", "version" => "0.1.0"];
          }
       }
       else
       {
        $ret = ['status' => "error", 'message' => "Token not present"];
       }
       

        return json_encode($ret);
    }

    	/**
	 * GET /signup
	 *
	 * @return Response
	 */
	public function getCreateAdmin()
    {
       $req = $request->all();
       $user = null;
       
       if(isset($req['token']))
       {
           $user = $this->helpers->getUserViaToken($req['token']);

          if($user == null)
		  {
			$ret = ['status' => "error", 'message' => "Invalid token"];
		  }
          else
          {
            $ret = ['status' => "ok", 'message' => " GET /create-admin", "version" => "0.1.0"];
          }
       }
       else
       {
        $ret = ['status' => "error", 'message' => "Token not present"];
       }
       

        return json_encode($ret);
    }

    public function postCreateAdmin(Request $request)
    {
        $req = $request->all();
        #dd($req);
        $ret = ['status' => "error", 'message' => "Nothing happened"];
        
        $validator = Validator::make($req, [
                             'fname' => 'required',
                             'lname' => 'required',
                             'password' => 'required|confirmed',
                             'email' => 'required|email', 
                             //'role' => 'required',  
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
           $req['role'] = "admin";     
           $req['token'] = $this->helpers->createAPIToken();			
            
                       #dd($req);            

            $user =  $this->helpers->createUser($req); 
            
            $ret = ['status' => "ok", 'message' => "{$user->email} signed up successfully"];
          }

          return json_encode($ret);
    }

    /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getAdminLogin(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
             $ret = ['status' => "ok", 'message' => " GET /admin-login", "version" => "0.1.0"];
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }


	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
    public function postAdminLogin(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
            $ret = ['status' => "error", 'message' => "Nothing happened"];
            #dd($req);
            
            $validator = Validator::make($req, [
                                 'password' => 'required|min:6',
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
                if(Auth::attempt(['email' => $req['email'],'password' => $req['password'],'status'=> "ok"],$remember))
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
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
         return json_encode($ret);
    }


    /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getCreateEmployee(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
             $ret = ['status' => "ok", 'message' => " GET /new-employee", "version" => "0.1.0"];
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }


    /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
    public function postCreateEmployee(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
            
            $ret = ['status' => "error", 'message' => "Nothing happened"];
        
        $validator = Validator::make($req, [
                             'fname' => 'required',
                             'lname' => 'required',
                             'password' => 'required|confirmed',
                             'email' => 'required|email', 
                             'department' => 'required',
                             'salary' => 'required',
                             'benefits' => 'required'
                             //'role' => 'required',  
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
           $req['role'] = "employee";     
           $req['token'] = "";	
           
           
            
                       #dd($req);            

            $user =  $this->helpers->createUser($req); 

            //Employee details
           $req['employee_id'] = $user->id;
           $req['leave_status'] = "ineligible";
           $req['department'] = $req['department'];

           $e = $this->helpers->createEmployee($req);

           //Salary details
           $req['salary'] = $req['salary'];
           $req['benefits'] = $req['benefits'];

           $s = $this->helpers->createSalaryDetails($req);
           
            
            $ret = ['status' => "ok", 'message' => " Employee {$user->email} signed up successfully"];
          }
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
         return json_encode($ret);
    }

     /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getEmployees(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
               $employees = $this->helpers->getEmployees();
             $ret = ['status' => "ok", 'data' => $employees];
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }

     /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getEmployee(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
             if(isset($req['id']))
             {
                $employee = $this->helpers->getEmployee($req['id']);
             }
             else
             {
               $employee = [];
             }
             $ret = ['status' => "ok", 'data' => $employee];
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }

     /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getUpdateEmployee(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
             $ret = ['status' => "ok", 'message' => " GET /update-employee", "version" => "0.1.0"];
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }


     /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function postUpdateEmployee(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
            $ret = ['status' => "error", 'message' => "Nothing happened"];
            #dd($req);
            
            $validator = Validator::make($req, [
                                'employee_id' => 'required',
             ]);
             
             if($validator->fails())
             {
                 $messages = $validator->messages();
                 $ret['message'] = "Validation error";
             }
             
             else
             {
                $this->helpers->updateEmployee($req);
                $ret = ['status' => "ok", 'message' => "Employee information updated"];
             }
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }


     /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getDeleteEmployee(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
            $ret = ['status' => "error", 'message' => "Nothing happened"];
            #dd($req);
            
            $validator = Validator::make($req, [
                                'id' => 'required'
             ]);
             
             if($validator->fails())
             {
                 $messages = $validator->messages();
                 $ret['message'] = "Validation error";
             }
             
             else
             {
                $this->helpers->deleteEmployee($req['id']);
                $ret = ['status' => "ok", 'message' => "Employee information deleted"];
             }
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }


     /**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getAssignLeave(Request $request)
    {
        $req = $request->all();
        $user = null;
        
        if(isset($req['token']))
        {
            $user = $this->helpers->getUserViaToken($req['token']);
 
           if($user == null)
           {
             $ret = ['status' => "error", 'message' => "Invalid token"];
           }
           else
           {
            $ret = ['status' => "error", 'message' => "Nothing happened"];
            #dd($req);
            
            $validator = Validator::make($req, [
                                'id' => 'required'
             ]);
             
             if($validator->fails())
             {
                 $messages = $validator->messages();
                 $ret['message'] = "Validation error";
             }
             
             else
             {
                $this->helpers->assignLeave($req['id']);
                $ret = ['status' => "ok", 'message' => "Employee ID {$req['id']} has been assigned leave"];
             }
           }
        }
        else
        {
         $ret = ['status' => "error", 'message' => "Token not present"];
        }
        
 
         return json_encode($ret);
    }

	


}