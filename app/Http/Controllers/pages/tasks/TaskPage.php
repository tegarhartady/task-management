<?php

namespace App\Http\Controllers\pages\tasks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskPage extends Controller
{
  public function index()
  {
    return view('content.pages.Tasks.index');
  }
}
