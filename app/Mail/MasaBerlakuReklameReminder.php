<?php

namespace App\Mail;

use App\Models\PermohonanReklame;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MasaBerlakuReklameReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanReklame $permohonan,
        public int $sisaHari
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Masa Berlaku Reklame Akan Berakhir ' . $this->sisaHari . ' Hari Lagi - ' . $this->permohonan->nomor_registrasi,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder-masa-berlaku-reklame',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
