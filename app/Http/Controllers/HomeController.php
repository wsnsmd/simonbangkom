<?php

namespace App\Http\Controllers;

use App\Models\Jppd;
use App\Models\Bangkom;
use App\Models\ViewJppd;
use App\Exports\BangkomExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Excel;

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

        $grafik1['lokasi'] = [];
        $grafik1['jp_rata'] = [];
        $grafik1['warna'] = [];
        $grafik2['lokasi'] = [];
        $grafik2['persentase'] = [];
        $grafik2['warna'] = [];

        if(!Auth::user()->hasRole(['super-admin', 'admin']))
        {
            $opd = Auth::user()->lokasi;
            $pd = ViewJppd::where('lokasi', $opd)->where('tahun', $this->tahun)->orderBy('rata_rata_jp', 'desc')->get();
            $pd2 = ViewJppd::where('lokasi', $opd)->where('tahun', $this->tahun)->orderBy('persentase', 'desc')->get();
            $total_pns = Jppd::where('lokasi', $opd)->where('tahun', $this->tahun)->sum('jumlah_pegawai');
            $average_jp = Jppd::where('lokasi', $opd)->where('tahun', $this->tahun)->avg('rata_rata_jp');
            $pns_gt_20 = ViewJppd::where('lokasi', $opd)->where('tahun', $this->tahun)->sum('jp_lebih_20');
            $pns_lt_20 = ViewJppd::where('lokasi', $opd)->where('tahun', $this->tahun)->sum('jp_kurang_20');
        }
        else
        {
            // $pd = Jppd::where('tahun', $this->tahun)->orderBy('rata_rata_jp', 'desc')->get();
            $pd = ViewJppd::where('tahun', $this->tahun)->orderBy('rata_rata_jp', 'desc')->get();
            $pd2 = ViewJppd::where('tahun', $this->tahun)->orderBy('persentase', 'desc')->get();
            $total_pns = Jppd::where('tahun', $this->tahun)->sum('jumlah_pegawai');
            $average_jp = Jppd::where('tahun', $this->tahun)->avg('rata_rata_jp');
            $pns_gt_20 = ViewJppd::where('tahun', $this->tahun)->sum('jp_lebih_20');
            $pns_lt_20 = ViewJppd::where('tahun', $this->tahun)->sum('jp_kurang_20');
        }

        foreach($pd as $item)
        {
            array_push($grafik1['lokasi'], $item->lokasi);
            array_push($grafik1['jp_rata'], $item->rata_rata_jp);
            array_push($grafik1['warna'], $this->rand_color());
        }

        foreach($pd2 as $item)
        {
            array_push($grafik2['lokasi'], $item->lokasi);
            array_push($grafik2['persentase'], round($item->persentase, 2));
            array_push($grafik2['warna'], $this->rand_color());
        }

        $tgl = $pd->first()->created_at;

        return view('dashboard', [
            'pageTitle' => 'Dashboard',
            'breadcrumbItems' => $breadcrumbsItems,
            'pd' => $pd,
            'pd2' => $pd2,
            'average_jp' => $average_jp,
            'total_pns' => $total_pns,
            'tgl' => $tgl,
            'pns_gt_20' => $pns_gt_20,
            'pns_lt_20' => $pns_lt_20,
            'grafik1' => $grafik1,
            'grafik2' => $grafik2,
        ]);
    }

    public function refresh(Request $request)
    {
        if(!Auth::user()->hasRole(['super-admin', 'admin']))
            abort(404);

        try
        {
            DB::beginTransaction();
            $client = new Client(['http_errors' => false, 'verify' => false]);

            $request_pns = $client->get(env('SIMPEG_JP_ALL_PNS') . $this->tahun . '?api_token=' . env('SIMPEG_KEY'), ['timeout' => 120]);
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
                    $buffer['glr_depan'] = rtrim($p['glr_depan']);
                    $buffer['glr_belakang'] = rtrim($p['glr_belakang']);
                    $buffer['jabatan'] = rtrim($p['jabatan']);
                    $buffer['opd'] = rtrim($p['opd']);
                    $buffer['tahun'] = $this->tahun;
                    array_push($pns, $buffer);
                }

                Bangkom::where('tahun', $this->tahun)->delete();
                foreach (array_chunk($pns, 1000) as $t)
                {
                    Bangkom::insert($t);
                }
                Bangkom::where('tahun', $this->tahun)->update(['created_at' => $created_at]);

                $peda = Bangkom::select('opd')->orderBy('opd')->groupBy('opd')->get();

                Jppd::where('tahun', $this->tahun)->delete();
                $i = 1;

                foreach($peda as $p)
                {
                    $jum_pegawai = Bangkom::where('opd', $p->opd)->where('tahun', $this->tahun)->count();
                    $total_jp = Bangkom::where('opd', $p->opd)->where('tahun', $this->tahun)->sum('total_jp');
                    $rata_jp = $total_jp / $jum_pegawai;
                    $data_pd = new Jppd;
                    $data_pd->id_skpd = $i++;
                    $data_pd->lokasi = rtrim($p->opd);
                    $data_pd->tahun = $this->tahun;
                    $data_pd->jumlah_pegawai = $jum_pegawai;
                    $data_pd->total_jp = $total_jp;
                    $data_pd->rata_rata_jp = $rata_jp;
                    $data_pd->save();
                }

                Jppd::where('tahun', $this->tahun)->update(['created_at' => $created_at]);

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
        if(!Auth::user()->hasRole(['super-admin', 'admin']))
        {
            if((Auth::user()->lokasi != $pd->lokasi))
                abort(404);
        }

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
                if(!Auth::user()->hasRole(['super-admin', 'admin']))
                {
                    if((Auth::user()->lokasi != rtrim($data_pegawai['skpd'])))
                        abort(404);
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

    public function exportData(Request $request)
    {
        $pd = ViewJppd::where('tahun', $request->tahun)->orderBy('persentase', 'desc')->get();
        $styleArray = array(
            'font'  => array(
                 'size'  => 11,
                 'name'  => 'Arial'
             ));

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('BPSDM Prov. Kaltim')->setLastModifiedBy('BPSDM Prov. Kaltim');
        $spreadsheet->getDefaultStyle()->applyFromArray($styleArray);
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'REKAPITULASI PERANGKAT DAERAH');
        $activeWorksheet->setCellValue('A3', 'Update terakhir: ');
        $activeWorksheet->setCellValue('A5', '#');
        $activeWorksheet->setCellValue('B5', 'PERANGKAT DAERAH');
        $activeWorksheet->setCellValue('C5', 'JUMLAH PEGAWAI');
        $activeWorksheet->setCellValue('D5', 'JUMLAH PEGAWAI JP >= 20');
        $activeWorksheet->setCellValue('E5', 'JUMLAH PEGAWAI JP < 20');
        $activeWorksheet->setCellValue('F5', 'PERSENTASE');
        $activeWorksheet->setCellValue('G5', 'TOTAL JP');
        $activeWorksheet->setCellValue('H5', 'RATA-RATA JP');

        $activeWorksheet->mergeCells('A1:H1');
        $activeWorksheet->mergeCells('A3:H3');
        $activeWorksheet->getStyle('A1')->getFont()->setBold(true);
        $activeWorksheet->getStyle('A5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('B5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('C5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('D5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('E5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('F5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('G5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('H5')->getFont()->setBold(true);
        $activeWorksheet->getColumnDimension('A')->setWidth(4);
        $activeWorksheet->getColumnDimension('B')->setWidth(80);
        $activeWorksheet->getColumnDimension('C')->setWidth(20);
        $activeWorksheet->getColumnDimension('D')->setWidth(20);
        $activeWorksheet->getColumnDimension('E')->setWidth(20);
        $activeWorksheet->getColumnDimension('F')->setWidth(20);
        $activeWorksheet->getColumnDimension('G')->setWidth(20);
        $activeWorksheet->getColumnDimension('H')->setWidth(20);
        $activeWorksheet->getRowDimension(5)->setRowHeight(35);
        $activeWorksheet->getStyle('A5:H5')->getAlignment()->setWrapText(true);

        $no = 1;
        $row = 6;

        foreach($pd as $p)
        {
            $activeWorksheet->setCellValue('A'.$row, $no++);
            $activeWorksheet->setCellValue('B'.$row, $p->lokasi);
            $activeWorksheet->setCellValue('C'.$row, $p->jumlah_pegawai);
            $activeWorksheet->setCellValue('D'.$row, $p->jp_lebih_20);
            $activeWorksheet->setCellValue('E'.$row, $p->jp_kurang_20);
            $activeWorksheet->setCellValue('F'.$row, round($p->persentase, 2));
            $activeWorksheet->setCellValue('G'.$row, $p->total_jp);
            $activeWorksheet->setCellValue('H'.$row, $p->rata_rata_jp);
            $activeWorksheet->getRowDimension($row++)->setRowHeight(15);
        }

        $activeWorksheet->setCellValue('A3', 'Update terakhir: ' . $p->created_at);
        $activeWorksheet->getStyle('A3')->getFont()->setItalic(true);

        $activeWorksheet->getStyle('A5:H'.$row-1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path = storage_path('simonbangkom-rekap-'. time() . '.xlsx'));
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportOpd(Request $request)
    {
        $opd = $request->opd;
        $tahun = $request->tahun;

        $pegawai = Bangkom::where('tahun', $tahun)->where('opd', $opd)->get();

        $styleArray = array(
            'font'  => array(
                 'size'  => 11,
                 'name'  => 'Arial'
             ));

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator($opd)->setLastModifiedBy($opd);
        $spreadsheet->getDefaultStyle()->applyFromArray($styleArray);
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', $opd);
        $activeWorksheet->setCellValue('A3', 'Update terakhir: ');
        $activeWorksheet->setCellValue('A5', '#');
        $activeWorksheet->setCellValue('B5', 'NIP');
        $activeWorksheet->setCellValue('C5', 'NAMA');
        $activeWorksheet->setCellValue('D5', 'JABATAN');
        $activeWorksheet->setCellValue('E5', 'BIDANG');
        $activeWorksheet->setCellValue('F5', 'SUBBIDANG');
        $activeWorksheet->setCellValue('G5', 'TOTAL JP');

        $activeWorksheet->mergeCells('A1:E1');
        $activeWorksheet->mergeCells('A3:E3');
        $activeWorksheet->getStyle('A1')->getFont()->setBold(true);
        $activeWorksheet->getStyle('A5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('B5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('C5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('D5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('E5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('F5')->getFont()->setBold(true);
        $activeWorksheet->getStyle('G5')->getFont()->setBold(true);
        $activeWorksheet->getColumnDimension('A')->setWidth(4);
        $activeWorksheet->getColumnDimension('B')->setWidth(20);
        $activeWorksheet->getColumnDimension('C')->setWidth(35);
        $activeWorksheet->getColumnDimension('D')->setWidth(70);
        $activeWorksheet->getColumnDimension('E')->setWidth(50);
        $activeWorksheet->getColumnDimension('F')->setWidth(50);
        $activeWorksheet->getColumnDimension('G')->setWidth(10);
        $activeWorksheet->getRowDimension(5)->setRowHeight(25);

        $no = 1;
        $row = 6;
        foreach($pegawai as $p)
        {
            $nama = (empty($p->glr_depan) ? '' : trim($p->glr_depan)) . trim($p->nama) . (!empty($p->glr_belakang) ? ', ' . $p->glr_belakang : '');
            $activeWorksheet->setCellValue('A'.$row, $no++);
            $activeWorksheet->setCellValueExplicit('B'.$row, $p->nip_baru, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeWorksheet->setCellValue('C'.$row, $nama);
            $activeWorksheet->setCellValue('D'.$row, strtoupper($p->jabatan));
            $activeWorksheet->setCellValue('E'.$row, $p->bidang);
            $activeWorksheet->setCellValue('F'.$row, $p->subbidang);
            $activeWorksheet->setCellValue('G'.$row, $p->total_jp);
            $activeWorksheet->getRowDimension($row++)->setRowHeight(15);
        }
        $activeWorksheet->setCellValue('A3', 'Update terakhir: ' . $p->created_at);
        $activeWorksheet->getStyle('A3')->getFont()->setItalic(true);

        $activeWorksheet->getStyle('A5:G'.$row-1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $writer = new Xlsx($spreadsheet);
        $writer->save($path = storage_path('simonbangkom-opd-'. time() . '.xlsx'));
        return response()->download($path)->deleteFileAfterSend();

        // return Excel::download(new BangkomExport($pegawai), 'test.xlsx');
    }
}
