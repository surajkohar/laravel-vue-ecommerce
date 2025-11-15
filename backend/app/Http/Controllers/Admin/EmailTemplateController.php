<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Admin\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends ApiController
{

    public function index(Request $request)
    {
        try {

            $where = [];

            $listing = EmailTemplate::getListing($request, $where);

            return response()->json([
                'status' => true,
                'page' => $listing
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Somethings went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        return EmailTemplate::findOrFail($id);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates',
            'type' => 'required|in:admin,client,system',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'short_codes' => 'required|string',
            'is_active' => 'boolean'
        ]);

        return EmailTemplate::create($validated);
    }


    public function update(Request $request, $id)
{
    try {
        $template = EmailTemplate::findOrFail($id);
        $data = $request->toArray();
        $validator = Validator::make($data, [
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:admin,client,system',
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'short_codes' => 'sometimes|string',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors(), 422);
        }

        unset($data['slug']);

        // Update the template
        EmailTemplate::modify($id, $data);

        return $this->respondWithSuccess([
            'message' => 'Email template updated successfully',
            'template' => $template
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to update email template', [
            'error' => $e->getMessage(),
            'id' => $id,
            'request' => $request->all()
        ]);

        return $this->respondWithError('Failed to update email template', 500);
    }
}

    public function destroy($id)
    {
        $template = EmailTemplate::findOrFail($id);

        if ($template->is_system) {
            return response()->json([
                'message' => 'System templates cannot be deleted'
            ], 403);
        }

        $template->delete();
        return response()->json(['message' => 'Template deleted']);
    }


    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:email_templates,id',
            'to' => 'required|email',
            'variables' => 'required|array',
            'cc' => 'nullable|array',
            'bcc' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*.path' => 'required_with:attachments|string',
            'attachments.*.name' => 'nullable|string',
            'attachments.*.mime' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $template = EmailTemplate::find($request->template_id);

        // Replace variables in content
        $content = $template->description;
        foreach ($request->variables as $key => $value) {
            $content = str_replace('{'.$key.'}', $value, $content);
        }

        // Replace variables in subject
        $subject = $template->subject;
        foreach ($request->variables as $key => $value) {
            $subject = str_replace('{'.$key.'}', $value, $subject);
        }

        try {
            Mail::to($request->to)
                ->cc($request->cc ?? [])
                ->bcc($request->bcc ?? [])
                ->send(new DynamicEmail($subject, $content, $request->attachments ?? []));

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id'
        ]);

        $template = EmailTemplate::find($request->template_id);
        $user = $request->user();

        // Create test variables
        $variables = array_fill_keys(
            explode(',', $template->short_codes),
            'TEST_VALUE'
        );

        // Replace variables in content
        $content = $template->description;
        foreach ($variables as $key => $value) {
            $content = str_replace('{'.$key.'}', $value, $content);
        }

        // Replace variables in subject
        $subject = '[TEST] ' . $template->subject;
        foreach ($variables as $key => $value) {
            $subject = str_replace('{'.$key.'}', $value, $subject);
        }

        try {
            Mail::to($user->email)
                ->send(new DynamicEmail($subject, $content));

            return response()->json(['message' => 'Test email sent successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send test email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get template variables
     *
     * @param int $id Template ID
     * @return array
     *
     * GET /api/email-templates/{id}/variables
     */
    public function getVariables($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return [
            'variables' => array_map('trim', explode(',', $template->short_codes)),
            'template' => [
                'subject' => $template->subject,
                'description' => $template->description
            ]
        ];
    }

    /**
     * Initialize default system templates
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * POST /api/email-templates/initialize
     */
    public function initializeTemplates()
    {
        $defaultTemplates = [
            [
                'title' => 'Admin Registration',
                'slug' => 'admin-registration',
                'type' => 'system',
                'subject' => 'Admin Registration Successful',
                'description' => '<p>Dear {first_name} {last_name}<br><br>You account has been registered on {company_name}. Please use the below credentails for login.<br><br>{admin_link}<br>Email: {email}<br>Password: {password}<br><br>Thanks</p>',
                'short_codes' => 'first_name,last_name,email,password,admin_link,company_name',
                'is_active' => true,
                'is_system' => true
            ],
            [
                'title' => 'Forgot Password',
                'slug' => 'admin-forgot-password',
                'type' => 'system',
                'subject' => 'Forgot Password',
                'description' => '<p>Dear {first_name} {last_name},<br><br>Please click on below link to reset your account password.<br><br>{recovery_link}<br><br>Thank you!</p>',
                'short_codes' => 'first_name,last_name,email,recovery_link,admin_link,company_name',
                'is_active' => true,
                'is_system' => true
            ],
            // Add other default templates...
        ];

        foreach ($defaultTemplates as $templateData) {
            EmailTemplate::firstOrCreate(
                ['slug' => $templateData['slug']],
                $templateData
            );
        }

        return response()->json(['message' => 'Default templates initialized']);
    }
}
