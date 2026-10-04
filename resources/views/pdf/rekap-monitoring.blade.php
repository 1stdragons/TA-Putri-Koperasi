<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Monitoring</title>
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
            font-size: 15px;
            margin: 0 0 3px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .kop h2 {
            font-size: 12px;
            margin: 0 0 8px;
            font-weight: normal;
        }

        .kop .meta {
            font-size: 9px;
            color: #475569;
        }

        .info-cetak {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9px;
        }

        .info-cetak td {
            padding: 2px 4px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .stats-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: center;
            width: 11.11%;
        }

        .stats-table .label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
        }

        .stats-table .value {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.data thead {
            display: table-header-group;
        }

        table.data th {
            background: #1e293b;
            color: #fff;
            font-size: 8.5px;
            padding: 5px 4px;
            text-align: left;
            border: 1px solid #1e293b;
        }

        table.data td {
            font-size: 8.5px;
            padding: 4px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        table.data tr:nth-child(even) td {
            background: #f8fafc;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            color: #fff;
        }

        .badge-aktif {
            background: #10b981;
        }

        .badge-peringatan {
            background: #f59e0b;
        }

        .badge-tidak_aktif {
            background: #f43f5e;
        }

        .keterangan {
            margin-top: 10px;
            font-size: 9px;
        }

        .keterangan ul {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .keterangan li {
            margin-bottom: 2px;
        }

        .ttd-wrapper {
            width: 100%;
            margin-top: 36px;
        }

        .ttd-wrapper td {
            width: 50%;
            text-align: center;
            font-size: 9px;
            vertical-align: top;
        }

        .ttd-space {
            height: 60px;
        }

        .ttd-line {
            border-bottom: 1px solid #1e293b;
            width: 70%;
            margin: 0 auto;
        }

        .page-number:before {
            content: counter(page);
        }
    </style>
</head>

<body>

    <div class="kop">
        <h1>Koperasi Produsen Poltas Kreatif Mandiri</h1>
        <h2>Rekap Monitoring Usaha Anggota Koperasi Syariah</h2>
        <div class="meta">Filter diterapkan: {{ $filterLabel }}</div>
    </div>

    <table class="info-cetak">
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ $printedAt }}</td>
            <td style="width:30px;"></td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ $printedBy }}</td>
        </tr>
        <tr>
            <td><strong>Total Data</strong></td>
            <td>: {{ $stats->total_laporan }} laporan</td>
            <td></td>
            <td><strong>Role Pencetak</strong></td>
            <td>: {{ $printedRole }}</td>
        </tr>
    </table>

    <table class="stats-table">
        <tr>
            <td>
                <div class="label">Anggota</div>
                <div class="value">{{ $stats->total_anggota }}</div>
            </td>
            <td>
                <div class="label">Usaha</div>
                <div class="value">{{ $stats->total_usaha }}</div>
            </td>
            <td>
                <div class="label">Laporan</div>
                <div class="value">{{ $stats->total_laporan }}</div>
            </td>
            <td>
                <div class="label">Aktif</div>
                <div class="value" style="color:#10b981;">{{ $stats->jumlah_aktif }}</div>
            </td>
            <td>
                <div class="label">Peringatan</div>
                <div class="value" style="color:#f59e0b;">{{ $stats->jumlah_peringatan }}</div>
            </td>
            <td>
                <div class="label">Tidak Aktif</div>
                <div class="value" style="color:#f43f5e;">{{ $stats->jumlah_tidak_aktif }}</div>
            </td>
            <td>
                <div class="label">Total Omzet</div>
                <div class="value">Rp {{ number_format($stats->total_omzet, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Total Biaya</div>
                <div class="value">Rp {{ number_format($stats->total_biaya, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Total Laba</div>
                <div class="value">Rp {{ number_format($stats->total_laba, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:3%;">No</th>
                <th style="width:12%;">Anggota</th>
                <th style="width:7%;">No. Anggota</th>
                <th style="width:12%;">Usaha</th>
                <th style="width:8%;">Akad</th>
                <th style="width:9%;">Periode</th>
                <th style="width:10%;">Omzet</th>
                <th style="width:10%;">Biaya</th>
                <th style="width:10%;">Laba</th>
                <th style="width:6%;">Kenaikan</th>
                <th style="width:6%;">Rasio Biaya</th>
                <th style="width:7%;">Hasil</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->nama_anggota }}</td>
                    <td>{{ $r->nomor_anggota }}</td>
                    <td>{{ $r->nama_usaha }}</td>
                    <td>{{ $r->jenis_akad ?? '-' }}</td>
                    <td>Ke-{{ $r->periode_ke }} ({{ \Carbon\Carbon::parse($r->tanggal_mulai)->format('d/m/y') }} -
                        {{ \Carbon\Carbon::parse($r->tanggal_selesai)->format('d/m/y') }})</td>
                    <td>Rp {{ number_format($r->omzet_sekarang, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($r->biaya_operasional, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($r->laba_bersih, 0, ',', '.') }}</td>
                    <td>{{ $r->persentase_kenaikan_omzet }}%</td>
                    <td>{{ $r->rasio_biaya }}%</td>
                    <td><span
                            class="badge badge-{{ $r->hasil_evaluasi }}">{{ strtoupper(str_replace('_', ' ', $r->hasil_evaluasi)) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="keterangan">
        <strong>Keterangan Indikator Hasil Evaluasi:</strong>
        <ul>
            <li><strong>Aktif</strong> — indikator kenaikan omzet dan efisiensi biaya operasional terpenuhi.</li>
            <li><strong>Peringatan</strong> — hanya salah satu dari kedua indikator yang terpenuhi.</li>
            <li><strong>Tidak Aktif</strong> — kedua indikator tidak terpenuhi.</li>
        </ul>
    </div>

    <table class="ttd-wrapper">
        <tr>
            <td>
                Mengetahui,<br>
                <div class="ttd-space"></div>
                <div class="ttd-line"></div>
                <strong>Ketua Koperasi</strong>
            </td>
            <td>
                Diperiksa oleh,<br>
                <div class="ttd-space"></div>
                <div class="ttd-line"></div>
                <strong>Pengurus</strong>
            </td>
        </tr>
    </table>

</body>

</html>
