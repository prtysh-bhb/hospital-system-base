<?php

namespace App\Events;

use App\Services\WhatsAppService;
use App\Services\UltraMSGService;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifiyUserEvent
{
    use Dispatchable, SerializesModels;

    public $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;

        // Send WhatsApp notification directly in event
        $this->sendWhatsAppNotification();
    }

    /**
     * Send WhatsApp notification directly from event
     */
    private function sendWhatsAppNotification()
    {
        try {
            // Extract data from array
            $phoneNumber = $this->data['phone_number'] ?? null;
            $templateName = $this->data['template_name'] ?? null;
            $components = $this->data['components'] ?? [];
            $appointmentData = $this->data['appointment_data'] ?? [];

            // Validate phone number
            if (!$phoneNumber) {
                return;
            }

            // Validate template name
            if (!$templateName) {
                return;
            }

            // Initialize WhatsApp service
            // $whatsappService = app(WhatsAppService::class);
            $whatsappService = $this->getWhatsAppService();

            // Build message with template name and components
            $message = [
                'name' => $templateName,
                'language' => 'en_US',
                'components' => $components,
            ];

            // Send WhatsApp message via template
            $response = $whatsappService->send(
                $phoneNumber,
                $message,
                'template'
            );

            // Log success with response
            Log::info('WhatsApp notification sent successfully', [
                'request' => $this->data,
                'response' => $response ?? null,
            ]);

        } catch (\Exception $e) {
            // Log all errors
            Log::error('WhatsApp notification failed', [
                'request' => $this->data,
                'response' => $response ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getWhatsAppService(): ?object  
    {
        $driver = config('services.whatsapp_driver');

        return match ($driver) {
            'ultramsg' => new UltraMSGService(),
            'waba' => new WhatsAppService(),
            default => null,
        };
    }
}
