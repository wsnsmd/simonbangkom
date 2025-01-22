<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;

use App\Models\ApiToken;

class BearerTokenSimAsnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:simasn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek Bearer Token SIMASN';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking SIMASN bearer token...');
        $tokenData = ApiToken::where('app_name', '=', 'SIMASN')->first();
        $now = Carbon::now();

        // Periksa apakah token ada dan valid
        if (!$tokenData || !$tokenData->expires_at || $now->greaterThanOrEqualTo(Carbon::parse($tokenData->expires_at))) {
            $this->info('Token is missing or expired. Requesting a new token...');

            $client = new Client(['verify' => false]);
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];
            $body = json_encode([
                "username" => env('SIMASN_USERNAME'),
                "password" => env('SIMASN_PASSWORD')
            ]);

            $request = new \GuzzleHttp\Psr7\Request('POST', env('SIMASN_LOGIN'), $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $body = json_decode($res->getBody());

            if ($body->success) {
                // Simpan token baru ke database
                ApiToken::updateOrCreate([
                    'app_name' => 'SIMASN',
                ],
                [
                    'token' => $body->access_token,
                    'type' => 0,
                    'expires_at' => $body->expires_at,
                ]);

                $this->info('New token retrieved and saved successfully.');
            } else {
                // Log error jika gagal mendapatkan token baru
                $this->error('Failed to retrieve new token. Response: ' . $res->body());
            }
        } else {
            $this->info('Token is still valid. No action needed.');
        }
    }
}
