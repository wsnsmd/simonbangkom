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

            $request_pns = $client->get(env('SIMPEG_JP_ALL_PNS') . $tahun . '?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
            if($request_pns->getStatusCode() == 200)
            {
                $result_pns = $request_pns->getBody();
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

                Bangkom::where('tahun', $tahun)->delete();
                foreach (array_chunk($pns, 1000) as $t)
                {
                    Bangkom::insert($t);
                }
                Bangkom::where('tahun', $tahun)->update(['created_at' => $created_at]);

                $peda = Bangkom::select('opd')->orderBy('opd')->groupBy('opd')->get();

                Jppd::where('tahun', $tahun)->delete();
                $i = 1;

                foreach($peda as $p)
                {
                    $jum_pegawai = Bangkom::where('opd', $p->opd)->where('tahun', $tahun)->count();
                    $total_jp = Bangkom::where('opd', $p->opd)->where('tahun', $tahun)->sum('total_jp');
                    $rata_jp = $total_jp / $jum_pegawai;
                    $data_pd = new Jppd;
                    $data_pd->id_skpd = $i++;
                    $data_pd->lokasi = $p->opd;
                    $data_pd->tahun = $tahun;
                    $data_pd->jumlah_pegawai = $jum_pegawai;
                    $data_pd->total_jp = $total_jp;
                    $data_pd->rata_rata_jp = $rata_jp;
                    $data_pd->save();
                }

                Jppd::where('tahun', $tahun)->update(['created_at' => $created_at]);

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
