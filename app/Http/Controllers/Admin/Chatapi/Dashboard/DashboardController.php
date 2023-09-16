<?php

namespace App\Http\Controllers\Admin\Chatapi\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebSiteList;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::where('status', 0)->count();
        $manager = User::where('role', 2)->where('status', 0)->count();
        $agent = User::where('role', 3)->where('status', 0)->count();
        $website = WebSiteList::where('status', 1)->count();
        $return['ucount'] = $user;
        $return['mcount'] = $manager;
        $return['acount'] = $agent;
        $return['wcount'] = $website;
        return response()->json($return);
    }
}
