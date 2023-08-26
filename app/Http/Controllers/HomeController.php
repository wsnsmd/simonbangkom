<?php

namespace App\Http\Controllers;

use App\Models\Jppd;
use App\Models\Bangkom;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Yajra\DataTables\DataTables;
use DB;

// use DataTables;
// use Yajra\DataTables\DataTables as DataTablesDataTables;

class HomeController extends Controller
{
    /**
     * Analytic Dashboard
     */
    public function index()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Home',
                'url' => '/',
                'active' => true
            ],
        ];

        $pd = Jppd::where('tahun', $this->tahun)->orderBy('rata_rata_jp', 'desc')->get();
        $tgl = $pd->first()->created_at;
        $lokasi = [];
        $jp_rata = [];
        $warna = [];
        $total_pns = Jppd::where('tahun', $this->tahun)->sum('jumlah_pegawai');
        $average_jp = Jppd::where('tahun', $this->tahun)->avg('rata_rata_jp');
        foreach($pd as $item)
        {
            array_push($lokasi, $item->lokasi);
            array_push($jp_rata, $item->rata_rata_jp);
            array_push($warna, $this->rand_color());
        }

        return view('dashboard', [
            'pageTitle' => 'Dashboard',
            'breadcrumbItems' => $breadcrumbsItems,
            'pd' => $pd,
            'lokasi' => $lokasi,
            'jp_rata' => $jp_rata,
            'warna' => $warna,
            'average_jp' => $average_jp,
            'total_pns' => $total_pns,
            'tgl' => $tgl,
        ]);
    }

    public function refresh(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $client = new Client(['http_errors' => false, 'verify' => false]);

            $request_pd = $client->get(env('SIMPEG_JP_ALLPD') . $this->tahun . '?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
            $request_pns = $client->get(env('SIMPEG_JP_ALL_PNS') . $this->tahun . '?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
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
                    $buffer['tahun'] = $this->tahun;
                    array_push($pns, $buffer);
                }

                Jppd::where('tahun', $this->tahun)->delete();
                Jppd::insert($pd);
                Jppd::where('tahun', $this->tahun)->update(['created_at' => $created_at]);

                Bangkom::where('tahun', $this->tahun)->delete();
                foreach (array_chunk($pns, 1000) as $t)
                {
                    Bangkom::insert($t);
                }
                Bangkom::where('tahun', $this->tahun)->update(['created_at' => $created_at]);

                DB::commit();

                return redirect()->route('dashboard.index');
            }
        }
        catch(RequestException $ex)
        {
            DB::rollback();
            $notifikasi = 'Terjadi kesalahan, silahkan diulang kembali!';
            return $notifikasi;
        }
    }

    public function detail($id)
    {
        $pd = Jppd::where('id_skpd', $id)->where('tahun', $this->tahun)->first();
        $pegawai = Bangkom::where('tahun', $this->tahun)->where('opd', $pd->lokasi)->get();

        $breadcrumbsItems = [
            [
                'name' => 'Detail',
                'url' => '#',
                'active' => true
            ],
        ];

        return view('dashboard_detail', [
            'pageTitle' => 'Detail',
            'breadcrumbItems' => $breadcrumbsItems,
            'pd' => $pd,
            'pegawai' => $pegawai
        ]);

    }

    public function showPegawai($nip)
    {
        try
        {
            $client = new Client(['http_errors' => false, 'verify' => false]);
            $req_pegawai = $client->get(env('SIMPEG_PNS') . $nip . '/?api_token=' . env('SIMPEG_KEY'));

            if($req_pegawai->getStatusCode() == 200)
            {
                $res_pegawai = $req_pegawai->getBody();
                $data_pegawai = json_decode($res_pegawai, true);

                if($data_pegawai['status']['kode'] != 200)
                    return redirect()->back()->with('error', $data_pegawai['keterangan']);

                // dd($data_pegawai['nama']);
                // https://api-simpeg.kaltimbkd.info/pns/rekap-diklat-pegawai/{TAHUN}/{NIPPEGAWAI}/?api_token=TOKEN&page=1
                $req_bangkom = $client->get(env('SIMPEG_JP_PNS'). $this->tahun . '/' . $nip . '/?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
                if($req_bangkom->getStatusCode() == 200)
                {
                    $res_bangkom = $req_bangkom->getBody();
                    $json_bangkom = json_decode($res_bangkom, true);
                    $bangkom = $json_bangkom['data'];
                }
                return view('dashboard_pegawai', compact('data_pegawai', 'bangkom'));
            }
        }
        catch(RequestException $ex)
        {
            $notifikasi = 'Terjadi kesalahan, silahkan diulang kembali!';
            return $notifikasi;
        }
    }

    public function getPegawai(Request $request, $id)
    {
        $current_page = ($request->start / $request->length) + 1 ?? 1;
        try
        {
            $url = env('SIMPEG_JP_PD') . $this->tahun . '/' . $id . '/?page='.$current_page.'&api_token=' . env('SIMPEG_KEY');
            $client = new Client(['http_errors' => false, 'verify' => false]);
            // https://api-simpeg.kaltimbkd.info/pns/rekap-diklat-opd/{TAHUN}/{IDSKPD}/?api_token=TOKEN
            $request = $client->get($url, ['timeout' => 120]);
            if($request->getStatusCode() == 200)
            {
                $result = $request->getBody();
                $decode = json_decode($result, true);
                $data = $decode['data'];
                $tables = DataTables::of($data['pegawai'])
                            ->addColumn('nama_lengkap', function($p) {
                                return $p['glr_depan'].$p['nama'].' '.$p['glr_belakang'] ;
                            })
                            ->addColumn('aksi', function($p) {
                                $button = '<div class="action-btns space-x-2 flex">
                                                <a class="action-btn shift-Away" href="'.route('dashboard.pegawai', $p['nip_baru']).'" data-tippy-content="Detail" data-tippy-theme="dark">
                                                    <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                </a>
                                            </div>';
                                return $button;
                            })
                            ->editColumn('total_jp', function($p) {
                                if($p['total_jp'] >= 20)
                                    return '<span class="badge bg-primary-500 text-white capitalize">'.$p['total_jp'].'</span>';
                                else
                                    return '<span class="badge bg-danger-500 text-white capitalize">'.$p['total_jp'].'</span>';
                            })
                            ->setTotalRecords($data['total_data'])
                            ->setFilteredRecords($data['total_data'])
                            ->skipPaging()
                            ->rawColumns(['aksi', 'total_jp'])
                            ->make(true);
                return $tables;
            }
        }
        catch(RequestException $ex)
        {
            $notifikasi = 'Terjadi kesalahan, silahkan diulang kembali!';
            response()->json($notifikasi);
        }
    }
}
