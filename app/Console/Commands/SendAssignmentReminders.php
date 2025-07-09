<?php
// app/Console/Commands/SendAssignmentReminders.php
namespace App\Console\Commands;

use App\Services\TaskNotificationService;
use Illuminate\Console\Command;

class SendAssignmentReminders extends Command
{
    protected $signature = 'send-assignment-reminders';
    protected $description = 'Send reminder emails for assignments due tomorrow';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Sending assignment reminder emails...');
        (new TaskNotificationService())->sendReminderEmails();
        $this->info('Reminder emails sent successfully.');
    }
}