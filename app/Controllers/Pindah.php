<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class Pindah extends BaseController
{
    /*--- FRONT ---*/
	public function index()
	{
        $uri            = new \CodeIgniter\HTTP\URI(current_url(true));
        $queryString    = $uri->getQuery();
        $params         = [];
        parse_str($queryString, $params);

        if (count($params) == 4 && array_key_exists('bulan', $params) && array_key_exists('tahun', $params) && array_key_exists('kecamatan', $params) && array_key_exists('kelurahan', $params)) {
            $modul           = 'Filter';
            $bulan           = $params['bulan'];
            $tahun           = $params['tahun'];
            $kecamatan       = $params['kecamatan'];
            $kelurahan       = $params['kelurahan'];

            // $list_bulan      = $this->list_bulan('lahir');
            // $list_tahun      = $this->list_tahun('lahir');
            // $list_kecamatan  = $this->kecamatan->list();
            // $list_kelurahan  = $this->kelurahan->list_only_kelurahan();

            // array_unshift($list_bulan, ['month_number' => 'all', 'month_name' => 'all']);
            // array_unshift($list_tahun, ['year' => 'all']);
            // array_unshift($list_kecamatan, ['idc' => 'all']);

            // if (!in_array($bulan, $list_bulan) ||  !in_array($tahun, $list_tahun) || !in_array($kecamatan, $list_kecamatan) || !in_array($kelurahan, $list_kelurahan) ) {
            //     $modul           = '';
            //     $bulan           = NULL;
            //     $tahun           = NULL;
            //     $kecamatan       = NULL;
            //     $kelurahan       = NULL;
            // }
        } else {
            $modul           = '';
            $bulan           = NULL;
            $tahun           = NULL;
            $kecamatan       = NULL;
            $kelurahan       = NULL;
        }

        $list_bulan     = $this->list_bulan('pindah');
        $list_tahun     = $this->list_tahun('pindah');
        $list_kecamatan = $this->kecamatan->list();
        $list_kelurahan = $this->kelurahan->list();

        $data = [
            'title'         => 'Data Pindah',
            'modul'	        => $modul,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'kecamatan'     => $kecamatan,
            'kelurahan'     => $kelurahan,
            'list_bulan'    => $list_bulan,
            'list_tahun'    => $list_tahun,
            'list_kecamatan'=> $list_kecamatan,
            'list_kelurahan'=> $list_kelurahan,
        ];
        return view('panel/pindah/data/index', $data);
	}

    public function modal()
    {
        if ($this->request->isAJAX()) {

            $id         = $this->request->getVar('id');
            $modal      = $this->request->getVar('modal');
            $pindah     = $this->pindah->find($id);
            $kecamatan  = $this->kecamatan->list();
            $kelurahan  = $this->kelurahan->list();

            $data = [
                'title'     => 'DATA PINDAH '.$pindah['nama'],
                'id'        => $id,
                'modal'     => $modal,
                'kecamatan' => $kecamatan,
                'kelurahan' => $kelurahan,
                'pindah'    => $pindah,
            ];
            $msg = [
                'sukses' => view('panel/pindah/data/modal', $data)
            ];
            echo json_encode($msg);
        }
    }

    /*--- BACK ---*/
    public function filter()
    {
        $bulan      = $this->request->getVar('bulan'); 
        $tahun      = $this->request->getVar('tahun');
        $kecamatan  = $this->request->getVar('kecamatan');
        $kelurahan  = $this->request->getVar('kelurahan');

        $queryParam = 'bulan=' . $bulan . '&tahun=' . $tahun . '&kecamatan=' . $kecamatan . '&kelurahan=' . $kelurahan;

        $newUrl = '/pindah?' . $queryParam; 

        return redirect()->to($newUrl);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $bulan      = $this->request->getVar('bulan'); 
            $tahun      = $this->request->getVar('tahun');
            $kecamatan  = $this->request->getVar('kecamatan');
            $kelurahan  = $this->request->getVar('kelurahan');

            if ($bulan == 'all') {
                $teks_bulan = '';
            }else {
                $teks_bulan = 'BULAN '.$bulan;
            }

            if ($tahun == 'all') {
                $teks_tahun = '';
            }else {
                $teks_tahun = 'TAHUN '.$tahun;
            }

            if ($kecamatan == 'all') {
                $teks_kecamatan = '';
            }else {
                $teks_kecamatan = 'KEC. '.$kecamatan;
            }

            if ($kelurahan == 'all') {
                $teks_kelurahan = '';
            }else {
                $teks_kelurahan = 'KEL. '.$kelurahan;
            }

            $data = [
                'title'         => 'Data Pindah',
                'modul'	        => 'Filter',
                'bulan'         => $bulan,
                'tahun'         => $tahun,
                'kecamatan'     => $kecamatan,
                'kelurahan'     => $kelurahan,
                'teks_bulan'    => $teks_bulan,
                'teks_tahun'    => $teks_tahun,
                'teks_kecamatan'=> $teks_kecamatan,
                'teks_kelurahan'=> $teks_kelurahan,
            ];
            $msg = [
                'data' => view('panel/pindah/data/list', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function fetch()
    {
        if ($this->request->isAJAX()) {
            $bulan      = $this->request->getVar('bulan'); 
            $tahun      = $this->request->getVar('tahun');
            $kecamatan  = $this->request->getVar('kecamatan');
            $kelurahan  = $this->request->getVar('kelurahan');

            $lists 		= $this->pindah->get_datatables($bulan, $tahun, $kecamatan, $kelurahan);
            $data 		= [];
            $no 		= $this->request->getPost('start');

            $role       = session('role');

            foreach ($lists as $list) {
                $no++;

                $btn_info = "<button type=\"button\" class=\"btn btn-sm btn-info mb-2\" onclick=\"info('$list->id')\" ><i class=\" bx bx-info-circle\"></i></button>";
                $btn_edit = "<button type=\"button\" class=\"btn btn-sm btn-warning mb-2\" onclick=\"edit('$list->id')\" ><i class=\" bx bx-edit\"></i></button>";
                $btn_hapus = "<button type=\"button\" class=\"btn btn-sm btn-danger mb-2\" onclick=\"hapus('$list->id','$list->nama')\" ><i class=\" bx bx-trash\"></i></button>";

                if ($role == '707SP') {
                    $row_action = $btn_info.' '.$btn_edit.' '.$btn_hapus;
                } else{
                    $row_action = $btn_info;
                }

                $row = [];

                $row[] = $no;
				$row[] = $list->nik;
                $row[] = $list->nama;
                $row[] = $list->alamat;
				$row[] = shortdate_indo($list->tgl_pindah);
                // $row[] = $list->kecamatan;
                // $row[] = $list->kelurahan;
                $row[] = $row_action;

                $data[] = $row;
            }
            $output = [
                "recordTotal"     => $this->pindah->count_all(),
                "recordsFiltered" => $this->pindah->count_filtered(),
                "data"            => $data,
            ];
            echo json_encode($output);
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $rules = [
                'kk'            => 'required',
                'nik'           => 'required',
                'nama'          => 'required',
                'kelamin'       => 'required',
                'tgl_pindah'    => 'required',
                'skpwni'        => 'required',
                'kecamatan'     => 'required',
                'kelurahan'     => 'required',
                'alamat'        => 'required',
                'tujuan'        => 'required',
            ];
    
            $errors = [
                'skpwni' => [
                    'required'    => 'skpwni harus diisi.',
                ],
                'kecamatan' => [
                    'required'   => 'kecamatan harus dipilih.',
                ],
                'kelurahan' => [
                    'required'   => 'kelurahan harus dipilih.',
                ],
                'kk' => [
                    'required'   => 'kk harus dipilih.',
                ],
                'nik' => [
                    'required'   => 'nik harus dipilih.',
                ],
                'nama' => [
                    'required'   => 'nama harus dipilih.',
                ],
                'tempat_pindah' => [
                    'required'   => 'tempat pindah harus dipilih.',
                ],
                'kelamin' => [
                    'required'   => 'kelamin harus dipilih.',
                ],
                'alamat' => [
                    'required'   => 'alamat harus dipilih.',
                ],
                'tujuan' => [
                    'required'   => 'tujuan harus dipilih.',
                ],
            ];
            $valid = $this->validate($rules, $errors);
            if (!$valid) {
                $response = [
                    'error' => [
                        'skpwni'        => $validation->getError('skpwni'),
                        'kecamatan'     => $validation->getError('kecamatan'),
                        'kelurahan'     => $validation->getError('kelurahan'),
                        'kk'            => $validation->getError('kk'),
                        'nik'           => $validation->getError('nik'),
                        'nama'          => $validation->getError('nama'),
                        'tgl_pindah'    => $validation->getError('tgl_pindah'),
                        'kelamin'       => $validation->getError('kelamin'),
                        'alamat'        => $validation->getError('alamat'),
                        'tujuan'        => $validation->getError('tujuan'),
                    ]
                ];
            } else {
                $id = $this->request->getVar('id');
                $updateData = [
                    'skpwni'            => str_replace(' ', '', strtoupper($this->request->getVar('skpwni'))),
                    'kecamatan'         => strtoupper($this->request->getVar('kecamatan')),
                    'kelurahan'         => strtoupper($this->request->getVar('kelurahan')),
                    'kk'                => str_replace(' ', '', strtoupper($this->request->getVar('kk'))),
                    'nik'               => str_replace(' ', '', strtoupper($this->request->getVar('nik'))),
                    'nama'              => strtoupper($this->request->getVar('nama')),
                    'tgl_pindah'        => $this->request->getVar('tgl_pindah'),
                    'kelamin'           => str_replace(' ', '', strtoupper($this->request->getVar('kelamin'))),
                    'alamat'            => strtoupper($this->request->getVar('alamat')),
                    'tujuan'            => strtoupper($this->request->getVar('tujuan')),
                    'edited'            => date('Y-m-d H:i:s'),
                ];
                $this->pindah->update($id, $updateData);

                $response = [
                    'success' => true,
                    'icon'    => 'success',
                    'message' => 'Data Berhasil Diupdate.',
                ];
            }
            echo json_encode($response);
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {

            $id  = $this->request->getVar('id');
            $this->pindah->delete($id);
                $response = [
                    'success' => true,
                    'icon'    => 'success',
                    'message' => 'Data Berhasil Dihapus.',
                ];
            echo json_encode($response);
        }
    }
}