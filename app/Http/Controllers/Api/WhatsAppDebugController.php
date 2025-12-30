<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppDebugController extends Controller
{
    /**
     * Check WhatsApp configuration and credentials
     */
    public function checkConfig()
    {
        $config = config('whatsapp');

        $result = [
            'status' => 'Configuration Check',
            'api_url' => $config['api_url'] ?? 'NOT SET',
            'phone_number_id' => $config['phone_number_id'] ? 'SET (ID: '.$config['phone_number_id'].')' : 'NOT SET',
            'business_account_id' => $config['business_account_id'] ? 'SET (ID: '.$config['business_account_id'].')' : 'NOT SET',
            'access_token' => $config['access_token'] ? 'SET (length: '.strlen($config['access_token']).' chars)' : 'NOT SET',
            'verify_token' => $config['verify_token'] ? 'SET' : 'NOT SET',
            'all_configured' => $config['phone_number_id'] && $config['business_account_id'] && $config['access_token'] ? true : false,
        ];

        // Log configuration check details
        Log::channel('whatsapp_log')->info('Checking WhatsApp Configuration', $result);

        return response()->json($result);
    }

    /**
     * Validate access token with Meta API (by making a simple API call)
     */
    public function validateToken()
    {
        $token = config('whatsapp.access_token');
        $phoneNumberId = config('whatsapp.phone_number_id');

        if (! $token) {
            return response()->json([
                'valid' => false,
                'message' => 'Access token not configured in .env',
            ], 400);
        }

        if (! $phoneNumberId) {
            return response()->json([
                'valid' => false,
                'message' => 'Phone Number ID not configured in .env',
            ], 400);
        }

        try {
            $client = new Client([
                'base_uri' => 'https://graph.facebook.com/v22.0',
                'timeout' => 10.0,
                'verify' => false,
            ]);

            // Try to fetch phone number info - this validates the token
            $response = $client->get("/{$phoneNumberId}", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Log successful token validation
            Log::channel('whatsapp_log')->info('Token validation successful', [
                'phone_number_details' => $data,
            ]);

            return response()->json([
                'valid' => true,
                'message' => 'Access token is valid and working!',
                'phone_number_details' => $data,
                'status' => 'Ready to send messages',
            ]);

        } catch (\Exception $e) {
            Log::channel('whatsapp_log')->error('Token validation failed: '.$e->getMessage());

            $errorMsg = $e->getMessage();
            $action = 'Generate a new long-lived token from Facebook Developer Tools';

            if (strpos($errorMsg, '401') !== false || strpos($errorMsg, 'Unauthorized') !== false) {
                $action = 'Your token is expired or invalid. Generate a new one: https://developers.facebook.com/tools/accesstoken/';
            } elseif (strpos($errorMsg, '400') !== false) {
                $action = 'Phone Number ID might be incorrect. Check your .env file.';
            }

            return response()->json([
                'valid' => false,
                'message' => 'Token validation failed',
                'error' => $errorMsg,
                'action' => $action,
            ], 400);
        }
    }

    /**
     * Get WhatsApp Business Phone Numbers from Business Account
     */
    public function getPhoneNumbers()
    {
        $businessAccountId = config('whatsapp.business_account_id');
        $token = config('whatsapp.access_token');

        if (! $businessAccountId || ! $token) {
            return response()->json([
                'error' => 'Business Account ID or Token not configured in .env',
            ], 400);
        }

        try {
            $client = new Client([
                'base_uri' => 'https://graph.facebook.com/v22.0',
                'timeout' => 10.0,
                'verify' => false,
            ]);

            // Get all phone numbers from business account
            $response = $client->get("/{$businessAccountId}/phone_numbers", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => [
                    'fields' => 'id,display_phone_number,quality_rating,status',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Log successful phone number retrieval
            Log::channel('whatsapp_log')->info('Successfully retrieved phone numbers from business account', [
                'business_account_id' => $businessAccountId,
                'phone_numbers' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Available WhatsApp phone numbers in your business account',
                'business_account_id' => $businessAccountId,
                'phone_numbers' => $data,
                'instructions' => 'Copy the ID from the first phone number and update WHATSAPP_BUSINESS_PHONE_NUMBER_ID in .env file',
            ]);

        } catch (\Exception $e) {
            Log::channel('whatsapp_log')->error('Failed to retrieve phone numbers from business account: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve phone numbers from business account',
                'message' => $e->getMessage(),
                'business_account_id' => $businessAccountId,
                'action' => 'Verify Business Account ID is correct and token has proper permissions',
            ], 400);
        }
    }

    /**
     * Send test message to validate everything
     */
    public function sendTestMessage(Request $request)
    {
        $phoneNumber = $request->input('phone_number', '923001234567');
        $message = $request->input('message', 'Hello from WhatsApp API');

        $phoneNumberId = config('whatsapp.phone_number_id');
        $token = config('whatsapp.access_token');

        if (! $phoneNumberId || ! $token) {
            return response()->json([
                'success' => false,
                'error' => 'Phone Number ID or Access Token not configured in .env',
            ], 400);
        }

        try {
            $client = new Client([
                'base_uri' => 'https://graph.facebook.com/v22.0',
                'timeout' => 10.0,
                'verify' => false,
            ]);

            // Remove non-digits from phone number
            $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

            // Log the test message attempt
            Log::channel('whatsapp_log')->info('Sending WhatsApp test message', [
                'phone_number_id' => $phoneNumberId,
                'to' => $cleanPhone,
                'message' => $message,
            ]);

            $response = $client->post("/{$phoneNumberId}/messages", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $cleanPhone,
                    'type' => 'template',
                    'template' => [
                        'name' => $message,
                        'language' => [
                            'code' => 'en_US',
                        ],
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Log successful message sending
            Log::channel('whatsapp_log')->info('Test message sent successfully', [
                'result' => $result,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::channel('whatsapp_log')->error('Test message failed: '.$e->getMessage());

            $errorMsg = $e->getMessage();
            $solution = '';
            $action = '';

            if (strpos($errorMsg, 'Session has expired') !== false) {
                $solution = 'Your access token has expired';
                $action = 'Generate a new long-lived token from: https://developers.facebook.com/tools/accesstoken/ and update .env WHATSAPP_BUSINESS_ACCESS_TOKEN';
            } elseif (strpos($errorMsg, '401') !== false) {
                $solution = 'Unauthorized - Token is invalid or expired';
                $action = 'Check your access token in .env file';
            } elseif (strpos($errorMsg, '400') !== false) {
                $solution = 'Bad Request - Check phone number format or Phone Number ID';
                $action = 'Ensure phone number includes country code (e.g., 923001234567)';
            } elseif (strpos($errorMsg, 'Invalid phone') !== false) {
                $solution = 'Phone number format is invalid';
                $action = 'Use format: country_code + number (e.g., 923001234567)';
            } else {
                $solution = 'API Error occurred';
                $action = 'Check logs for more details';
            }

            return response()->json([
                'success' => false,
                'error' => $errorMsg,
                'problem' => $solution,
                'action' => $action,
            ], 400);
        }
    }
}
