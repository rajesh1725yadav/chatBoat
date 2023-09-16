<?php

namespace App\Http\Controllers\Admin\Chatapi;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebSiteList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Session;
//use Session;

class UserController extends Controller
{
  public function generateUserid(){
       $adcode = 'CSU' . strtoupper(uniqid(10));
       $checkdata = User::where('user_id', $adcode)->count();
       if ($checkdata > 0) {
           $this->generateUserid();
       } else {
           return $adcode;
       }
   }
    public function userRegistation(Request $request)
    {
     $updateUser = User::where('id',$request->id)->first();
     if(!empty($updateUser != '')){
       $validator = Validator::make(
        $request->all(),
        [
           'name' => 'required',
           'role' => 'required',
           'date_of_joining' => 'required',
        ]
      );
     }else {
       $validator = Validator::make(
        $request->all(),
        [
           'name' => 'required',
           'email' => 'required|email|unique:users',
           'password' => 'required|min:8',
           'role' => 'required',
           'date_of_joining' => 'required',
        ]
      );
     } 
    if ($validator->fails())
      {
        $return['code'] = 100;
        $return['msg'] = 'error';
        $return['err'] = $validator->errors();
        return response()->json($return);
      }
      if(!empty($updateUser != '')){
      $data = User::whereId($request->id)->update(['name'=>$request->name,'role'=>$request->role,'role_manager'=>$request->select_manager,'date_of_joining'=>$request->date_of_joining,'agent'=>$request->agent,'shift_start_time'=>$request->shift_start_time,'website_id'=>$request->website_id,'shift_end_time'=>$request->shift_end_time,'weekly_day_off'=>$request->weekly_day_off,'break_duration'=>$request->break_duration,]);
        $return['code'] = 200;
        $return['msg'] = 'Data found';
        return response()->json($return);
      }else {
        $confirmpassword = $request->input('password');
        $data = new User();
        $data->name = $request->name;
        $data->email  = $request->email;
        $data->user_id = self::generateUserid();
        $data ->role   = $request->role;
        $data ->role_manager   = $request->select_manager;
        $data ->date_of_joining   = $request->date_of_joining;
        $data ->shift_start_time   = $request->shift_start_time;
        $data ->shift_end_time   = $request->shift_end_time;
        $data ->weekly_day_off   = $request->weekly_day_off;
        $data ->break_duration   = $request->break_duration;
        $data ->agent   = $request->agent;
        $data ->website_id   = $request->website_id;
        $data->password =  Hash::make($confirmpassword);
      }
        if($data->save()){
          $return['code'] = 200;
          $return['msg'] = 'Data found';
        }else{
           $return['code'] = 100;
          $return['msg']   = 'Data Not found';
        }
        return response()->json($return);
  }
  public function userLogin(Request $request){
    if(Auth::attempt(['email' => $request->email, 'password' => $request->Password])){
           $user = Auth::user();
           $loginUser = $user->id;
           session(['variableName' => $user]);
           $userloginss = Session::get('variableName');
           $return['code'] = 200;
           $return['msg'] = 'User login successfully.';
           $return['token'] =  $user->createToken('MyApp')->plainTextToken;
           $return['name'] =  $user->name;
           $return['login'] = $userloginss;
           return response()->json($return);
       }
       else{
         $return['code'] = 100;
         $return['msg']   = 'Data Not found';
         return response()->json($return);
       }
  }
  public function registerUserList(Request $request){
    $pg = $request->pg;
    $lim = $request->lim;
    $pgs = ($pg > 0) ? $lim*$pg : 0;
    $userList = DB::table('users as t1')->select('t1.id','t1.user_id','t1.name','t1.email','t1.role','t1.agent','t1.date_of_joining','t1.status','t1.role_manager','t1.shift_start_time','t1.shift_end_time','t1.weekly_day_off','t1.break_duration', DB::raw("(select name from users t2 where t2.user_id = t1.role_manager ) as u_manager"),DB::raw("(select user_id from users t3 where t3.user_id = t1.role_manager) as u_id"),'t1.website_id')
     ->where('t1.trash',0);
    $rows = $userList->count();
    $userList = $userList->orderBy('t1.id','desc')->offset($pgs)->limit($lim)->get();
    if(!empty($userList)){
      $return['code'] = 200;
      $return['rows'] = $rows;
      $return['start'] = $pgs+1;
      $return['list'] = $userList;
      $return['msg']  = 'Data found!.';
    }else{
      $return['code'] = 101;
      $return['msg']  = 'Data Not found!.';
    }
    return response()->json($return);
  }
  public function index(){
       $file = "C:\Users\USER\Downloads\jk.txt";
       $doc = file_get_contents("$file");
        $data = preg_replace('/\s+/', '-', $doc);
        $data1 = explode('-', $data);
       foreach ($data1 as $key => $value) {
          DB::table('ss_countries_ips')->insert([
               'ip_addr' => $value,
               'country_code' => 'UG',
               'country_name' => 'UGANDA',
               'state' => ''
           ]);
         }
      }
    public function getManagerList(){
      $userList = User::select('user_id as value','name as label')->where('role',2)->orderBy('name','asc')->get();
      if(!empty($userList)){
        $return['code'] = 200;
        $return['data'] =  $userList;
        $return['msg']  = 'Data found!.';
      }else{
        $return['code'] = 101;
        $return['msg']  = 'Data Not found!.';
      }
      return response()->json($return);
    }
    public function deleteSingleRecordUser(Request $request){
      $deleteData = User::where('id',$request->id)->delete();
     if($deleteData > 0){
       $return['code'] = 200;
       $return['msg']  = 'Record Delete Successfully!.';
     }else {
       $return['code'] = 101;
       $return['msg']  = 'Record Not Delete Successfully!.';
     }
      return response()->json($return);
    }
    public function statusUpdateUser(Request $request){
     $res =  DB::table('users')->where('id',$request->id)->update(['status'=>$request->status]);
     if($res > 0){
       $return['code'] = 200;
       $return['msg']  = 'Status updated successfully!';
     }else {
       $return['code'] = 101;
       $return['msg']  = 'Something went wrong!';
     }
    return response()->json($return);
    }
    public function userDeatils(Request $request){
      $userList = DB::table('users as t1')->select('t1.id','t1.user_id','t1.name','t1.email','t1.role','t1.agent','t1.date_of_joining','t1.status','t1.role_manager','t1.shift_start_time','t1.shift_end_time','t1.weekly_day_off','t1.break_duration', DB::raw("(select name from users t2 where t2.user_id = t1.role_manager ) as u_manager"),DB::raw("(select user_id from users t3 where t3.user_id = t1.role_manager) as u_id"),'t1.*')->where('t1.trash',0)->where('t1.user_id',$request->user_id)->get();
      $rows = $userList->count();
      $userList = $userList->first();
      if(!empty($userList)){
        $return['code'] = 200;
        $return['list'] = $userList;
        $return['msg']  = 'Data found!.';
      }else{
        $return['code'] = 101;
        $return['msg']  = 'Data Not found!.';
      }
      return response()->json($return);
    }
    public function webSiteList(){
      $webList = WebSiteList::select('id as value','website as label')->where('status',1)->orderBy('id','desc')->get();
      if(!empty($webList)){
        $return['code'] = 200;
        $return['list'] = $webList;
        $return['msg']  = 'Data found!.';
       }else{
        $return['code'] = 101;
        $return['msg']  = 'Data Not found!.';
      }
      return response()->json($return);
    }
   public function getWebsiteDetails(Request $req){
       $webList = User::where('user_id',$req->user_id)->first();
       $result = json_decode($webList->website_id);
       $uwebList=array();
       foreach ($result as $res) {
          $webList = WebSiteList::where('id',$res->value)->orderBy('id','desc')->first();
          array_push($uwebList,$webList);
       }
       if(!empty($uwebList)){
         $return['code'] = 200;
         $return['list'] = $uwebList;
         $return['msg']  = 'Data found!.';
        }else{
         $return['code'] = 101;
         $return['msg']  = 'Data Not found!.';
       }
       return response()->json($return);
   }
   public function getAgentDetails(Request $request){
       $userList = DB::table('users as t1')->select('t1.id','t1.user_id','t1.name','t1.email','t1.role','t1.agent','t1.date_of_joining','t1.status','t1.role_manager','t1.shift_start_time','t1.shift_end_time','t1.weekly_day_off','t1.break_duration', DB::raw("(select name from users t2 where t2.user_id = t1.role_manager ) as u_manager"),DB::raw("(select user_id from users t3 where t3.user_id = t1.role_manager) as u_id"),'t1.*')->where('t1.trash',0)->where('t1.role_manager',$request->user_id)->get();
       if(!empty($userList)){
         $return['code'] = 200;
         $return['list'] = $userList;
         $return['msg']  = 'Data found!.';
        }else{
         $return['code'] = 101;
         $return['msg']  = 'Data Not found!.';
       }
       return response()->json($return);
   }
}
