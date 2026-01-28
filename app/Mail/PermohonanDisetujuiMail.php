<?php

namespace App\Mail;

use App\Models\PermohonanReklame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanDisetujuiMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public PermohonanReklame $permohonan)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Reklame Anda Telah Disetujui - ' . $this->permohonan->nomor_registrasi,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan-disetujui',
            with: [
                'permohonan' => $this->permohonan,
                'nama_pemohon' => $this->permohonan->nama_pemohon,
                'nomor_registrasi' => $this->permohonan->nomor_registrasi,
                'jenis_reklame' => $this->permohonan->jenis_reklame,
                'lokasi_pemasangan' => $this->permohonan->lokasi_pemasangan,
                'durasi_pemasangan' => $this->permohonan->durasi_pemasangan,
                'tanggal_approval' => $this->permohonan->updated_at->format('d F Y'),
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
