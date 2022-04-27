<?php
namespace App\Helpers;

use App\Helpers\Contracts\HelperContract; 
use Crypt;
use Carbon\Carbon; 
use Mail;
use Auth;
use \Swift_Mailer;
use \Swift_SmtpTransport;
use App\Models\User;
use App\Models\Employees;
use App\Models\SalaryDetails;
use GuzzleHttp\Client;

class Helper implements HelperContract
{    

            public $emailConfig = [
                           'ss' => 'smtp.gmail.com',
                           'se' => 'uwantbrendacolson@gmail.com',
                           'sp' => '587',
                           'su' => 'uwantbrendacolson@gmail.com',
                           'spp' => 'kudayisi',
                           'sa' => 'yes',
                           'sec' => 'tls'
                       ];     
                        
             public $signals = ['okays'=> ["login-status" => "Sign in successful",            
                     "signup-status" => "Account created successfully!",

                     ],
                     'errors'=> ["login-status-error" => "There was a problem signing in, please contact support.",
					 "signup-status-error" => "There was a problem signing in, please contact support.",
					
                    ]
                   ];


          function sendEmailSMTP($data,$view,$type="view")
           {
           	    // Setup a new SmtpTransport instance for new SMTP
                $transport = "";
if($data['sec'] != "none") $transport = new Swift_SmtpTransport($data['ss'], $data['sp'], $data['sec']);

else $transport = new Swift_SmtpTransport($data['ss'], $data['sp']);

   if($data['sa'] != "no"){
                  $transport->setUsername($data['su']);
                  $transport->setPassword($data['spp']);
     }
// Assign a new SmtpTransport to SwiftMailer
$smtp = new Swift_Mailer($transport);

// Assign it to the Laravel Mailer
Mail::setSwiftMailer($smtp);

$se = $data['se'];
$sn = $data['sn'];
$to = $data['em'];
$subject = $data['subject'];
                   if($type == "view")
                   {
                     Mail::send($view,$data,function($message) use($to,$subject,$se,$sn){
                           $message->from($se,$sn);
                           $message->to($to);
                           $message->subject($subject);
                          if(isset($data["has_attachments"]) && $data["has_attachments"] == "yes")
                          {
                          	foreach($data["attachments"] as $a) $message->attach($a);
                          } 
						  $message->getSwiftMessage()
						  ->getHeaders()
						  ->addTextHeader('x-mailgun-native-send', 'true');
                     });
                   }

                   elseif($type == "raw")
                   {
                     Mail::raw($view,$data,function($message) use($to,$subject,$se,$sn){
                            $message->from($se,$sn);
                           $message->to($to);
                           $message->subject($subject);
                           if(isset($data["has_attachments"]) && $data["has_attachments"] == "yes")
                          {
                          	foreach($data["attachments"] as $a) $message->attach($a);
                          } 
                     });
                   }
           }    

           function createUser($data)
           {
           	$ret = User::create(['fname' => $data['fname'], 
                                                      'lname' => $data['lname'], 
                                                      'email' => $data['email'], 
                                                     'role' => $data['role'], 
                                                      'status' => $data['status'], 
                                                      'token' => $data['token'],
                                                     'password' => bcrypt($data['password']), 
                                                      'remember_token' => "default",
                                                      'reset_code' => "default"
                                                      ]);
                                                      
                return $ret;
           }

           
           function createAPIToken()
           {
           	$ret = "empl_mgt_".rand(99999,9999999999);
                                                      
                return $ret;
           }

           function getUserViaToken($token)
           {
               $ret = null;

               if(isset($token))
               {
                 $ret = User::where('token',$token)->first();
               }

               return $ret;
           }
           
          
          function createEmployee($data)
          {
              $ret = Employees::create([
                  'employee_id' => $data['employee_id'],
                  'leave_status' => $data['leave_status'],
                  'department' => $data['department']
              ]);

              return $ret;
          }

          function getEmployees($params=[])
          {
            $ret = [];
            $employees = Employees::where('id','>','0')->get();

           if($employees != null)
            {
                foreach($employees as $e)
                {
                    $temp = $this->getEmployee($e->id,$params); 
                    array_push($ret,$temp); 
                }
                    
            }                                     
             return $ret;
          }

          function getEmployeeDetails($id)
          {
            $ret = [];
            $e = Employees::where('employee_id',$id)->first();

           if($e != null)
            {
                    $temp['id'] = $e->id; 
                    $temp['employee_id'] = $e->employee_id; 
                    $temp['leave_status'] = $e->leave_status; 
                    $temp['department'] = $e->department; 
                    $temp['date'] = $e->created_at->format("jS F, Y"); 
                    $ret = $temp; 
            }                          
                                                   
             return $ret;
          }

          function getEmployee($id)
          {
            $ret = [];
            $e = User::where(['id' => $id, 'role' => "employee"])->first();

           if($e != null)
            {
                    $temp['id'] = $e->id; 
                    $temp['employee_id'] = $e->employee_id; 
                    $temp['leave_status'] = $e->leave_status; 
                    $temp['department'] = $e->department; 
                    $temp['details'] = $this->getEmployeeDetails($e->id);
                    $temp['salary'] = $this->getSalaryDetails($e->id);
                    $temp['date'] = $e->created_at->format("jS F, Y"); 
                    $ret = $temp; 
            }                          
                                                   
             return $ret;
          }

           function updateEmployee($data)
           {
            $e = Employees::where('employee_id',$data['employee_id'])->first();
            $s = SalaryDetails::where('employee_id',$data['employee_id'])->first();
            
              if($e != null && $s != null)
               {
                   $temp = []; $temp2 = [];
                   if(isset($data['leave_status'])) $temp['leave_status'] = $data['leave_status'];
                   if(isset($data['department'])) $temp['department'] = $data['department'];
                   if(isset($data['salary'])) $temp2['salary'] = $data['salary'];
                   if(isset($data['benefits'])) $temp2['benefits'] = $data['benefits'];
               	   $e->update($temp);  
                   $s->update($temp2);             
               }
           }	

           function deleteEmployee($id)
           {
           	$u = User::where('id',$id)->first();
            
              if($u != null)
               {
                   $s = SalaryDetails::where('employee_id',$id)->first();
                   $e = Employees::where('employee_id',$id)->first();
                   $e->delete(); 
                   $s->delete();
                   $u->delete();              
               }
           }  
           
           
           function createSalaryDetails($data)
           {
               $ret = SalaryDetails::create([
                   'employee_id' => $data['employee_id'],
                   'salary' => $data['salary'],
                   'benefits' => $data['benefits']
               ]);
 
               return $ret;
           }

           function getSalaryDetails($id)
          {
            $ret = [];
            $e = SalaryDetails::where('employee_id',$id)->first();

           if($e != null)
            {
                    $temp['id'] = $e->id; 
                    $temp['employee_id'] = $e->employee_id; 
                    $temp['salary'] = $e->salary; 
                    $temp['benefits'] = $e->benefits; 
                    $temp['date'] = $e->created_at->format("jS F, Y"); 
                    $ret = $temp; 
            }                          
                                                   
             return $ret;
          }

          function assignLeave($id)
          {
              $e = Employees::where('employee_id',$id)->first();

              if($e != null)
              {
                  $e->update(['leave_status' => "active"]);
              }
          }
           
           
}
?>