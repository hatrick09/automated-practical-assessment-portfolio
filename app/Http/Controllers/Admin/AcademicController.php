<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('semesters')->orderByDesc('name')->get();
        return view('admin.academic.index', compact('academicYears'));
    }

    public function storeYear(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:50']]);
        AcademicYear::create($data);
        return back()->with('status', 'Academic year added.');
    }

    public function setCurrentYear(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);
        });
        return back()->with('status', "{$academicYear->name} set as the current academic year.");
    }

    public function destroyYear(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return back()->with('status', 'Academic year deleted.');
    }

    public function storeSemester(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:50']]);
        $academicYear->semesters()->create($data);
        return back()->with('status', 'Semester added.');
    }

    public function setCurrentSemester(Semester $semester)
    {
        DB::transaction(function () use ($semester) {
            Semester::query()->update(['is_current' => false]);
            $semester->update(['is_current' => true]);
        });
        return back()->with('status', "{$semester->name} set as the current semester.");
    }

    public function destroySemester(Semester $semester)
    {
        $semester->delete();
        return back()->with('status', 'Semester deleted.');
    }
}
