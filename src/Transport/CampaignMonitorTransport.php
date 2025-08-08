<?php

namespace CampaignMonitorMailer\Transport;

use CampaignMonitorMailer\Exceptions\CampaignMonitorException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class CampaignMonitorTransport extends AbstractTransport
{
    protected string $apiKey;

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = config('campaign-monitor.config.apiKey');
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        // Extract smart email ID and data from headers
        $smartEmailId = $this->extractSmartEmailId($email);
        $data = $this->extractData($email);

        if (! $smartEmailId) {
            throw new CampaignMonitorException('Smart email ID not found in message headers');
        }

        $payload = $this->formatPayload($email, $data);
        $this->cmPost($payload, $smartEmailId);
    }

    private function formatPayload(Email $email, array $data): array
    {
        return [
            'To' => $this->extractRecipients($email->getTo()),
            'Cc' => $this->extractRecipients($email->getCc()),
            'Bcc' => $this->extractRecipients($email->getBcc()),
            'Data' => $data,
        ];
    }

    private function extractRecipients($addresses): array
    {
        if (! $addresses) {
            return [];
        }

        return array_map(fn($address) => $address->toString(), $addresses);
    }

    private function extractSmartEmailId(Email $email): ?string
    {
        $header = $email->getHeaders()->get('X-Campaign-Monitor-Smart-Email-ID');

        return $header ? $header->getBodyAsString() : null;
    }

    private function extractData(Email $email): array
    {
        $header = $email->getHeaders()->get('X-Campaign-Monitor-Data');
        if ($header) {
            return json_decode($header->getBodyAsString(), true) ?? [];
        }

        return [];
    }

    protected function cmPost(array $payload, string $smartEmailId): void
    {
        try {
            $cm = new \CS_REST_Transactional_SmartEmail(
                $smartEmailId,
                ['api_key' => $this->apiKey]
            );

            // TODO parameterize 'YES'. I believe that its a consent to track option.
            $result = $cm->send($payload, 'Yes');

            if (! $result->was_successful()) {
                throw new CampaignMonitorException('Campaign Monitor API error: ' . json_encode($result->response));
            }
        } catch (\Exception $e) {
            Log::error('Campaign Monitor transport error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            throw $e;
        }
    }

    public function __toString(): string
    {
        return 'campaignmonitor-smartemail';
    }
}
