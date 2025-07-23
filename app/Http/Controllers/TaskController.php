<?php 

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $isAdmin = Auth::user() && Auth::user()->isAdmin();

        $tasksQuery = Task::query();

        if (!$isAdmin) {
            $tasksQuery->where('user_id', Auth::id());
        } else {
            $tasksQuery->with('user');
        }

        if ($search) {
            $tasksQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $tasksQuery->orderBy('created_at', 'desc');
        $tasks = $tasksQuery->paginate($perPage)->withQueryString();


        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'search' => $search,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'users' => User::all(),
        ]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|nullable|after_or_equal:today',
        ]);

        Auth::user()->tasks()->create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task criada com sucesso.');
    }

    public function edit(Task $task)
    {
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'users' => User::all(),
        ]);
    }

    public function update(Request $request, Task $task)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
            'due_date' => 'required|nullable|after_or_equal:today',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task atualizada com sucesso.');
    }

    public function destroy(Task $task)
    {

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task excluída com sucesso.');
    }
}
