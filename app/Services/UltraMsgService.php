<?php

namespace App\Services;

use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UltraMSGService
{
    protected string $apiUrl;
    protected string $token;
    protected string $instance_id;
    protected int $priority;

    protected array $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ];
    public function __construct()
    {
        $this->apiUrl = config('services.ultramsg.api_url', 'https://api.ultramsg.com');
        $this->token = config('services.ultramsg.token');
        $this->instance_id = config('services.ultramsg.instance_id');
        $this->priority = config('services.ultramsg.priority', 10);
    }

    /**
     * Send a message via UltraMSG
     *
     * @param string $to Recipient phone number with international code
     * @param array $message Message content and parameters
     * @param string $type Message type: 'text' or 'template'
     * @return array Response from the API
     * @throws \Exception
     */
    public function send(string $to, array $message, string $type = 'text'): array
    {
        $to = $this->normalizePhoneNumber($to);

        if ($type === 'template') {
            return $this->sendTemplateFromDatabase(
                $to,
                $message['name'],                 // DB template id OR name
                $message['components'] ?? []
            );
        }

        if ($type === 'text') {
            return $this->sendTextMessage($to, $message['body']);
        }

        throw new \Exception("UltraMSG unsupported message type: {$type}");
    }

    /**
     * Summary of sendTemplateFromDatabase
     * @param string $to
     * @param string $templateKey
     * @param array $components
     * @throws \Exception
     * @return array
     */
    private function sendTemplateFromDatabase(string $to,string $templateKey,array $components): array {
        // Find template by ID or NAME
        $template = WhatsappTemplate::where('id', $templateKey)
            ->orWhere('name', $templateKey)
            ->first();

        if (!$template) {
            throw new \Exception("WhatsApp template not found: {$templateKey}");
        }

        $text = $template->message;

        // Extract values from components
        $replacements = $this->extractTemplateParameters($components);

        // Replace {{keys}} in message
        foreach ($replacements as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }

        return $this->sendTextMessage($to, trim($text));
    }

    /**
     * Summary of extractTemplateParameters
     * @param array $components
     * @return array
     */
    private function extractTemplateParameters(array $components): array
    {
        $data = [];

        foreach ($components as $component) {
            foreach ($component['parameters'] ?? [] as $param) {
                if (!empty($param['key'])) {
                    $data[$param['key']] = $param['text'] ?? '';
                }
            }
        }

        return $data;
    }

    /**
     * Summary of sendTextMessage
     * @param string $to
     * @param string $text
     * @throws \Exception
     * @return array
     */
    private function sendTextMessage(string $to, string $text): array
    {
        $this->validateInputs($to, $text);

        $endpoint = "/{$this->instance_id}/messages/chat";

        return $this->request($endpoint, [
            'token' => $this->token,
            'to' => $to,
            'body' => $text,
            'priority' => $this->priority,
        ]);
    }

    /**
     * Summary of request
     * @param string $endpoint
     * @param array $params
     * @throws \Exception
     * @return array
     */
    private function request(string $endpoint, array $params): array
    {
        $response = Http::withHeaders($this->headers)
            ->asForm()
            ->timeout(30)
            ->post($this->apiUrl . $endpoint, $params);

        if (!$response->successful()) {
            Log::error('UltraMSG API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('UltraMSG API failed');
        }

        return $response->json();
    }

    /**
     * Summary of normalizePhoneNumber
     * @param string $phone
     * @return string
     */
    private function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return str_starts_with($phone, '+') ? $phone : ltrim($phone, '0');
    }

    /**
     * Summary of validateInputs
     * @param string $to
     * @param string $text
     * @throws \Exception
     * @return void
     */
    private function validateInputs(string $to, string $text): void
    {
        if (empty($to)) {
            throw new \Exception('Recipient phone number is required');
        }

        if (empty($text)) {
            throw new \Exception('Message content is required');
        }
    }
}
