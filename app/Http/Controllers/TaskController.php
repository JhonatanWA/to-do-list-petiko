<?php 

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse; 
use App\Services\exportCSV;
use Carbon\Carbon;

use App\Http\Controllers\Controller;

class TaskController extends Controller
{

    /**
     * Show a listing of the tasks.
     *
     * Params added:
     * - search: string
     * - perPage: int
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
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

    /**
     * Export tasks to CSV.
     * 
     * Params added:
     * - search: string
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request)
    {
        $search = $request->query('search');
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

        $tasks = $tasksQuery->orderBy('created_at', 'desc')->get(); 

        $headersData = [
            'ID', 'Título', 'Descrição', 'Data de Vencimento', 'Concluída'
        ];

         $mapping = [
            'ID' => 'id',
            'Título' => 'title',
            'Descrição' => 'description',
            'Data de Vencimento' => 'due_date',
            'Concluída' => 'is_completed',
        ];

        $dataCSV = [];

        if ($isAdmin) {
            $headersData[] = 'Usuário';
            $mapping['Usuário'] = 'user.name';
        }

        foreach ($tasks as $task) {
            $row = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date ? Carbon::parse($task->due_date)->format('d/m/Y') : '',
                'is_completed' => $task->is_completed ? 'Sim' : 'Não',
            ];

            if ($isAdmin) {
                $row['user.name'] = $task->user ? $task->user->name : 'N/A';
            }

            $dataCSV[] = (object) $row; // Garante acesso via $row->title etc.
        }

        $filename = 'tarefas_' . date('Ymd_His') . '.csv';

        $exportCSV = new exportCSV();
        return $exportCSV->export($dataCSV, $headersData, $filename, $mapping);
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'users' => User::all(),
        ]);
    }

    /**
     * Store a new created task in storage.
     *
     * Params added:
     * - title: string
     * - description: string
     * - due_date: date
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Show the form for editing the specified task.
     *
     * Params added:
     * - task: Task
     * 
     * @param  \App\Models\Task  $task
     * @return \Inertia\Response
     */
    public function edit(Task $task)
    {
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'users' => User::all(),
        ]);
    }

    /**
     * Update the specified task in storage.
     *
     * Params added:
     * - title: string
     * - description: string
     * - is_completed: boolean
     * - due_date: date
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Task $task)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
            'due_date' => 'required|nullable|after_or_equal:today',
            'user_id' => 'required|nullable'
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task atualizada com sucesso.');
    }

    /**
     * Remove the specified task from storage.
     * 
     * Params added:
     * - task: Task
     * 
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task excluída com sucesso.');
    }
}
