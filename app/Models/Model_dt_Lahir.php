<?php

namespace App\Models;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class Model_dt_Lahir extends Model
{
    protected $table      = 'tb_data_lahir';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tgl_entri', 'akta', 'kk', 'nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'kelamin', 'kategori', 'kecamatan', 'kelurahan', 'created', 'edited'];

    protected $column_order = array(null, 'nik', 'nama', null);
    protected $column_search = array('nik', 'nama');
    protected $order = array('id' => 'desc');
    protected $request;
    protected $db;
    protected $dt;

    function __construct(RequestInterface $request, $bulan = null,  $tahun = null, $bulanentri = null,  $tahunentri = null, $kecamatan = null , $kelurahan = null)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;

        $this->dt = $this->db->table($this->table)
        ->select('*')
        ->orderBy('id', 'DESC');
    }
    private function _get_datatables_query($bulan = null, $tahun = null, $bulanentri = null,  $tahunentri = null, $kecamatan = null , $kelurahan = null)
    {
        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $_POST['search']('value'));
                } else {
                    $this->dt->orLike($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->dt->groupEnd();
            }
            $i++;
        }

        if ($bulan != 'all') {
            $this->dt->where('EXTRACT(MONTH FROM tgl_lahir)', $bulan);
        }
        if ($tahun != 'all') {
            $this->dt->where('EXTRACT(YEAR FROM tgl_lahir)', $tahun);
        }
        if ($bulanentri != 'all') {
            $this->dt->where('EXTRACT(MONTH FROM tgl_entri)', $bulanentri);
        }
        if ($tahunentri != 'all') {
            $this->dt->where('EXTRACT(YEAR FROM tgl_entri)', $tahunentri);
        }
        if ($kecamatan != 'all') {
            $this->dt->where('kecamatan', $kecamatan);
        }
        if ($kelurahan != 'all') {
            $this->dt->where('kelurahan', $kelurahan);
        }

        if (isset($_POST['order'])) {
            $this->dt->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }
    
    function get_datatables($bulan = null, $tahun = null, $bulanentri = null,  $tahunentri = null, $kecamatan = null , $kelurahan = null)
    {
        $this->_get_datatables_query($bulan, $tahun, $bulanentri, $tahunentri, $kecamatan, $kelurahan);
        if (isset($_POST['length' != -1]))
            $this->dt->limit($_POST['length'], $_POST['start']);
        $query = $this->dt->get();
        return $query->getResult();
    }
    function count_filtered($bulan = null, $tahun = null, $bulanentri = null,  $tahunentri = null, $kecamatan = null , $kelurahan = null)
    {
        $this->_get_datatables_query($bulan, $tahun, $bulanentri, $tahunentri, $kecamatan, $kelurahan);
        return $this->dt->countAllResults();
    }
    public function count_all()
    {
        $tbl_storage = $this->db->table($this->table);
        return $tbl_storage->countAllResults();
    }

    public function total_tahun($year)
    {
        return $this->table('tb_data_lahir')
        ->where('YEAR(tgl_lahir)', $year)
        ->countAllResults();
    }

    public function total()
    {
        return $this->table('tb_data_lahir')
        ->countAllResults();
    }

    public function total_kec_tahun($year, $kec)
    {
        return $this->table('tb_data_lahir')
        ->where('YEAR(tgl_lahir)', $year)
        ->where('kecamatan', $kec)
        ->countAllResults();
    }

    public function total_kec($kec)
    {
        return $this->table('tb_data_lahir')
        ->where('kecamatan', $kec)
        ->countAllResults();
    }

    public function total_kel_tahun($year, $kel)
    {
        return $this->table('tb_data_lahir')
        ->where('YEAR(tgl_lahir)', $year)
        ->where('kelurahan', $kel)
        ->countAllResults();
    }

    public function total_kel($kel)
    {
        return $this->table('tb_data_lahir')
        ->where('kelurahan', $kel)
        ->countAllResults();
    }

    //Dashboard - Grafik
    public function chart_lahir($tahun, $month, $kec, $kelamin)
    {
        return $this->table('tb_data_lahir')
            ->where('YEAR(tgl_lahir)', $tahun)
            ->where('MONTH(tgl_lahir)', $month)
            ->where('kecamatan', $kec)
            ->where('kelamin', $kelamin)
            ->countAllResults();
    }

    public function chart_kel_lahir($tahun, $month, $kel, $kelamin)
    {
        return $this->table('tb_data_lahir')
            ->where('YEAR(tgl_lahir)', $tahun)
            ->where('MONTH(tgl_lahir)', $month)
            ->where('kelurahan', $kel)
            ->where('kelamin', $kelamin)
            ->countAllResults();
    }
}
