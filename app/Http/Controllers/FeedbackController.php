<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'message' => $request->message
        ]);

        return response()->json(['success' => 'Thank you! Your anonymous feedback has been recorded.']);
    }

    public function index()
{
    $feedbacks = \App\Models\Feedback::latest()->paginate(10);
    return view('admin.feedback.index', compact('feedbacks'));
}

public function readAll() {
    \App\Models\Feedback::where('is_read', false)->update(['is_read' => true]);
    return back()->with('success', 'Counter reset!');
}
}