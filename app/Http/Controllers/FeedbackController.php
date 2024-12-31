<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class FeedbackController extends Controller
{
    public function submitFeedback(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:15',
            'message' => 'required|string',
        ]);

        try {
            // Send feedback to company
            Mail::raw($this->getRawFeedbackContent($validatedData), function ($message) {
                $message->to('yumzyfooddealer@gmail.com')
                    ->subject('New Feedback Received')
                    ->from('yumzyfooddealer@gmail.com');
            });

            // Send acknowledgment to user
            Mail::raw($this->getRawAcknowledgmentContent($validatedData), function ($message) use ($validatedData) {
                $message->to($validatedData['email'])
                    ->subject('Feedback Acknowledgment')
                    ->from('yumzyfooddealer@gmail.com');
            });

            return response()->json([
                'message' => 'Feedback submitted successfully. You will receive a confirmation email shortly.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'There was an error submitting your feedback.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getRawFeedbackContent($data)
    {
        return "New Feedback Received:\n\n" .
            "Name: {$data['name']}\n" .
            "Email: {$data['email']}\n" .
            "Contact Number: {$data['contact_number']}\n" .
            "Message: {$data['message']}\n";
    }

    private function getRawAcknowledgmentContent($data)
    {
        return "Thank you for your feedback, {$data['name']}!\n\n" .
            "We have received your feedback and will review it shortly.\n" .
            "Here are the details you submitted:\n\n" .
            "Email: {$data['email']}\n" .
            "Contact Number: {$data['contact_number']}\n\n" .
            "We will get back to you as soon as possible.";
    }

}
