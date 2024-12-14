<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use Carbon\Carbon;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // dd(config('mail.mailers.imap'));
        // dd(config('imap.accounts.imap'));


        // Connect to the IMAP server
        $client = Client::account('imap');  // Using the 'imap' account from mail.php

        // Open the inbox folder
        $folder = $client->getFolder('INBOX');

        // Get emails from the last 7 days
        $date = Carbon::now()->subDays(7)->toDateString();
        $messages = $folder->messages()->since($date)->get();

        // Loop through the messages and print out the details
        foreach ($messages as $message) {
            $this->info('Subject: ' . $message->getSubject());
            // $this->info(string: 'From: ' . $message->getFrom());
            // $this->info('Date: ' . $message->getDate());
            // $this->info('Body: ' . $message->getTextBody());
            $this->line('------------------------------------');
        }
    }
}
