<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Detail Hasil Monitoring</title>
    <style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop h1 {
            font-size: 14px;
            margin: 0 0 3px;
            text-transform: uppercase;
        }

        .kop h2 {
            font-size: 11px;
            margin: 0 0 6px;
            font-weight: normal;
        }

        .kop .meta {
            font-size: 9px;
            color: #475569;
        }

        .section-title {
            background: #1e293b;
            color: #fff;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 0;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        table.info td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 9.5px;
            vertical-align: top;
        }

        table.info td.label {
            width: 28%;
            background: #f1f5f9;
            font-weight: bold;
        }

        .hasil-box {
            text-align: center;
            padding: 14px;
            margin: 14px 0;
            border: 2px solid;
        }

        .hasil-aktif {
            border-color: #10b981;
            background: #ecfdf5;
            color: #047857;
        }

        .hasil-peringatan {
            border-color: #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

        .hasil-tidak_aktif {
            border-color: #f43f5e;
            background: #fff1f2;
            color: #9f1239;
        }

        .hasil-box .judul {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .hasil-box .kesimpulan {
            font-size: 9.5px;
            margin-top: 6px;
        }

        .indikator-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .indikator-table th {
            background: #1e293b;
            color: #fff;
            padding: 5px;
            font-size: 9px;
        }

        .indikator-table td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            font-size: 9px;
            text-align: center;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            color: #fff;
        }

        .badge-ya {
            background: #10b981;
        }

        .badge-tidak {
            background: #f43f5e;
        }

        .ttd-wrapper {
            width: 100%;
            margin-top: 30px;
        }

        .ttd-wrapper td {
            width: 33.33%;
            text-align: center;
            font-size: 9px;
            vertical-align: top;
        }

        .ttd-space {
            height: 55px;
        }

        .ttd-line {
            border-bottom: 1px solid #1e293b;
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <div class="kop">
        <h1>Koperasi Produsen Poltas Kreatif Mandiri</h1>
        <h2>Hasil Monitoring Usaha 14 Hari — Periode ke-{{ $laporan->periode_ke }}</h2>
        <div class="meta">Dicetak: {{ $printedAt }} oleh {{ $printedBy }} ({{ $printedRole }})</div>
    </div>

    <div class="section-title">A. Identitas Anggota</div>
    <table class="info">
        <tr>
            <td class="label">Nama</td>
            <td>{{ $anggota->akun->nama }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Anggota</td>
            <td>{{ $anggota->nomor_anggota }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td>{{ $anggota->nik }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td>{{ $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Telepon</td>
            <td>{{ $anggota->telepon ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td>{{ $anggota->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Anggota</td>
            <td>{{ $anggota->status_anggota_label }}</td>
        </tr>
    </table>

    <div class="section-title">B. Data Usaha</div>
    <table class="info">
        <tr>
            <td class="label">Nama Usaha</td>
            <td>{{ $usaha->nama_usaha }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Usaha</td>
            <td>{{ $usaha->jenis_usaha_label }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Akad</td>
            <td>{{ $usaha->jenis_akad ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Modal Awal</td>
            <td>Rp {{ number_format($usaha->modal_awal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Mulai Usaha</td>
            <td>{{ $usaha->tanggal_mulai ? $usaha->tanggal_mulai->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Usaha</td>
            <td>{{ $usaha->alamat_usaha ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Usaha</td>
            <td>{{ $usaha->status_usaha_label }}</td>
        </tr>
        <tr>
            <td class="label">Status Validasi Syariah</td>
            <td>{{ $usaha->status_validasi_syariah_label }}</td>
        </tr>
    </table>

    <div class="section-title">C. Data Laporan</div>
    <table class="info">
        <tr>
            <td class="label">Periode Ke</td>
            <td>{{ $laporan->periode_ke }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Mulai</td>
            <td>{{ $laporan->tanggal_mulai->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Selesai</td>
            <td>{{ $laporan->tanggal_selesai->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Omzet</td>
            <td>Rp {{ number_format($laporan->omzet, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Biaya Operasional</td>
            <td>Rp {{ number_format($laporan->biaya_operasional, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Laba Bersih</td>
            <td>Rp {{ number_format($laporan->laba_bersih, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Catatan Anggota</td>
            <td>{{ $laporan->keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">File Bukti Transaksi</td>
            <td>{{ $laporan->file_bukti ?? 'Tidak ada file bukti' }}</td>
        </tr>
    </table>

    <div class="section-title">D. Validasi Pengurus</div>
    <table class="info">
        <tr>
            <td class="label">Status Validasi</td>
            <td>{{ $validasi->status_pengurus_label }}</td>
        </tr>
        <tr>
            <td class="label">Nama Validator</td>
            <td>{{ $validasi->validator?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Validasi</td>
            <td>{{ $validasi->tanggal_validasi_pengurus ? $validasi->tanggal_validasi_pengurus->format('d F Y, H:i') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Catatan Pengurus</td>
            <td>{{ $validasi->catatan_pengurus ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">E. Validasi DPS</div>
    <table class="info">
        <tr>
            <td class="label">Status Validasi Syariah</td>
            <td>{{ $usaha->status_validasi_syariah_label }}</td>
        </tr>
        <tr>
            <td class="label">Nama DPS</td>
            <td>{{ $usaha->validasiAkadTerakhir?->dps?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Validasi</td>
            <td>{{ $usaha->validasiAkadTerakhir?->divalidasi_pada ? $usaha->validasiAkadTerakhir->divalidasi_pada->format('d F Y, H:i') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Jenis Akad Divalidasi</td>
            <td>{{ $usaha->validasiAkadTerakhir?->jenis_akad_divalidasi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Catatan DPS</td>
            <td>{{ $usaha->validasiAkadTerakhir?->catatan_dps ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Iterasi Validasi</td>
            <td>{{ $usaha->validasiAkadTerakhir?->iterasi ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">F. Hasil Evaluasi 14 Hari</div>
    <table class="indikator-table">
        <tr>
            <th>Omzet Sebelumnya</th>
            <th>Omzet Sekarang</th>
            <th>Kenaikan Omzet</th>
            <th>Indikator Omzet</th>
            <th>Rasio Biaya</th>
            <th>Indikator Biaya</th>
        </tr>
        <tr>
            <td>{{ $validasi->omzet_sebelumnya > 0 ? 'Rp ' . number_format($validasi->omzet_sebelumnya, 0, ',', '.') : 'Periode Pertama' }}
            </td>
            <td>Rp {{ number_format($laporan->omzet, 0, ',', '.') }}</td>
            <td>{{ $validasi->persentase_kenaikan_omzet }}%</td>
            <td><span
                    class="badge {{ $validasi->indikator_omzet ? 'badge-ya' : 'badge-tidak' }}">{{ $validasi->indikator_omzet ? 'MEMENUHI' : 'TIDAK MEMENUHI' }}</span>
            </td>
            <td>{{ $validasi->rasio_biaya }}%</td>
            <td><span
                    class="badge {{ $validasi->indikator_biaya ? 'badge-ya' : 'badge-tidak' }}">{{ $validasi->indikator_biaya ? 'MEMENUHI' : 'TIDAK MEMENUHI' }}</span>
            </td>
        </tr>
    </table>

    <div class="hasil-box hasil-{{ $validasi->hasil_evaluasi }}">
        <div class="judul">Hasil Evaluasi: {{ str_replace('_', ' ', $validasi->hasil_evaluasi) }}</div>
        <div class="kesimpulan">{{ $kesimpulan }}</div>
    </div>

    <table class="ttd-wrapper">
        <tr>
            <td>
                <div class="ttd-space"></div>
                <div class="ttd-line"></div>
                <strong>Pengurus</strong>
            </td>
            <td>
                <div class="ttd-space"></div>
                <div class="ttd-line"></div>
                <strong>DPS</strong>
            </td>
            <td>
                <div class="ttd-space"></div>
                <div class="ttd-line"></div>
                <strong>Ketua Koperasi</strong>
            </td>
        </tr>
    </table>

</body>

</html>
