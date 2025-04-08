<?php

use App\Lib\FileManager;
use App\Models\GeneralSetting;
use App\Models\MailTemplate;
use Illuminate\Support\Facades\Cache;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) {
        return @$general->$key;
    }

    return $general;
}
function notify($user, $templateName, $shortCodes = [], $sendVia = null, $createLog = true, $clickValue = null)
{
    try {
        $template = MailTemplate::query()->where('act', $templateName)->first();
        if (!$template) {
            throw new \Exception("Mail template '$templateName' not found.");
        }

        $globalTemplate = gs('email_template');
        if (!$globalTemplate) {
            throw new \Exception("Global email template not found.");
        }

        // Ensure $shortCodes is always an array
        if (!is_array($shortCodes)) {
            $shortCodes = [];
        }

        $globalShortCodes = [
            '{{fullname}}' => $user->name,
            '{{site_name}}' => gs('site_name'),
        ];

        // Replace placeholders in content
        $content = $template->body;

        foreach ($shortCodes as $key => $value) {
            $content = str_replace('{{' .$key. '}}', $value, $content);
        }



        // Replace placeholders in global template
        $globalTemplate = str_replace(array_keys($globalShortCodes), array_values($globalShortCodes), $globalTemplate);

        // Final email body
        $finalEmailBody = str_replace('{{message}}', $content, $globalTemplate);
        $res = sendMailTrap($user, $template->subject, $finalEmailBody);


    } catch (\Exception $e) {
        return response($e->getMessage(), 500);
    }
}

function sendMailTrap($user, $subject, $finalMessage)
{
    try {
        $general = gs();

        $mailTrap = MailtrapClient::initSendingEmails(
            apiKey: env('MAIL_TRAP_API_KEY'),
        );

        $email = (new MailtrapEmail())
            ->from(new Address($general->email_from, $general->site_name))
            ->replyTo(new Address($general->email_from))
            ->to(new Address($user->email, $user->name))
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->html($finalMessage)
            ->category('Integration Test');

        $response = $mailTrap->send($email);


        return $response; // ✅ Return the response for debugging

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
function verificationCode($length)
{
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int)($min - 1) . '9';
    return random_int($min, $max);
}
function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}
function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}
function rssPaginate($paginated)
{
    return [
        'meta' => [
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'per_page'     => $paginated->perPage(),
            'total'        => $paginated->total(),
        ],
        'links' => [
            'first' => $paginated->url(1),
            'last'  => $paginated->url($paginated->lastPage()),
            'next'  => $paginated->nextPageUrl(),
            'prev'  => $paginated->previousPageUrl(),
        ]
    ];
}
