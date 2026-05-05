<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class CreateInvoiceCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'invoice:create {user?} {email?} {lang?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create invoice for user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
 
     
    
    }
}
