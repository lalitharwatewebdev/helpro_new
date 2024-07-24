<?php
namespace App\Http\Controllers\Admin;

use App\Exports\GeneralExport;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SubjectController extends Controller
{
    public function index()
    {
        $students = Student::where('status', 'active')->get();
        return view('content.tables.subjects', compact('students'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $data = new Subject;
        $data->name = $request->name;
        $data->student_id = $request->student_id;
        $data->save();
        return response([
            'header' => 'Added',
            'message' => 'Subject Added successfully',
            'table' => 'subject-table' 
        ]);
    }
    public function edit($id)
    {
        $name = Subject::findOrFail($id);
        return response($name);
    }

    public function update(Request $request)
    {

    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:subjects,id',
            'status' => 'required|in:active,blocked',
        ]);

        Subject::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'subject status updated successfully',
            'table' => 'subject-table',
        ]);
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'subject deleted successfully',
            'table' => 'subject-table',
        ]);
    }

    public function export()
    {
        return Excel::download(new GeneralExport(new Subject), 'subject.xlsx');
    }

}
