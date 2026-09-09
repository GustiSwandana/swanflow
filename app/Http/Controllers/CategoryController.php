<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of transaction categories.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->withCount('transactions')->get();

        $expenseCategories = $categories->where('type', TransactionType::Expense);
        $incomeCategories = $categories->where('type', TransactionType::Income);

        return view('categories.index', [
            'user' => $user,
            'expenseCategories' => $expenseCategories,
            'incomeCategories' => $incomeCategories,
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Tipe kategori wajib dipilih.',
            'type.in' => 'Tipe kategori tidak valid.',
        ]);

        $defaultColor = ($validated['type'] ?? 'expense') === 'income' ? '#10B981' : '#F43F5E';
        $validated['color'] = ! empty($validated['color']) ? $validated['color'] : $defaultColor;
        $validated['icon'] = ! empty($validated['icon']) ? $validated['icon'] : 'tag';
        $validated['user_id'] = $user->id;

        Category::create($validated);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($category->user_id !== $user->id, 403, 'Kategori bawaan sistem tidak dapat diubah.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Tipe kategori wajib dipilih.',
            'type.in' => 'Tipe kategori tidak valid.',
        ]);

        $defaultColor = ($validated['type'] ?? $category->type) === 'income' ? '#10B981' : '#F43F5E';
        $validated['color'] = ! empty($validated['color']) ? $validated['color'] : ($category->color ?: $defaultColor);
        $validated['icon'] = ! empty($validated['icon']) ? $validated['icon'] : ($category->icon ?: 'tag');

        $category->update($validated);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, Category $category): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user() ?? User::first() ?? User::getPrimaryUser();

        abort_if($category->user_id !== $user->id, 403, 'Kategori bawaan sistem tidak dapat dihapus.');

        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }
}
