<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Get all courses (accessible to all users)
    public function index()
    {
        $courses = Course::with('instructor:id,firstname,lastname,email')->get();
        return response()->json($courses, 200);
    }
    // Create a course (only instructors)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'instructor_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Course created successfully.', 'course' => $course], 201);
    }
    //
    // Update a course (only instructors)
    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            // Ensure the logged-in instructor owns the course
            if ($course->instructor_id !== $request->user()->id) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }

            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
            ]);

            $course->update($request->all());
            return response()->json(['message' => 'Course updated successfully.', 'course' => $course], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while registering the user.'], 500);
        }
    }
    // Delete
    // Delete a course (only instructors)
    public function destroy(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Ensure the logged-in instructor owns the course
        if ($course->instructor_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully.'], 200);
    }
}
