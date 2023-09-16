<?php

namespace App\Http\Controllers\Admin\Chatapi\Department;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DepartMentController extends Controller
{
  public function AddDepartment(Request $request)
  {
    $getValue = Department::where('id', $request->id)->count();
    if ($getValue > 0) {
      $validator = Validator::make(
        $request->all(),
        [
          'dep_name' => 'required|min:3|max:100',
        ]
      );
    } else {
      $validator = Validator::make(
        $request->all(),
        [
          'dep_name' => 'required|unique:departments,dep_name|min:3|max:100',
        ]
      );
    }
    if ($validator->fails()) {
      $return['code'] = 100;
      $return['msg'] = 'error';
      $return['err'] = $validator->errors();
      return response()->json($return);
    }
    if(!empty($getValue)){
      $daat =  Department::where('id',$request->id)->update(['dep_name'=>$request->dep_name]);
      if($daat > 0){
         $return['code'] = 200;
         $return['msg']  = 'Category Saved Successfully!.';
         }else {
          $return['code'] = 101;
          $return['msg']  = 'Something went wrong!';
       }
       return response()->json($return);
    }else {
        $data = new Department;
        $data->web_id =  (int)$request->web_id;
        $data->dep_name = $request->dep_name;
    }
  if($data->save()){
     $return['code'] = 200;
     $return['msg']  = 'Category Saved Successfully!.';
     }else {
      $return['code'] = 101;
      $return['msg']  = 'Something went wrong!';
   }
    return response()->json($return);
  }
  public function getdepartmentList(Request $request)
  {
    $pg = $request->pg;
    $lim = $request->lim;
    $pgs = ($pg > 0) ? $lim * $pg : 0;
    $depList = Department::select('departments.*', 'web_site_lists.website')
    ->join('web_site_lists', 'web_site_lists.id', '=', 'departments.web_id')
    ->where('web_id', $request->web_id)
    ->orderBy('id', 'desc')->offset($pgs)->limit($lim)->get();
    $rows = Department::count();
    if (!empty($depList)) {
      $return['code'] = 200;
      $return['rows'] = $rows;
      $return['start'] = $pgs + 1;
      $return['list'] = $depList;
      $return['msg']  = 'Data found!.';
    } else {
      $return['code'] = 101;
      $return['msg']  = 'Data Not found!.';
    }
    return response()->json($return);
  }
  public function getFilterListDepartment(Request $request)
  {
    $pg = $request->pg;
    $lim = $request->lim;
    $pgs = ($pg > 0) ? $lim * $pg : 0;
    $catList = Department::where('dep_name', 'LIKE', "%$request->search%")->orWhere('created_by', 'LIKE', "%$request->search%")->where('status', 1)->orderBy('id', 'desc')->offset($pgs)->limit($lim)->get();
    $rows = Department::where('dep_name', 'LIKE', "%$request->search%")->orWhere('created_by', 'LIKE', "%$request->search%")->where('status', 1)->count();
    if (!empty($catList)) {
      $return['code'] = 200;
      $return['rows'] = $rows;
      $return['start'] = $pgs + 1;
      $return['list'] = $catList;
      $return['msg']  = 'Data found!.';
    } else {
      $return['code'] = 101;
      $return['msg']  = 'Data Not found!.';
    }
    return response()->json($return);
  }
  public function deleteSingleRecordDep(Request $request)
  {

    $deleteData = Department::where('id', $request->id)->delete();
    if ($deleteData > 0) {
      $return['code'] = 200;
      $return['msg']  = 'Category Delete Successfully!.';
    } else {
      $return['code'] = 101;
      $return['msg']  = 'Category Not Delete Successfully!.';
    }
    return response()->json($return);
  }
  public function multipleDeleteRecordsDdep(Request $request)
  {
    $return['code'] = 200;
    $return['msg']  = 'List has been Deleted Successfully!.';
    try {
      $ids = explode(",", $request->ids);
      Department::destroy($ids);
    } catch (\Exception $e) {
      $return['code'] = 101;
      $return['msg']  = 'Something went wrong!';
    }
    return response()->json($return);
  }
  public function statusUpdateDepartMent(Request $request)
  {
    $res =  DB::table('departments')->where('id', $request->id)->update(['status' => $request->status]);
    if ($res > 0) {
      $return['code'] = 200;
      $return['msg']  = 'Status updated successfully!';
    } else {
      $return['code'] = 101;
      $return['msg']  = 'Something went wrong!';
    }
    return response()->json($return);
  }
}
