<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LahirImport extends BaseController
{
	public function index()
	{
        $data = [
            'title'         => 'Data Kelahiran Import',
        ];
        return view('panel/lahir/import/index', $data);
	}

    public function import()
    {
        // Get the uploaded Excel file
        $file = $this->request->getFile('excel_file');
        // Load the spreadsheet from the file
        $spreadsheet = IOFactory::load($file->getTempName());
        // Get the first worksheet
        $worksheet = $spreadsheet->getActiveSheet();
        // Get all data rows from the worksheet
        $dataFile = [];
        $startRow = 2; // Start from row 2
        foreach ($worksheet->getRowIterator($startRow) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $dataFile[] = $rowData;
        }
        // Pass the data to the view
        $data = [
            'title'         => 'Data Kelahiran Import',
            'preview_data'  => $dataFile
        ];
        // Display the preview view
        return view('panel/lahir/import/preview', $data);
    }

    public function save()
    {
        $requestData = $this->request->getPost('data');
        $data = json_decode($requestData, true);
        foreach ($data as $row) {
            $tgl_entri      = $row['column_1'];
            $kecamatan      = $row['column_2'];
            $kelurahan      = $row['column_3'];
            $akta           = $row['column_4'];
            $kk             = $row['column_5'];
            $nik            = $row['column_6'];
            $nama           = $row['column_7'];
            $tempat_lahir   = $row['column_8'];
            $tgl_lahir      = $row['column_9'];
            $kelamin        = $row['column_10'];
            $kategori       = $row['column_11'];
            $this->lahir->insert([
                'tgl_entri'     => $tgl_entri,
                'kecamatan'     => strtoupper($kecamatan),
                'kelurahan'     => strtoupper($kelurahan),
                'akta'          => str_replace(' ', '', strtoupper($akta)),
                'kk'            => str_replace(' ', '', strtoupper($kk)),
                'nik'           => str_replace(' ', '', strtoupper($nik)),
                'nama'          => strtoupper($nama),
                'tempat_lahir'  => strtoupper($tempat_lahir),
                'tgl_lahir'     => $tgl_lahir,
                'kelamin'       => str_replace(' ', '', strtoupper($kelamin)),
                'kategori'      => str_replace(' ', '', strtoupper($kategori)),
                'created'       => date('Y-m-d H:i:s')
            ]);
        }
        $response = [
            'success' => true,
            'code'    => '200',
            'message' => 'Data Berhasil Diimport Berjumlah '.count($data),
            'redirect' => '/lahir-import'
        ];
        return $this->response->setJSON($response);
    }
}