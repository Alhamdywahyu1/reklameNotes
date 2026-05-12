<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PermohonanReklame;

class PermohonanReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PermohonanReklame $permohonan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat: Permohonan Reklame Menunggu Verifikasi - ' . $this->permohonan->nomor_registrasi,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder-pending-permohonan',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
