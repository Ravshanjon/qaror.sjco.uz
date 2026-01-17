<?php

namespace App\Http\Controllers;

use App\Imports\QarorlarImport;
use App\Models\Qaror;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QarorController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'published_id' => 'required|integer|unique:qarors,published_id',
            'title' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        $qaror = Qaror::create([
            'published_id' => $data['published_id'],
            'title' => $data['title'],
        ]);

        if ($request->hasFile('pdf')) {
            $path = $request->file('pdf')->storeAs(
                'qarorlar',
                'qaror_' . $qaror->published_id . '.pdf',
                'public'
            );

            $qaror->update(['pdf_path' => $path]);
        }

        return back()->with('success', 'Qaror saqlandi');
    }
    public function update(Request $request, Qaror $qaror)
    {
        $request->validate([
            'pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        if ($request->hasFile('pdf')) {

            // 🔥 eski PDF ni o‘chiramiz
            if ($qaror->pdf_path && Storage::disk('public')->exists($qaror->pdf_path)) {
                Storage::disk('public')->delete($qaror->pdf_path);
            }

            $path = $request->file('pdf')->storeAs(
                'qarorlar',
                'qaror_' . $qaror->published_id . '.pdf',
                'public'
            );

            $qaror->pdf_path = $path;
        }

        $qaror->save();

        return back()->with('success', 'Yangilandi');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new QarorlarImport, $request->file('file'));

        return back()->with('success', 'Qarorlar muvaffaqiyatli import qilindi!');
    }
}
