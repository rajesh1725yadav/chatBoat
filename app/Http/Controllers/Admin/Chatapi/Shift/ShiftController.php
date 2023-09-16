<?php

namespace App\Http\Controllers\Admin\Chatapi\Shift;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
//use Session;

class ShiftController extends Controller
{
    public function userAgentList(){
      $userList = User::select('id as value','name as label')->where('agent','!=',null)->orderBy('name','asc')->get();
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
    public function shiftUserList(Request $request){
      $pg = $request->pg;
      $lim = $request->lim;
      $pgs = ($pg > 0) ? $lim*$pg : 0;
      $userList = DB::table('user_shift')
             ->Join('users', 'user_shift.user_id', '=', 'users.id')
             ->where('users.status', 0)
             ->offset($pgs)->limit($lim)
             ->orderBy('user_shift.id','desc')
             ->select('user_shift.*','users.name','users.user_id','users.role','users.agent','users.date_of_joining','users.email','users.role_manager')
             ->get();
      $rows = DB::table('user_shift')
                    ->Join('users', 'user_shift.user_id', '=', 'users.id')
                    ->where('users.status', 0)
                    ->count();
      //$rows =   $userLists->count();
      if(!empty($userList)){
        $return['code'] = 200;
        $return['rows'] = $rows;
        $return['start'] = $pgs+1;
        $return['data'] =  $userList;
        $return['msg']  = 'Data found!.';
      }else{
        $return['code'] = 101;
        $return['msg']  = 'Data Not found!.';
      }
      return response()->json($return);
    }
    public function statusUpdateUserShift(Request $request){
     $res =  DB::table('user_shift')->where('id',$request->id)->update(['status'=>$request->status]);
     if($res > 0){
       $return['code'] = 200;
       $return['msg']  = 'Status updated successfully!';
     }else {
       $return['code'] = 101;
       $return['msg']  = 'Something went wrong!';
     }
    return response()->json($return);
    }

    public function addShiftUser(Request $request){
        $validator = Validator::make(
         $request->all(),
         [
            'user_name'        => 'required',
            'weekly_day_off'   => 'required',
            'shift_start_time' => 'required',
            'shift_end_time'   => 'required',
            'break_duration'   => 'required',
         ]
       );
     if ($validator->fails())
       {
         $return['code'] = 100;
         $return['msg'] = 'error';
         $return['err'] = $validator->errors();
         return response()->json($return);
       }
      $exist = UserShift::where('id',$request->id)->first();
      if(!empty($exist) != ''){
        $affected = DB::table('user_shift')
              ->where('id', $request->id)
              ->update(['user_id' => $request->user_name,'weekly_day_off' => $request->weekly_day_off,'shift_start_time' => $request->shift_start_time,'shift_end_time' => $request->shift_end_time,'break_duration' => $request->break_duration]);
        if($affected > 0){
          $return['code'] = 200;
          $return['msg']  = 'Updated Record Successfully!.';
         }else {
           $return['code'] = 101;
           $return['msg']  = 'Updated Record Not Successfully!.';
        }
      }else {
        $result = new UserShift();
        $result->user_id = $request->user_name;
        $result->weekly_day_off = intval($request->weekly_day_off);
        $result->shift_start_time = intval($request->shift_start_time);
        $result->shift_end_time = intval($request->shift_end_time);
        $result->break_duration = intval($request->break_duration);
        if($result->save()){
          $return['code'] = 200;
          $return['msg']  = 'Insert Record Successfully!.';
        }else {
          $return['code'] = 101;
          $return['msg']  = 'Insert Record Not Successfully!.';
        }
      }
         return response()->json($return);
    }
    public function getFilterShiftList(Request $request){
      $pg = $request->pg;
      $lim = $request->lim;
      $pgs = ($pg > 0) ? $lim*$pg : 0;
      $userLists = DB::table('user_shift')
             ->Join('users', 'user_shift.user_id', '=', 'users.id');
      $userLists->where('users.name', 'LIKE', "%$request->name%")->orWhere('users.user_id', 'LIKE', "%$request->role_manager%")
             ->where('users.status', 0);
      $userList = $userLists->offset($pgs)->limit($lim)
             ->select('user_shift.*','users.name','users.user_id','users.role','users.agent','users.date_of_joining','users.email','users.role_manager')
             ->get();
      $rows =$userList->count();
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
    public function deleteSingleShiftRecord(Request $request){
      $deleteData = UserShift::where('id',$request->id)->delete();
     if($deleteData > 0){
       $return['code'] = 200;
       $return['msg']  = 'Record Delete Successfully!.';
     }else {
       $return['code'] = 101;
       $return['msg']  = 'Record Not Delete Successfully!.';
     }
      return response()->json($return);
    }
}
