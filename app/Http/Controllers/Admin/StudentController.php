<?php
namespace App\Http\Controllers\Admin;

use App;
use App\Exports\GeneralExport;
use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use Excel;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('content.tables.students');
    }
    public function store(Request $request)
    {
        $data = new Student;
        $data->name = $request->name;
        $data->rollno = $request->rollno;
        $data->city = $request->city;
        if ($request->image) {
            $data->image = FileUploader::uploadFile($request->image, 'images/student');
        }
        $data->state = $request->state;
        $data->save();
        return response([
            'header' => 'Added',
            'message' => 'Added successfully',
            'table' => 'student-table',
        ]);

    }
    public function edit($id)
    {
        // $name = Student::findOrFail($id);
        // return response($name);


        $name = new Student();
        $data = $name::where('id', $id)->first();
        return response($data);

    }

    public function update(Request $request)
    {
        $data = Student::findOrFail($request->id);
        $data->name = $request->name;
        $data->rollno = $request->rollno;
        $data->city = $request->city;
        if ($request->image) {
            $data->image = FileUploader::uploadFile($request->image, 'images/student');
        }
        $data->state = $request->state;
        $data->save();
        return response([
            'header' => 'Updated',
            'message' => 'Updated successfully',
            'table' => 'student-table',
        ]);

    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:students,id',
            'status' => 'required|in:active,blocked',
        ]);

        Student::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'student status updated successfully',
            'table' => 'student-table',
        ]);
    }

    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'student deleted successfully',
            'table' => 'student-table',
        ]);
    }
}
