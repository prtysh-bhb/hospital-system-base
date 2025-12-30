<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;

    protected $config;

    protected $baseUrl;

    protected $headers;
    protected $language_code;

    public function __construct()
    {
        $this->config = config('services.whatsapp');
        $this->baseUrl = $this->config['api_url'];
        $this->language_code = $this->config['language_code'];
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'verify' => false, // Only for development
        ]);

        $this->headers = [
            'Authorization' => 'Bearer '.$this->config['access_token'],
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Validate WhatsApp phone number format
     */
    public function validatePhoneNumber(string $phoneNumber): array
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strlen($cleaned) < 10) {
            return ['valid' => false, 'message' => 'Phone number too short'];
        }

        if (strlen($cleaned) > 15) {
            return ['valid' => false, 'message' => 'Phone number too long'];
        }

        if (! preg_match('/^\d{10,15}$/', $cleaned)) {
            return ['valid' => false, 'message' => 'Invalid phone number format'];
        }

        return ['valid' => true, 'message' => 'Phone number is valid', 'formatted' => $cleaned];
    }

    /**
     * Send WhatsApp message
     */
    public function send(string $to, array $message, string $type = 'text')
    {
        try {
            $validation = $this->validatePhoneNumber($to);
            if (! $validation['valid']) {
                throw new \Exception($validation['message']);
            }

            $to = $validation['formatted'];
            $url = "/{$this->config['phone_number_id']}/messages";

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => $type,
            ];

            switch ($type) {
                case 'text':
                    $payload['text'] = ['body' => $message['body']];
                    break;

                case 'image':
                    $payload['image'] = [
                        'link' => $message['link'],
                        'caption' => $message['caption'] ?? null,
                    ];
                    break;

                case 'document':
                    $payload['document'] = [
                        'link' => $message['link'],
                        'filename' => $message['filename'],
                        'caption' => $message['caption'] ?? null,
                    ];
                    break;

                case 'template':
                    $payload['template'] = [
                        'name' => trim($message['name']),
                        'language' => [
                            'code' => $message['language'] ?? $this->language_code
                        ],
                        'components' => $message['components'] ?? [],
                    ];
                    break;

                case 'interactive':
                    $payload['interactive'] = $message['interactive'];
                    break;
            }

            // Log outgoing payload
            Log::channel('whatsapp_log')->info('Sending WhatsApp message', [
                'to' => $to,
                'type' => $type,
                'payload' => $payload,
            ]);

            $response = $this->client->post($url, [
                'headers' => $this->headers,
                'json' => $payload,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Log response
            Log::channel('whatsapp_log')->info('WhatsApp API response', $responseBody);

            return $responseBody;

        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

            // Log error
            Log::channel('whatsapp_log')->error('WhatsApp API Error', [
                'message' => $e->getMessage(),
                'response' => $errorBody,
            ]);

            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                switch ($statusCode) {
                    case 400:
                        throw new \Exception('Bad Request: Check phone number format or Phone Number ID.');
                    case 401:
                        throw new \Exception('Unauthorized: Access token is invalid or expired.');
                    case 403:
                        throw new \Exception('Forbidden: Your app may not have permission to perform this action.');
                    case 404:
                        throw new \Exception('Not Found: Endpoint or resource not found.');
                    case 429:
                        throw new \Exception('Too Many Requests: You are being rate limited by WhatsApp API.');
                    default:
                        throw new \Exception("WhatsApp API returned status {$statusCode}: ".$errorBody);
                }
            }

            throw new \Exception('WhatsApp API Error: '.$e->getMessage());
        }
    }

    /**
     * Upload media to WhatsApp
     */
    public function uploadMedia(UploadedFile $file)
    {
        try {
            $mimeType = $file->getMimeType();

            $type = str_starts_with($mimeType, 'image/') ? 'image' :
                (str_starts_with($mimeType, 'video/') ? 'video' :
                    (str_starts_with($mimeType, 'audio/') ? 'audio' : 'document'));

            $url = "/{$this->config['phone_number_id']}/media";

            $response = $this->client->post($url, [
                'headers' => ['Authorization' => 'Bearer '.$this->config['access_token']],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'rb'),
                        'filename' => $file->getClientOriginalName(),
                        'headers' => ['Content-Type' => $mimeType],
                    ],
                    ['name' => 'type', 'contents' => $type],
                    ['name' => 'messaging_product', 'contents' => 'whatsapp'],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::channel('whatsapp_log')->info('Media uploaded to WhatsApp', $data);

            return $data;

        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::channel('whatsapp_log')->error('WhatsApp Media Upload Error', ['error' => $errorBody]);
            throw new \Exception('Media upload failed');
        }
    }

    /**
     * Get media URL
     */
    public function getMediaUrl(string $mediaId)
    {
        try {
            $response = $this->client->get("/{$mediaId}", [
                'headers' => ['Authorization' => 'Bearer '.$this->config['access_token']],
                'query' => ['phone_number_id' => $this->config['phone_number_id']],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (! isset($data['url'])) {
                Log::channel('whatsapp_log')->error('WhatsApp Get Media Error: URL not found', ['response' => $data]);

                return null;
            }

            Log::channel('whatsapp_log')->info('WhatsApp Get Media URL', $data);

            return [
                'media_id' => $data['id'],
                'url' => $data['url'],
                'mime_type' => $data['mime_type'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::channel('whatsapp_log')->error('WhatsApp Get Media Error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Delete media
     */
    public function deleteMedia(string $mediaId)
    {
        try {
            $response = $this->client->delete("/{$mediaId}", ['headers' => $this->headers]);

            $success = $response->getStatusCode() === 200;
            Log::channel('whatsapp_log')->info('WhatsApp Delete Media', ['media_id' => $mediaId, 'success' => $success]);

            return $success;

        } catch (RequestException $e) {
            Log::channel('whatsapp_log')->error('WhatsApp Delete Media Error', ['message' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Create message template
     */
    public function createTemplate(array $data)
    {
        try {
            $url = "/{$this->config['business_account_id']}/message_templates";

            $payload = [
                'name' => strtolower(trim($data['name'])),
                'language' => !empty($data['language']) ? trim($data['language']) : $this->language_code,
                'category' => $data['category'],
                'components' => [],
            ];

            if (! empty($data['header'])) {
                $payload['components'][] = [
                    'type' => 'HEADER',
                    'format' => 'TEXT',
                    'text' => $data['header'],
                ];
            }

            $payload['components'][] = ['type' => 'BODY', 'text' => $data['body']];

            if (! empty($data['footer'])) {
                $payload['components'][] = ['type' => 'FOOTER', 'text' => $data['footer']];
            }

            if (! empty($data['buttons'])) {
                $payload['components'][] = ['type' => 'BUTTONS', 'buttons' => $data['buttons']];
            }

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->config['access_token'],
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody(), true);

            Log::channel('whatsapp_log')->info('WhatsApp Template Created', $result);

            return $result;

        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::channel('whatsapp_log')->error('WhatsApp Template Error', ['error' => $errorBody]);
            throw new \Exception($errorBody);
        }
    }

    /**
     * Get message template list
     */
    public function getTemplates()
    {
        try {
            $response = $this->client->get("/{$this->config['business_account_id']}/message_templates", [
                'headers' => $this->headers,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::channel('whatsapp_log')->info('WhatsApp Templates List', $data);

            return $data;
        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::channel('whatsapp_log')->error('WhatsApp Get Templates Error', ['error' => $errorBody]);
            throw new \Exception($errorBody);
        }
    }

    /**
     * Delete message template
     * Deleting templates via WhatsApp API is not supported. Please delete templates manually via the WhatsApp Business Manager UI.
     */
    // public function deleteTemplate(string $templateName)
    // {
    //     try {
    //         $response = $this->client->delete("/{$this->config['business_account_id']}/message_templates?name={$templateName}", [
    //             'headers' => $this->headers,
    //         ]);

    //         $success = $response->getStatusCode() === 200;

    //         Log::channel('whatsapp_log')->info('WhatsApp Template Deleted', ['template' => $templateName, 'success' => $success]);

    //         return $success;

    //     } catch (RequestException $e) {
    //         $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
    //         Log::channel('whatsapp_log')->error('WhatsApp Delete Template Error', ['error' => $errorBody]);
    //         return false;
    //     }
    // }

    /**
     * Verify webhook
     */
    public function verifyWebhook(string $mode, string $token, string $challenge)
    {
        if ($mode === 'subscribe' && $token === $this->config['verify_token']) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }
}
