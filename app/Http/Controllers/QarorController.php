<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportQarorRequest;
use App\Http\Requests\StoreQarorRequest;
use App\Http\Requests\UpdateQarorRequest;
use App\Imports\QarorlarImport;
use App\Jobs\ImportQarorExcelJob;
use App\Models\Qaror;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class QarorController extends Controller
{
    public function store(StoreQarorRequest $request)
    {
        $data = $request->validated();

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
    public function update(UpdateQarorRequest $request, Qaror $qaror)
    {
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


    public function import(ImportQarorRequest $request)
    {
        // Store the uploaded file temporarily
        $filePath = $request->file('file')->store('temp-imports');

        // Dispatch async job for import
        ImportQarorExcelJob::dispatch(storage_path('app/' . $filePath));

        return back()->with('success', 'Import jarayoni boshlandi! Qarorlar tez orada qo\'shiladi.');
    }
}
