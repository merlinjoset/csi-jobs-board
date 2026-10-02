<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $event  received | shortlisted | rejected | interview
     */
    public function __construct(public Application $application, public string $event)
    {
    }

    public function envelope(): Envelope
    {
        $title = $this->application->jobPost->title;

        $subjects = [
            'received' => "New application for {$title}",
            'shortlisted' => "You have been shortlisted for {$title}",
            'rejected' => "Update on your application for {$title}",
            'interview' => "Interview invitation: {$title}",
        ];

        return new Envelope(subject: $subjects[$this->event] ?? "Update on your application for {$title}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application', with: [
            'application' => $this->application->loadMissing(['jobPost.provider', 'seeker']),
            'event' => $this->event,
        ]);
    }
}
