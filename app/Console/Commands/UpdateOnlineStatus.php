<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateOnlineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:update-online-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update device online status based on last heartbeat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) Setting::get('heartbeat_interval', 300);
        $threshold = Carbon::now()->subSeconds($interval * 2);

        $offlineCount = Device::where('is_online', true)
            ->where(function ($query) use ($threshold) {
                $query->whereNull('last_online')
                      ->orWhere('last_online', '<', $threshold);
            })
            ->update(['is_online' => false]);

        $this->info("Updated {$offlineCount} devices to offline status.");
        return Command::SUCCESS;
    }
}
