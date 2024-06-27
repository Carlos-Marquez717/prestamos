<?php

namespace App;

use TCPDF;

class PdfGenerator
{
    protected $pdf;

    public function __construct()
    {
        $this->pdf = new TCPDF();
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Your Name');
        $this->pdf->SetTitle('Sample PDF');
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
    }

    public function generate($html)
    {
        $this->pdf->AddPage();
        $this->pdf->writeHTML($html, true, false, true, false, '');

        // You can add more customization here if needed

        return $this->pdf;
    }
}
