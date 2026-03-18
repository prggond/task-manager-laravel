<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index(){
        return response()->json(Project::all());
    }

    public function store(Request $request){
        $project = Project::create($request->all());
        return response()->json($project);
    }
}
