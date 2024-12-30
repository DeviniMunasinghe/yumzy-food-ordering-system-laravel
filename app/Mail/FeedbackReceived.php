<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackReceived extends Mailable
{
    use SerializesModels;

    public $feedbackData;

    public function __construct($feedbackData)
    {
        $this->feedbackData = $feedbackData;
    }

    public function build()
    {
        // Prepare the plain text content for the company
        $content = "New Feedback Received:\n\n";
        $content .= "Name: {$this->feedbackData['name']}\n";
        $content .= "Email: {$this->feedbackData['email']}\n";
        $content .= "Contact Number: {$this->feedbackData['contact_number']}\n";
        $content .= "Message: {$this->feedbackData['message']}\n";

        // Send raw plain text content directly to the email
        return $this->from('yumzyfooddealer@gmail.com')
            ->subject('New Feedback Received')
            ->text($content);  // Send raw text content
    }
}
