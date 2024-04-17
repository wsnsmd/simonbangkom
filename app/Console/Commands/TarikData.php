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
            $headers = [
                'Authorization' => 'Bearer ' . env('SIMASN_BEARER')
            ];

            $request_pns = $client->get(env('SIMASN_JP_ALL_PNS') . '?tahun=' . $tahun, ['headers' => $headers, 'timeout' => 120]);

            if($request_pns->getStatusCode() == 200)
            {
                $result_pns = $request_pns->getBody();
                $json_pns = json_decode($result_pns, true);
                $created_at = now();

                $data_pns = $json_pns['data']['pegawai'];
                $pns = [];

                foreach($data_pns as $p)
                {
                    // $buffer = $p;
                    $buffer['nip_baru'] = $p['nip'];
                    $buffer['nip_lama'] = $p['nip'];
                    $buffer['nama'] = $p['nama'];
                    $buffer['glr_depan'] = rtrim($p['gelar_depan']);
                    $buffer['glr_belakang'] = rtrim($p['gelar_belakang']);
                    $buffer['jabatan'] = rtrim($p['jabatan']);
                    $buffer['opd'] = rtrim($p['opd']);
                    $buffer['bidang'] = rtrim($p['bidang']);
                    $buffer['subbidang'] = rtrim($p['subbidang']);
                    $buffer['subunor'] = rtrim($p['subunor']);
                    $buffer['total_jp'] = $p['total_jp'];
                    $buffer['tahun'] = $tahun;
                    array_push($pns, $buffer);
                }

                Bangkom::where('tahun', $tahun)->delete();
                foreach (array_chunk($pns, 1000) as $t)
                {
                    Bangkom::insert($t);
                }
                Bangkom::where('tahun', $tahun)->update(['created_at' => $created_at]);

                $peda = Bangkom::select('opd')->where('tahun', $tahun)->orderBy('opd')->groupBy('opd')->get();

                Jppd::where('tahun', $tahun)->delete();
                $i = 1;

                foreach($peda as $p)
                {
                    $jum_pegawai = Bangkom::where('opd', $p->opd)->where('tahun', $tahun)->count();
                    $total_jp = Bangkom::where('opd', $p->opd)->where('tahun', $tahun)->sum('total_jp');
                    if($jum_pegawai > 0)
                        $rata_jp = $total_jp / $jum_pegawai;
                    else
                        $rata_jp = 0;
                    $data_pd = new Jppd;
                    $data_pd->id_skpd = $i++;
                    $data_pd->lokasi = rtrim($p->opd);
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
