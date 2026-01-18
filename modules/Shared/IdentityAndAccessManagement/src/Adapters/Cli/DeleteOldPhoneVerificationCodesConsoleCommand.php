<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Cli;

use Illuminate\Console\Command;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\DeleteOldPhoneVerificationCodes;

class DeleteOldPhoneVerificationCodesConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idm:delete-old-phone-verification-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old phone verification code records';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DeleteOldPhoneVerificationCodes::make()->handle();
    }
}
