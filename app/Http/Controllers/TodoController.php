<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'today');
        $user = auth()->user();

        $todayTodos = Todo::where('user_id', $user->id)
            ->where('is_completed', false)
            ->whereDate('due_date', today())
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->get();

        $upcomingTodos = Todo::where('user_id', $user->id)
            ->where('is_completed', false)
            ->where(function ($q) {
                $q->whereDate('due_date', '>', today())
                    ->orWhereNull('due_date');
            })
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get();

        $completedTodos = Todo::where('user_id', $user->id)
            ->where('is_completed', true)
            ->orderByDesc('completed_at')
            ->limit(50)
            ->get();

        return view('todos.index', compact('tab', 'todayTodos', 'upcomingTodos', 'completedTodos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'in:low,medium,high'],
            'category' => ['nullable', 'in:kerja,pribadi,belanja,keuangan'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['user_id'] = auth()->id();

        Todo::create($validated);

        return back()->with('success', 'Aktivitas berhasil ditambahkan!');
    }

    public function update(Request $request, Todo $todo): RedirectResponse
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'in:low,medium,high'],
            'category' => ['nullable', 'in:kerja,pribadi,belanja,keuangan'],
            'due_date' => ['nullable', 'date'],
        ]);

        $todo->update($validated);

        return back()->with('success', 'Aktivitas berhasil diperbarui!');
    }

    public function toggle(Todo $todo): RedirectResponse
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->update([
            'is_completed' => ! $todo->is_completed,
            'completed_at' => $todo->is_completed ? null : now(),
        ]);

        return back()->with('success', $todo->is_completed
            ? 'Aktivitas ditandai selesai!'
            : 'Aktivitas dibuka kembali!');
    }

    public function destroy(Todo $todo): RedirectResponse
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus!');
    }
}
