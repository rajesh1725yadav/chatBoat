<?php

namespace App\Http\Controllers\Admin\Chatapi\Website;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\WebSiteList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Str;

class WebSiteController extends Controller
{ 
   public function AddWebSite(Request $request){
     $getValue = WebSiteList::where('id',$request->id)->count();
     if($getValue > 0){
        $validator = Validator::make(
         $request->all(),
         [
           'website' => 'required|min:3|max:30'.$request->id,
           'website_url' => 'required|url',
         ]
       );
     }else {
       $validator = Validator::make(
        $request->all(),
        [
          'website' => 'required|unique:web_site_lists,website|min:3|max:30'.$request->id,
          'website_url' => 'required|unique:web_site_lists,website_url|url',
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
      $return['code'] = 200;
      $return['msg']  = 'Website Saved Successfully!.';
      $rerult = self::generateUserName();

      try {
        $save = WebSiteList::updateOrCreate([
            'id'   => $request->id,
         ],[
            'website'         => $request->website,
            'website_url'     => $request->website_url,
            'web_site_code'   => $rerult,
            'bot_name'        => $request->bot_name,
            'massage'         => $request->massage,
            'category_id'     => $request->category_id,
         ]);
      } catch (\Exception $e) {
        $return['code'] = 101;
        $return['msg']  = 'Something went wrong!';
      }
      return response()->json($return);
   }
   public function generateUserName(){
        $adcode = 'CSU' . strtoupper(uniqid(15));
        $checkdata = WebSiteList::where('web_site_code', $adcode)->count();
        if ($checkdata > 0) {
            $this->generateUserName();
        } else {
            return $adcode;
        }
    }

   public function websiteList(Request $request){
     $pg = $request->pg;
     $lim = $request->lim;
     $pgs = ($pg > 0) ? $lim*$pg : 0;
     $websiteList = WebSiteList::orderBy('id','desc')->offset($pgs)->limit($lim)->get();
     $rows = WebSiteList::where('status',1)->count();
     if(!empty($websiteList)){
       $return['code'] = 200;
       $return['rows'] = $rows;
       $return['start'] = $pgs+1;
       $return['list'] = $websiteList;
       $return['msg']  = 'Data found!.';
     }else{
       $return['code'] = 101;
       $return['msg']  = 'Data Not found!.';
     }
     return response()->json($return);
   }
   public function getFilterWebsiteList(Request $request){
     $pg = $request->pg;
     $lim = $request->lim;
     $pgs = ($pg > 0) ? $lim*$pg : 0;
     $catList = WebSiteList::join('categories', 'web_site_lists.category_id', '=', 'categories.id')
               ->select('web_site_lists.*','categories.name')->orderBy('id','desc')
               ->where('web_site_lists.website', 'LIKE', "%$request->search%")->orWhere('web_site_lists.website_url', 'LIKE', "%$request->search%")->orWhere('web_site_lists.bot_name', 'LIKE', "%$request->search%")
               ->orWhere('web_site_lists.massage', 'LIKE', "%$request->search%")->orWhere('web_site_lists.created_at', 'LIKE', "%$request->search%")->where('web_site_lists.status',1)->orderBy('id','desc')->offset($pgs)->limit($lim)->get();

       $rows = WebSiteList::join('categories', 'web_site_lists.category_id', '=', 'categories.id')
                         ->select('web_site_lists.*','categories.name')->orderBy('id','desc')
                         ->where('web_site_lists.website', 'LIKE', "%$request->search%")->orWhere('web_site_lists.website_url', 'LIKE', "%$request->search%")->orWhere('web_site_lists.bot_name', 'LIKE', "%$request->search%")
                         ->orWhere('web_site_lists.massage', 'LIKE', "%$request->search%")->orWhere('web_site_lists.created_at', 'LIKE', "%$request->search%")->where('web_site_lists.status',1)->count();


     // $rows = WebSiteList::where('website', 'LIKE', "%$request->search%")->orWhere('website_url', 'LIKE', "%$request->search%")->orWhere('bot_name', 'LIKE', "%$request->search%")
     // ->orWhere('massage', 'LIKE', "%$request->search%")->orWhere('created_at', 'LIKE', "%$request->search%")->where('status',1)->count();
     if(!empty($catList)){
       $return['code'] = 200;
       $return['rows'] = $rows;
       $return['start'] = $pgs+1;
       $return['list'] = $catList;
       $return['msg']  = 'Data found!.';
     }else{
       $return['code'] = 101;
       $return['msg']  = 'Data Not found!.';
     }
     return response()->json($return);
   }
   public function deleteWebSite(Request $request){
     $deleteData = WebSiteList::where('id',$request->id)->where('status',1)->delete();
    if($deleteData > 0){
      $return['code'] = 200;
      $return['msg']  = 'Record Delete Successfully!.';
    }else {
      $return['code'] = 101;
      $return['msg']  = 'Record Not Delete Successfully!.';
    }
     return response()->json($return);
   }

   public function multipleDeleteRecordsWebSite(Request $request){
     $return['code'] = 200;
     $return['msg']  = 'List has been Deleted Successfully!.';
      try {
        $ids = explode(",",$request->ids);
        WebSiteList::destroy($ids);
      } catch (\Exception $e) {
        $return['code'] = 101;
        $return['msg']  = 'Something went wrong!';
      }
      return response()->json($return);
   }
   // public function getWebsiteList(){
   //   $catList = Category::select('id as value','name as label')->where('status',1)->orderBy('id','desc')->get();
   //   if(!empty( $catList)){
   //     $return['code'] = 200;
   //     $return['data'] =  $catList;
   //     $return['msg']  = 'Data found!';
   //   }else {
   //     $return['code'] = 101;
   //     $return['msg']  = 'Something went wrong!';
   //   }
   //  return response()->json($return);
   // }
   public function statusUpdateWebsite(Request $request){
    $res =  DB::table('web_site_lists')->where('id',$request->id)->update(['status'=>$request->status]);
    if($res > 0){
      $return['code'] = 200;
      $return['msg']  = 'Status updated successfully!';
    }else {
      $return['code'] = 101;
      $return['msg']  = 'Something went wrong!';
    }
   return response()->json($return);
   }
}
