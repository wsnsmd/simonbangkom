<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use GuzzleHttp\Client;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        Commands\TarikData::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('token:simasn');
        $schedule->command('tarik:data')
                ->onSuccess(function () {
                    $client = new Client();
                    $client->post('https://wa.srv3.wapanels.com/send-message', [
                        'form_params' => [
                            'api_key' => env('WA_API_KEY'),
                            'sender' => env('WA_SENDER'),
                            'number' => env('WA_REPORT_NUMBER'),
                            'message' => 'Tarik Data SiMon Bangkom Sukses!'
                        ]
                        ]);
                })
                ->onFailure(function () {
                    $client = new Client();
                    $client->post('https://wa.srv3.wapanels.com/send-message', [
                        'form_params' => [
                            'api_key' => env('WA_API_KEY'),
                            'sender' => env('WA_SENDER'),
                            'number' => env('WA_REPORT_NUMBER'),
                            'message' => 'Tarik Data SiMon Bangkom Gagal!'
                        ]
                        ]);
                });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
