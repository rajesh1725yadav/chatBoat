<?php

namespace App\Http\Controllers\Admin\Chatapi\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
  public function AddCategory(Request $request)
  {
    $category = Category::where('id', $request->id)->count();
    if ($category > 0) {
      $validator = Validator::make(
        $request->all(),
        ['name' => 'required|min:3|max:30',]
      );
    } else {
      $validator = Validator::make(
        $request->all(),
        ['name' => 'required|unique:categories,name|min:3|max:30',]
      );
    }
    if ($validator->fails()) {
      $return['code'] = 100;
      $return['msg'] = 'error';
      $return['err'] = $validator->errors();
      return response()->json($return);
    }
    if (!empty($category)) {
      $update = Category::where('id', $request->id)->update(['name' => $request->name]);
      if ($update > 0) {
        $return['code'] = 200;
        $return['msg']  = 'Category Saved Successfully!.';
      } else {
        $return['code'] = 101;
        $return['msg']  = 'Something went wrong!';
      }
      return response()->json($return);
    } else {
      $result = new Category();
      $result->web_id = $request->web_id;
      $result->name = $request->name;
    }
    if ($result->save()) {
      $return['code'] = 200;
      $return['msg']  = 'Category Saved Successfully!.';
    } else {
      $return['code'] = 101;
      $return['msg']  = 'Something went wrong!';
    }
    return response()->json($return);
  }
  public function getCategoryList(Request $request)
  {
    $pg = $request->pg;
    $lim = $request->lim;
    $pgs = ($pg > 0) ? $lim * $pg : 0;
    $catList = Category::select('categories.*', 'web_site_lists.website')
    ->join('web_site_lists', 'categories.web_id', '=', 'web_site_lists.id' )
    ->where('categories.web_id', $request->web_id)
    ->where('categories.status', 1)->orderBy('id', 'desc')->offset($pgs)->limit($lim)->get();
    $rows = $catList->count();
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
  public function getFilterList(Request $request)
  {
    $pg = $request->pg;
    $lim = $request->lim;
    $pgs = ($pg > 0) ? $lim * $pg : 0;
    // $catList = Category::where('web_id',$request->web_id)->where('name', 'LIKE', "%$request->search%")->orWhere('created_by', 'LIKE', "%$request->search%")->where('status', 1)->orderBy('id', 'desc')->offset($pgs)->limit($lim)->get();
     $catLists = Category::where('web_id',$request->web_id)->where('status',1)->orderBy('id', 'desc');
     if(!empty($request->search)){
       $catList = $catLists->where('name', 'LIKE', "%$request->search%")->orWhere('created_by', 'LIKE', "%$request->search%")->offset($pgs)->limit($lim);
     }else {
       $return['code'] = 101;
       $return['msg']  = 'Data Not found!.';
       return response()->json($return);
     }
     $rows = $catList->count();
    if (!empty($request->web_id)) {
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
  public function deleteCategory(Request $request)
  {
    $deleteData = Category::where('id', $request->id)->where('status', 1)->delete();
    if ($deleteData > 0) {
      $return['code'] = 200;
      $return['msg']  = 'Category Delete Successfully!.';
    } else {
      $return['code'] = 101;
      $return['msg']  = 'Category Not Delete Successfully!.';
    }
    return response()->json($return);
  }
  public function multipleDeleteRecords(Request $request)
  {
    $return['code'] = 200;
    $return['msg']  = 'List has been Deleted Successfully!.';
    try {
      $ids = explode(",", $request->ids);
      Category::destroy($ids);
    } catch (\Exception $e) {
      $return['code'] = 101;
      $return['msg']  = 'Something went wrong!';
    }
    return response()->json($return);
  }
  public function getBlogReplaceData(){
    $users = DB::table('temporaryblogs')->select('description','id')->get();
    foreach ($users as $key => $value) {
      $this->descReplace($value->description,$value->id);
    }
  }

  public  static function descReplace($desc,$id)
  {
    $pattern = '/7searchppc.com/i';
    $description= preg_replace($pattern, '7searchppc.info', $desc);
    DB::table('temporaryblogs')->where('id',$id)->update(['description'=>$description]);
  }
}
