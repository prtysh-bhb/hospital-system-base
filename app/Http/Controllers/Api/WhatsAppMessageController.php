<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhatsAppMessageController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Send text message
     */
    public function sendTextMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            $response = $this->whatsappService->sendMessage(
                $request->phone_number,
                ['body' => $request->message],
                'text'
            );

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Message sent successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send image message
     */
    public function sendImageMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'image_url' => 'required|url',
            'caption' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            $response = $this->whatsappService->sendMessage(
                $request->phone_number,
                [
                    'link' => $request->image_url,
                    'caption' => $request->caption,
                ],
                'image'
            );

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Image sent successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send document message
     */
    public function sendDocumentMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'document_url' => 'required|url',
            'filename' => 'required|string',
            'caption' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            $response = $this->whatsappService->sendMessage(
                $request->phone_number,
                [
                    'link' => $request->document_url,
                    'filename' => $request->filename,
                    'caption' => $request->caption,
                ],
                'document'
            );

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Document sent successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send template message
     */
    public function sendTemplateMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'template_name' => 'required|string',
            'language' => 'nullable|string',
            'components' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            // Format phone number
            $phoneNumber = preg_replace('/[^0-9]/', '', $request->phone_number);

            // Trim template name and language
            $templateName = trim($request->template_name);
            $language = $request->language ? trim($request->language) : null;

            $response = $this->whatsappService->sendMessage(
                $phoneNumber,
                [
                    'name' => $templateName,
                    'components' => $request->components ?? [],
                ],
                'template'
            );
            // dd($response);

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Template message sent successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get templates list
     */
    public function getTemplates()
    {
        try {
            $templates = $this->whatsappService->getTemplates();

            return response()->json([
                'success' => true,
                'data' => $templates,
                'message' => 'Templates retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create template
     */
    public function createTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[a-z0-9_]+$/',
            'language' => 'nullable|string',
            'category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
            'body' => 'required|string',
            'header' => 'nullable|string',
            'footer' => 'nullable|string',
            'buttons' => 'nullable|array',
        ]);

        try {
            $response = $this->whatsappService->createTemplate($request->all());

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // public function deleteTemplate($name)
    // {
    //     try {
    //         $success = $this->whatsappService->deleteTemplate($name);
    //         if ($success) {
    //             return response()->json(['status' => 'success', 'message' => "Template '{$name}' deleted"]);
    //         } else {
    //             return response()->json(['status' => 'error', 'message' => "Failed to delete template '{$name}'"], 500);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Upload media
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        try {
            $response = $this->whatsappService->uploadMedia($request->file('file'));

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Media uploaded successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch media URL from WhatsApp API
     */
    public function fetchMedia(Request $request)
    {
        $request->validate([
            'media_id' => 'required|string',
        ]);

        $media = $this->whatsappService->getMediaUrl($request->media_id);

        if (! $media) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch media URL',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $media,
            'message' => 'Media URL fetched successfully',
        ]);
    }

    /**
     * Delete media from WhatsApp API
     */
    public function deleteMedia(Request $request)
    {
        $request->validate([
            'media_id' => 'required|string',
        ]);

        $mediaId = $request->input('media_id');

        $deleted = $this->whatsappService->deleteMedia($mediaId);

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media. Check if media ID is valid.',
            ], 400);
        }
    }
}
