<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // LIST
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            return response()->json(Task::with(['user', 'project'])->get());
        }

        return response()->json(
            Task::where('assigned_to', $user->id)->with(['project'])->get()
        );
    }

    // CREATE
    // public function store(Request $request, $id = null)
    // {
    //     $projectId = $id ?? $request->project_id;

    //     $request->validate([
    //         'title'       => 'required',
    //         'priority'    => 'required|in:low,medium,high',
    //         'due_date'    => 'required|date',
    //         'assigned_to' => 'required|exists:users,id',  // ✅ assigned_to
    //     ]);

    //     $task = Task::create([
    //         'project_id'  => $projectId,
    //         'assigned_to' => $request->assigned_to,        // ✅ fix
    //         'title'       => $request->title,
    //         'description' => $request->description,
    //         'priority'    => $request->priority,
    //         'due_date'    => $request->due_date,
    //         'status'      => 'TODO',                       // ✅ PENDING → TODO
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'data'    => $task->load('user'),
    //     ], 201);
    // }

    // UPDATE
    // public function update(Request $request, $id)
    // {
    //     $user = Auth::user();
    //     $task = Task::findOrFail($id);

    //     // User sirf apna task update kare
    //     if ($user->role != 'admin' && $task->assigned_to != $user->id) {  // ✅ fix
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     // OVERDUE → WIP not allowed
    //     if ($task->status == 'OVERDUE' && $request->status == 'WIP') {    // ✅ IN_PROGRESS → WIP
    //         return response()->json(['message' => 'Overdue task ko WIP mein nahi le ja sakte'], 400);
    //     }

    //     // OVERDUE → DONE only admin
    //     if ($task->status == 'OVERDUE' && $request->status == 'DONE') {
    //         if ($user->role != 'admin') {
    //             return response()->json(['message' => 'Only admin can close overdue tasks'], 403);
    //         }
    //     }

    //     $task->update([
    //         'status'   => $request->status   ?? $task->status,
    //         'priority' => $request->priority ?? $task->priority,
    //         'due_date' => $request->due_date ?? $task->due_date,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'data'    => $task,
    //     ]);
    // }

    // MY TASKS
    public function myTasks()
    {
        return response()->json(
            Task::where('assigned_to', auth()->id())->with('project')->get()  // ✅ fix
        );
    }
    public function store(Request $request, $id = null)
{
    $projectId = $id ?? $request->project_id;

    $task = Task::create([
        'project_id'  => $projectId,
        'user_id'     => $request->assigned_to,  // ✅ fix
        'title'       => $request->title,
        'description' => $request->description,
        'priority'    => strtoupper($request->priority),  // ✅ LOW/MEDIUM/HIGH
        'due_date'    => $request->due_date,
        'status'      => 'PENDING',  // ✅ fix
    ]);

    return response()->json(['success' => true, 'data' => $task], 201);
}

public function update(Request $request, $id)
{
    $user = Auth::user();
    $task = Task::findOrFail($id);

    if ($user->role != 'admin' && $task->user_id != $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // ✅ IN_PROGRESS use karo WIP nahi
    if ($task->status == 'OVERDUE' && $request->status == 'IN_PROGRESS') {
        return response()->json(['message' => 'Overdue task ko IN_PROGRESS mein nahi le ja sakte'], 400);
    }

    if ($task->status == 'OVERDUE' && $request->status == 'DONE') {
        if ($user->role != 'admin') {
            return response()->json(['message' => 'Only admin can close overdue tasks'], 403);
        }
    }

    $task->update([
        'status'   => $request->status   ?? $task->status,
        'priority' => $request->priority ? strtoupper($request->priority) : $task->priority,
        'due_date' => $request->due_date  ?? $task->due_date,
    ]);

    return response()->json(['success' => true, 'data' => $task]);
}

public function tasksByProject($id)
{
    $user  = Auth::user();
    $query = Task::with(['user', 'project'])->where('project_id', $id);

    if ($user->role != 'admin') {
        $query->where('user_id', $user->id);  // ✅ fix
    }

    return response()->json(['success' => true, 'data' => $query->get()]);
}

    // TASKS BY PROJECT
    // public function tasksByProject($id)
    // {
    //     $user  = Auth::user();
    //     $query = Task::with(['user', 'project'])->where('project_id', $id);

    //     if ($user->role != 'admin') {
    //         $query->where('assigned_to', $user->id);  // ✅ fix
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data'    => $query->get(),
    //     ]);
    // }
}