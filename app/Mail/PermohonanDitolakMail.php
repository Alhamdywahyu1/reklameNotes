<?php

namespace App\Mail;

use App\Models\PermohonanReklame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanDitolakMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public PermohonanReklame $permohonan,
        public string $keterangan = ''
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Reklame Anda Ditolak - ' . $this->permohonan->nomor_registrasi,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan-ditolak',
            with: [
                'permohonan' => $this->permohonan,
                'nama_pemohon' => $this->permohonan->nama_pemohon,
                'nomor_registrasi' => $this->permohonan->nomor_registrasi,
                'jenis_reklame' => $this->permohonan->jenis_reklame,
                'lokasi_pemasangan' => $this->permohonan->lokasi_pemasangan,
                'keterangan' => $this->keterangan,
                'tanggal_penolakan' => now()->format('d F Y'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
