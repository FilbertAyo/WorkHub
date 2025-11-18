<?php

namespace App\Console\Commands;

use App\Models\ApprovalLog;
use App\Models\Petty;
use Illuminate\Console\Command;

class FillPettyPaidDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'petty:fill-paid-date';

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
        $updatedCount = 0;

        $petties = Petty::all();

        foreach ($petties as $petty) {
            $approval = ApprovalLog::where('petty_id', $petty->id)
                ->where('action', 'paid')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($approval) {
                $petty->paid_date = $approval->created_at;
                $petty->save();
                $updatedCount++;
            }
        }

        $this->info("Updated {$updatedCount} petty records with paid_date.");
    }
}
