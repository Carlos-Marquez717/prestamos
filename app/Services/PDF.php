<?php

namespace App\Services;

use TCPDF;

class PDF extends TCPDF
{
    // Constructor
    public function __construct()
    {
        parent::__construct();
        // Configura el PDF aquí si es necesario
        $this->SetCreator('Your Name');
        $this->SetTitle('HISTORIAL_CLIENTE');
        $this->SetMargins(15, 15, 15);
    }

    // Método para agregar contenido al PDF
    public function addContent($html)
    {
        // Añade contenido al PDF
        $this->SetFont('helvetica', '', 10);
        $this->AddPage();
        $this->writeHTML($html, true, false, true, false, '');
    }

    // Método para descargar el PDF
    public function download($filename)
    {
        // Descarga el PDF al navegador
        $this->Output($filename, 'D');
    }

    // Otros métodos personalizados según tus necesidades
}
