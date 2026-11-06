<?php

namespace App\Mail;

use App\Models\Kuitansi;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PengingatJatuhTempoMail extends Mailable
{
    /**
     * @param  array<int, array<string, string>>  $hariH
     * @param  array<int, array<string, string>>  $tujuhHari
     */
    public function __construct(
        public string $namaPemilik,
        public array $hariH,
        public array $tujuhHari,
    ) {}

    public function envelope(): Envelope
    {
        $bagian = [];

        if ($this->hariH !== []) {
            $bagian[] = count($this->hariH).' tagihan jatuh tempo hari ini';
        }

        if ($this->tujuhHari !== []) {
            $bagian[] = count($this->tujuhHari).' tagihan jatuh tempo '.Kuitansi::HARI_PENGINGAT.' hari lagi';
        }

        return new Envelope(subject: 'BukuTempo: '.implode(' dan ', $bagian));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pengingat-jatuh-tempo',
            text: 'emails.pengingat-jatuh-tempo-text',
        );
    }
}
