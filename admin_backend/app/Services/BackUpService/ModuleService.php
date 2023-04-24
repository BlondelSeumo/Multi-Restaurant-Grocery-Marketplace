<?php

namespace App\Services\BackUpService;

use App\Models\User;
use App\Services\CoreService;
use Artisan;
use File;
use Symfony\Component\Process\Process;
use ZipArchive;

class ModuleService extends CoreService
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    public function unZip($file): array
    {
        $zip = new ZipArchive;
        $zip->open($file->getRealPath());

        $oldPath = 'app/public/unzip/booking';

        $storageDestinationPath = storage_path($oldPath);

        if (!File::exists($storageDestinationPath)) {
            File::makeDirectory($storageDestinationPath, 0755, true);
        }

        $zip->extractTo($storageDestinationPath);

        File::move(storage_path("$oldPath/booking.php"), base_path("routes/booking.php"));

        $this->moveApp($oldPath, 'cntrl_admin_booking', 'app/Http/Controllers/API/v1/Dashboard/Admin');
        $this->moveApp($oldPath, 'migrate_booking', 'database/migrations', 'database/migrations/booking');
        $this->moveApp($oldPath, 'cntrl_seller_booking', 'app/Http/Controllers/API/v1/Dashboard/Seller');
        $this->moveApp($oldPath, 'cntrl_user_booking', 'app/Http/Controllers/API/v1/Dashboard/User');
        $this->moveApp($oldPath, 'model_booking', 'app/Models');
        $this->moveApp($oldPath, 'repository_booking', 'app/Repositories');
        $this->moveApp($oldPath, 'request_booking', 'app/Http/Requests');
        $this->moveApp($oldPath, 'resource_booking', 'app/Http/Resources');
        $this->moveApp($oldPath, 'service_booking', 'app/Services');
        $zip->close();

        Artisan::call('migrate --path=database/migrations/booking/');
        Artisan::call('clear-compiled');
        $success = Process::fromShellCommandline("cd ". base_path() . " && composer dump-autoload");
        $success->run();
        Artisan::call('optimize:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');

        return [
            'status' => true,
            'data' => [
                $success->getErrorOutput(),
                $success->getExitCode(),
                $success->isSuccessful(),
                $success->getExitCodeText()
            ],
        ];
    }

    /**
     * @param string $oldPath
     * @param string $name
     * @param string $folder
     * @param string|null $custom
     * @return void
     */
    public function moveApp(string $oldPath, string $name, string $folder, string|null $custom = null): void
    {
       File::moveDirectory(storage_path("$oldPath/$name"), base_path(!empty($custom) ? $custom : "$folder/Booking"), true);
    }
}
