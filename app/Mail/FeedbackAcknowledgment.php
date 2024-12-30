<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackAcknowledgment extends Mailable
{
    use SerializesModels;

    public $feedbackData;

    public function __construct($feedbackData)
    {
        $this->feedbackData = $feedbackData;
    }

    public function build()
    {
        // Prepare the plain text content for the user
        $content = "Thank you for your feedback, {$this->feedbackData['name']}!\n\n";
        $content .= "We have received your feedback and will review it shortly. Here are the details you submitted:\n\n";
        $content .= "Email: {$this->feedbackData['email']}\n";
        $content .= "Contact Number: {$this->feedbackData['contact_number']}\n\n";
        $content .= "We will get back to you as soon as possible.";

        // Send raw plain text content directly to the email
        return $this->from('yumzyfooddealer@gmail.com')
            ->subject('Feedback Acknowledgment')
            ->text($content);  // Send raw text content
    }
}
