<?php

namespace App\Http\Controllers;

use App\Models\Task;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description ?? null,
            'status' => $request->status ?? null,
            'deadline' => explode('T', $request->deadline)[0] ?? null,
            'priority' => $request->priority ?? null,
            'user_id' => null
        ]);

        return redirect()->back()->with('success_message', 'Task created!');
    }

    public function update(Request $request)
    {
        $task = Task::find($request->id);

        $task->name = $request->name ?? $task->name;
        $task->description = $request->description ?? $task->description;
        $task->status = $request->status ?? $task->status;
        $task->deadline = $request->deadline ?? $task->deadline;
        $task->priority = $request->priority ?? $task->priority;

        $task->save();


        return redirect()->back()->with('success_message', 'Task updated!');
    }

    public function destroy(Request $request)
    {
        Task::destroy($request->id);
        return redirect()->back()->with('success_message', 'Task deleted!');

    }

    public function take(Request $request)
    {
        $task = Task::find($request->id);
        $task->user_id = $request->user()->id;
        $task->status = 'in_progress';
        $task->save();
        return redirect()->back()->with('success_message', 'Task taken!');
    }

    public function finish(Request $request)
    {
        $task = Task::find($request->id);
        $task->status = 'completed';
        $task->save();
        return redirect()->back()->with('success_message', 'Task complete!');
    }

    public function all()
    {
        $tasks = Task::with('user')->get();

        $tasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->status,
                'deadline' => $task->deadline,
                'priority' => $task->priority,
                'user_id' => $task->user_id,
                'ownername' => $task->user ? $task->user->name : '',
            ];
        });

        return $tasks;
    }

    public function getStatusList()
    {
        return [
                'backlog' => 'Waiting confirmation',
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'paused' => 'Paused',
                'completed' => 'Completed'
        ];
    }

    public function getPriorityList()
    {
        return [
                '1' => 'Normal',
                '2' => 'Moderate',
                '3' => 'Urgent'
        ];
    }

}
