<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Detail Validasi Syariah</title>
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

        .section-title {
            background: #1e293b;
            color: #fff;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 14px;
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

        table.riwayat {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.riwayat th {
            background: #334155;
            color: #fff;
            font-size: 8.5px;
            padding: 5px;
            text-align: left;
        }

        table.riwayat td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            font-size: 8.5px;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            color: #fff;
        }

        .badge-disetujui {
            background: #10b981;
        }

        .badge-perlu_revisi {
            background: #f43f5e;
        }

        .ttd-wrapper {
            width: 100%;
            margin-top: 30px;
        }

        .ttd-wrapper td {
            width: 50%;
            text-align: center;
            font-size: 9px;
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
        <h2>Detail Riwayat Validasi Syariah &amp; Akad Usaha</h2>
        <div style="font-size:9px; color:#475569;">Dicetak: {{ $printedAt }} oleh {{ $printedBy }}</div>
    </div>

    <div class="section-title">Identitas Anggota &amp; Usaha</div>
    <table class="info">
        <tr>
            <td class="label">Nama Anggota</td>
            <td>{{ $anggota->akun->nama }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Anggota</td>
            <td>{{ $anggota->nomor_anggota }}</td>
        </tr>
        <tr>
            <td class="label">Nama Usaha</td>
            <td>{{ $usaha->nama_usaha }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Usaha</td>
            <td>{{ $usaha->jenis_usaha_label }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Akad Saat Ini</td>
            <td>{{ $usaha->jenis_akad ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">File Akad Saat Ini</td>
            <td>{{ $usaha->file_akad ?? 'Tidak ada file' }}</td>
        </tr>
        <tr>
            <td class="label">Status Validasi Syariah</td>
            <td>{{ $usaha->status_validasi_syariah_label }}</td>
        </tr>
    </table>

    <div class="section-title">Riwayat Iterasi Validasi</div>
    <table class="riwayat">
        <thead>
            <tr>
                <th style="width:8%;">Iterasi</th>
                <th style="width:15%;">Jenis Akad</th>
                <th style="width:20%;">File Divalidasi</th>
                <th style="width:15%;">Status</th>
                <th style="width:15%;">Nama DPS</th>
                <th style="width:12%;">Tanggal</th>
                <th style="width:15%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($riwayat as $rv)
                <tr>
                    <td>{{ $rv->iterasi }}</td>
                    <td>{{ $rv->jenis_akad_divalidasi ?? '-' }}</td>
                    <td>{{ $rv->file_akad_divalidasi ?? '-' }}</td>
                    <td><span
                            class="badge badge-{{ $rv->status }}">{{ strtoupper(str_replace('_', ' ', $rv->status)) }}</span>
                    </td>
                    <td>{{ $rv->dps?->nama ?? '-' }}</td>
                    <td>{{ $rv->divalidasi_pada ? $rv->divalidasi_pada->format('d/m/Y') : '-' }}</td>
                    <td>{{ $rv->catatan_dps ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="ttd-wrapper">
        <tr>
            <td>
                <div class="ttd-space"></div>
                <div class="ttd-line"></div>
                <strong>Dewan Pengawas Syariah</strong>
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
