<?php

namespace App\Service;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeGeneratorService
{
    public function generateQrCode($content, $qrCodePath)
    {
        $qrCode = new QrCode($content);

        $writer = new PngWriter();
        $data = $writer->write($qrCode);

        file_put_contents($qrCodePath, $data);
    }
}