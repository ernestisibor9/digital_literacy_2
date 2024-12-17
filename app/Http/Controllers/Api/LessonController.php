<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    //
        /**
     * Display a listing of the lessons for a course.
     * Accessible to Learners and others with access to a course.
     */
    public function index($courseId)
    {
        $lessons = Lesson::where('course_id', $courseId)->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $lessons,
        ], 200);
    }
    /**
     * Store a newly created lesson.
     * Accessible to Admins and Instructors.
     */
    public function store(Request $request)
    {

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $lesson = Lesson::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully.',
            'data' => $lesson,
        ], 201);
    }
    //
        /**
     * Update the specified lesson.
     * Accessible to Admins and Instructors.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $lesson->update($request->only(['title', 'description', 'order']));

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully.',
            'data' => $lesson,
        ], 200);
    }
    //
        /**
     * Remove the specified lesson.
     * Accessible to Admins and Instructors.
     */
    public function delete($id)
    {
        $lesson = Lesson::findOrFail($id);

        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully.',
        ], 200);
    }
}
