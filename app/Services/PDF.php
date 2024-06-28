<?php

namespace App\Services;

use TCPDF;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PDF extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
        $this->SetCreator('Your Name');
        $this->SetTitle('HISTORIAL_CLIENTE');
        $this->SetMargins(15, 15, 15);
    }

    public function addContent($html)
    {
        $this->SetFont('helvetica', '', 10);
        $this->AddPage();
        $this->writeHTML($html, true, false, true, false, '');
    }

    public function addQrCode($text, $x = 93, $y = 160, $w = 25, $h = 25)
    {
        // Generar el código QR
        $qrCode = QrCode::create($text)
            ->setSize(150);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Guardar la imagen temporalmente
        $qrImagePath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
        $result->saveToFile($qrImagePath);

        // Agregar la imagen del código QR al PDF
        $this->Image($qrImagePath, $x, $y, $w, $h, 'PNG', '', '', true);

        // Eliminar la imagen temporal
        unlink($qrImagePath);
    }

    public function download($filename)
    {
        $this->Output($filename, 'D');
    }
}

