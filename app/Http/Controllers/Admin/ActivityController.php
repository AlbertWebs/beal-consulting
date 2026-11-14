<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.activity.index', [
            'activities' => AdminActivity::query()->with('user')->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where('summary', 'like', '%'.$request->string('q').'%'))->latest()->paginate(40)->withQueryString(),
        ]);
    }
}
