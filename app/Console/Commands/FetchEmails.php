<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FetchEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch emails, extract cargo ID, and save attachments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Connect to the IMAP server
        $client = Client::account('imap');  // Using the 'imap' account from config/imap.php

        // Open the inbox folder
        $folder = $client->getFolder('INBOX');

        // Get emails from the last 90 days
        $date = Carbon::now()->subDays(90)->toDateString();
        $messages = $folder->messages()->since($date)->get();

        // Loop through the messages
        foreach ($messages as $message) {
            $subject = $message->getSubject();
            $this->info('Subject: ' . $subject);

            // Check if subject contains 'cargo id'
            if (Str::contains(strtolower($subject), 'cargo id')) {
                // Extract cargo id from the subject using a regex pattern
                preg_match('/cargo id (\d+)/i', $subject, $matches);

                if (isset($matches[1])) {
                    $cargoId = $matches[1];

                    // Create a directory with the cargo ID if it doesn't exist
                    $directoryPath = storage_path('app/attachments/' . $cargoId);
                    if (!File::exists($directoryPath)) {
                        File::makeDirectory($directoryPath, 0755, true);
                    }

                    // Get all attachments for the email
                    $attachments = $message->getAttachments();

                    // Loop through each attachment and save it to the directory
                    foreach ($attachments as $attachment) {
                        $attachment->save($directoryPath . '/' . $attachment->getName());

                        $this->info('Saved attachment: ' . $attachment->getName() . ' to ' . $directoryPath);
                    }

                    $this->line('Attachments for Cargo ID ' . $cargoId . ' have been saved.');
                } else {
                    $this->warn('No cargo id found in the subject.');
                }
            } else {
                $this->info('No cargo id in subject: ' . $subject);
            }

            $this->line('------------------------------------');
        }
    }
}
