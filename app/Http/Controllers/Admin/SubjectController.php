<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
        ]);

        try {
            Subject::create([
                'name' => $request->name,
            ]);

            return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Error creating subject: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan mata pelajaran. Silakan coba lagi.');
        }
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
        ]);

        try {
            $subject->update([
                'name' => $request->name,
            ]);

            return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Error updating subject: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui mata pelajaran. Silakan coba lagi.');
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
            return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting subject: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus mata pelajaran. Silakan coba lagi.');
        }
    }
}