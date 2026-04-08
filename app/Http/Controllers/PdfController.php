<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // ===== PREVIEW =====
    public function previewSertifikat()
    {
        $data = [
            'nama' => 'Fahmi Rizky',
            'kegiatan' => 'Pelatihan Web Development'
        ];

        return view('pdf.sertifikat', $data);
    }

    public function previewUndangan()
    {
        $data = [
            'nama' => 'Fahmi Rizky',
            'tanggal' => '10 Maret 2026'
        ];

        return view('pdf.undangan', $data);
    }

    // ===== DOWNLOAD =====
    public function downloadSertifikat()
    {
        $data = [
            'nama' => 'Fahmi Rizky',
            'kegiatan' => 'Pelatihan Web Development'
        ];

        $pdf = Pdf::loadView('pdf.sertifikat_pdf', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat.pdf');
    }

    public function downloadUndangan()
    {
        $data = [
            'nama' => 'Fahmi Rizky',
            'tanggal' => '10 Maret 2026'
        ];

        $pdf = Pdf::loadView('pdf.undangan_pdf', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->download('undangan.pdf');
    }
}