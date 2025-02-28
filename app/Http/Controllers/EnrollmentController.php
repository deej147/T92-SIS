<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Student $student)
    {
        // Get subjects that the student is not enrolled in
        $availableSubjects = Subject::whereNotIn('id', $student->subjects->pluck('id'))
            ->get();

        return view('admin.enrollments.createForm', compact('student', 'availableSubjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        // Check if student is already enrolled in this subject
        if ($student->subjects()->where('subject_id', $request->subject_id)->exists()) {
            return redirect()->back()
                ->with('error', 'Student is already enrolled in this subject.');
        }

        // Enroll the student
        $student->subjects()->attach($request->subject_id);

        return redirect()->back()
            ->with('success', 'Student enrolled in subject successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }

    public function remove(Student $student, Subject $subject)
    {
        try {
            // Check if the subject has a grade
            $enrollment = $student->subjects()
                ->wherePivot('subject_id', $subject->id)
                ->withPivot('grade')
                ->first();

            if (!$enrollment) {
                throw new \Exception('Enrollment not found.');
            }

            if ($enrollment->pivot->grade !== null) {
                return redirect()->back()
                    ->with('error', 'Cannot remove subject. The student already has a grade for this subject.');
            }

            $student->subjects()->detach($subject->id);

            return redirect()->back()
                ->with('success', 'Student unenrolled from subject successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while removing the subject. Please try again.');
        }
    }
}
