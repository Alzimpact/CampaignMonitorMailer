<?php

namespace CampaignMonitorMailer\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMonitor extends Mailable
{
    use Queueable, SerializesModels;

    protected string $smartEmailId;

    protected array $data;

    public function __construct(string $smartEmailId, array $data = [])
    {
        $this->smartEmailId = $smartEmailId;
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Campaign Monitor Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->getHtmlContent(),
        );
    }

    protected function getHtmlContent(): string
    {
        // Campaign Monitor smart emails don't need HTML content
        // The content is handled by the smart email template
        return '<p>This email is sent via Campaign Monitor smart email.</p>';
    }

    public function build()
    {
        return $this->view('emails.campaign-monitor')
            ->with($this->data)
            ->withSymfonyMessage(function ($message) {
                $message->getHeaders()
                    ->addTextHeader('X-Campaign-Monitor-Smart-Email-ID', $this->smartEmailId)
                    ->addTextHeader('X-Campaign-Monitor-Data', json_encode($this->data));
            });
    }

    public function getSmartEmailId(): string
    {
        return $this->smartEmailId;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
