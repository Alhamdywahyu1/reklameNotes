<?php

namespace App\Mail;

use App\Models\PermohonanReklame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuratDiprintMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanReklame $permohonan,
        public string $operatorName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Surat Persetujuan Reklame Anda Telah Siap - {$this->permohonan->nomor_registrasi}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.surat_diprint',
            with: [
                'permohonan' => $this->permohonan,
                'operatorName' => $this->operatorName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
