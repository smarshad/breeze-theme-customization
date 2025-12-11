<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function create()
    {
        $menus = Menu::with(['parent', 'children'])->orderBy('order')->get();
        return view('admin.menu.create', compact('menus'));
    }
}
