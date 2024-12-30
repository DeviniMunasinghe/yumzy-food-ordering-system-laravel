<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackReceived;
use App\Mail\FeedbackAcknowledgment;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function submitFeedback(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:15',
            'message' => 'required|string',
        ]);
    
        try {
            // Send the feedback to the company email
            Mail::to('yumzyfooddealer@gmail.com')->send(new FeedbackReceived($validatedData));
    
            // Send the acknowledgment email to the user
            Mail::to($validatedData['email'])->send(new FeedbackAcknowledgment($validatedData));
    
            // Return success response
            return response()->json([
                'message' => 'Feedback submitted successfully. You will receive a confirmation email shortly.',
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error sending feedback email: ' . $e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'There was an error submitting your feedback.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
