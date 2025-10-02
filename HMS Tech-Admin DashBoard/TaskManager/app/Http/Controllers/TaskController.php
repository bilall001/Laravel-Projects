<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
              $tasks = Task::paginate(4);
        return view('tasks.index',compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $categories = Category::all();
       $users = User::all();
       return view('tasks.create',compact('categories','users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
             'title' => 'required|string|max:255',
    
    'description' => 'nullable|string',
    
    'category_id' => 'required|exists:categories,id',
    
    'user_id' => 'required|exists:users,id',
    
    'status1' => 'required|in:pending,in_progress,completed',
    
    'priority' => 'required|in:low,medium,high',
    
    'due_date' => 'nullable|date',
    
    'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status1,
            'priority' => $request->priority,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id,
            'due_date' => $request->due_date,
            'attachment' => $request->attachment
        ]);

      return redirect()->back()->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tasks = Task::all();
        return view('tasks.index',compact('tasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories= Category::all();
        $users = User::all();
        $task = Task::findOrFail($id);
        // dd($task);
        return view('tasks.edit',compact('task','categories','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'user_id' => 'required|exists:users,id',
        'status' => 'required|in:pending,in_progress,completed',
        'priority' => 'required|in:low,medium,high',
        'due_date' => 'nullable|date',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
    ]);

    $task = Task::findOrFail($id);

    $task->title = $request->title;
    $task->description = $request->description;
    $task->category_id = $request->category_id;
    $task->user_id = $request->user_id;
    $task->status = $request->status;
    $task->priority = $request->priority;
    $task->due_date = $request->due_date;

    // Handle file upload
    if ($request->hasFile('attachment')) {
        // Delete old file if exists
        if ($task->attachment && file_exists(public_path('attachments/' . $task->attachment))) {
            unlink(public_path('attachments/' . $task->attachment));
        }

        $file = $request->file('attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('attachments'), $filename);
        $task->attachment = $filename;
    }

    $task->save();

    return redirect()->back()->with('success', 'Task updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
            return redirect()->back()->with('success', 'Task deleted successfully!');
}
}