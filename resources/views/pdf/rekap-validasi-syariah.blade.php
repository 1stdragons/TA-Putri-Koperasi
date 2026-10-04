<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Validasi Syariah</title>
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

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .stats-table td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            text-align: center;
            width: 20%;
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

        .section-title {
            background: #1e293b;
            color: #fff;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 14px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data thead {
            display: table-header-group;
        }

        table.data th {
            background: #334155;
            color: #fff;
            font-size: 8.5px;
            padding: 5px 4px;
            text-align: left;
            border: 1px solid #334155;
        }

        table.data td {
            font-size: 8.5px;
            padding: 4px;
            border: 1px solid #cbd5e1;
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

        .badge-disetujui {
            background: #10b981;
        }

        .badge-perlu_revisi {
            background: #f43f5e;
        }

        .badge-menunggu {
            background: #f59e0b;
        }

        .badge-belum_diupload {
            background: #64748b;
        }
    </style>
</head>

<body>

    <div class="kop">
        <h1>Koperasi Produsen Poltas Kreatif Mandiri</h1>
        <h2>Rekap Validasi Syariah &amp; Akad Usaha Anggota</h2>
        <div style="font-size:9px; color:#475569;">Dicetak: {{ $printedAt }} oleh {{ $printedBy }}</div>
    </div>

    <table class="stats-table">
        <tr>
            <td>
                <div class="label">Total Usaha</div>
                <div class="value">{{ $totalUsaha }}</div>
            </td>
            <td>
                <div class="label">Disetujui</div>
                <div class="value" style="color:#10b981;">{{ $jumlahDisetujui }}</div>
            </td>
            <td>
                <div class="label">Perlu Revisi</div>
                <div class="value" style="color:#f43f5e;">{{ $jumlahPerluRevisi }}</div>
            </td>
            <td colspan="2">
                <div class="label">Distribusi Jenis Akad</div>
                <div class="value" style="font-size:9px; font-weight:normal;">
                    @foreach ($distribusiAkad as $akad => $jml)
                        {{ $akad }}: {{ $jml }}@if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Usaha &amp; Status Validasi</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:3%;">No</th>
                <th style="width:14%;">Anggota</th>
                <th style="width:8%;">No. Anggota</th>
                <th style="width:15%;">Usaha</th>
                <th style="width:10%;">Jenis Akad</th>
                <th style="width:12%;">Status Syariah</th>
                <th style="width:12%;">DPS Terakhir</th>
                <th style="width:10%;">Tgl Validasi</th>
                <th style="width:6%;">Iterasi</th>
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
                    <td><span
                            class="badge badge-{{ $r->status_validasi_syariah }}">{{ strtoupper(str_replace('_', ' ', $r->status_validasi_syariah)) }}</span>
                    </td>
                    <td>{{ $r->validasiAkadTerakhir?->dps?->nama ?? '-' }}</td>
                    <td>{{ $r->validasiAkadTerakhir?->divalidasi_pada ? $r->validasiAkadTerakhir->divalidasi_pada->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $r->validasi_akad_count }}x</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Riwayat Validasi Syariah</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:5%;">Iterasi</th>
                <th style="width:20%;">Usaha</th>
                <th style="width:14%;">DPS</th>
                <th style="width:12%;">Status</th>
                <th style="width:12%;">Tanggal</th>
                <th style="width:37%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($riwayat as $rv)
                <tr>
                    <td>{{ $rv->iterasi }}</td>
                    <td>{{ $rv->usaha->nama_usaha ?? '-' }}</td>
                    <td>{{ $rv->dps?->nama ?? '-' }}</td>
                    <td><span
                            class="badge badge-{{ $rv->status }}">{{ strtoupper(str_replace('_', ' ', $rv->status)) }}</span>
                    </td>
                    <td>{{ $rv->divalidasi_pada ? $rv->divalidasi_pada->format('d/m/Y') : '-' }}</td>
                    <td>{{ $rv->catatan_dps ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
