<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


class DashboardController extends Controller
{
    public function dashboard1(){
        $bodyCss = getAuthPageCss();
        return view('admin.dashboard1', compact('bodyCss'));
    }

    public function dashboard2(){
        $bodyCss = getAuthPageCss();
        return view('admin.dashboard2', compact('bodyCss'));
    }
}
