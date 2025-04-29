<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $query->where('status', $request->status);
        }
        $tasks = $query->latest()->paginate(5);
        if ($request->ajax()) {
            return view('tasks.partials.table', compact('tasks'))->render();
        }
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string'
        ]);

        Task::create($validated);
        return response()->json(['message' => 'Task added successfully.']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string'
        ]);

        $task = Task::findOrFail($id);
        $task->update($validated);
        return response()->json(['message' => 'Task updated successfully.']);
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function toggleStatus($id)
    {
        $task = Task::findOrFail($id);
        $task->status = $task->status === 'completed' ? 'pending' : 'completed';
        $task->save();
        return response()->json(['message' => 'Task status updated.']);
    }

    public function edit($id)
    {
        return Task::findOrFail($id);
    }
}
