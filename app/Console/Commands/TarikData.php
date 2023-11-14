<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jppd;
use App\Models\Bangkom;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use DB;

class TarikData extends Command
{
    protected $signature = 'tarik:data';

    protected $description = 'Tarik Data SIMPEG';

    public function handle()
    {
        try
        {
            DB::beginTransaction();
            $tahun = env('APP_TAHUN');
            $client = new Client(['http_errors' => false, 'verify' => false]);

            $request_pd = $client->get(env('SIMPEG_JP_ALLPD') . $tahun . '?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
            $request_pns = $client->get(env('SIMPEG_JP_ALL_PNS') . $tahun . '?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
            if($request_pd->getStatusCode() == 200 && $request_pns->getStatusCode() == 200)
            {
                $result_pd = $request_pd->getBody();
                $result_pns = $request_pns->getBody();
                $pd = json_decode($result_pd, true);
                $json_pns = json_decode($result_pns, true);
                $created_at = now();

                $data_pns = $json_pns['data']['pegawai'];
                $pns = [];

                foreach($data_pns as $p)
                {
                    $buffer = $p;
                    $buffer['tahun'] = $tahun;
                    array_push($pns, $buffer);
                }

                Jppd::where('tahun', $tahun)->delete();
                Jppd::insert($pd);
                Jppd::where('tahun', $tahun)->update(['created_at' => $created_at]);

                Bangkom::where('tahun', $tahun)->delete();
                foreach (array_chunk($pns, 1000) as $t)
                {
                    Bangkom::insert($t);
                }
                Bangkom::where('tahun', $tahun)->update(['created_at' => $created_at]);

                DB::commit();

                \Log::info('Tarik Data Berhasil!');
            }
        }
        catch(RequestException $ex)
        {
            DB::rollback();
            \Log::info('Tarik Data Gagal!');
        }
    }
}
