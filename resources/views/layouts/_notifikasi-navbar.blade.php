{{-- Dropdown Notifikasi Navbar --}}
<div class="dropdown" id="navbarNotifWrapper">
    <button class="btn position-relative border-0 bg-transparent" type="button" id="navbarNotifBtn"
        data-bs-toggle="dropdown" aria-expanded="false"
        style="font-size:1.15rem; color:var(--text-muted); padding:.4rem .6rem;">

        <i class="bi bi-bell-fill"></i>

        <span id="navbarNotifBadge" class="d-none"
            style="position:absolute; top:2px; right:2px;
                background:linear-gradient(135deg,#f43f5e,#ec4899);
                color:#fff; font-size:.6rem; font-weight:800;
                min-width:16px; height:16px; border-radius:50%;
                align-items:center; justify-content:center;
                padding:0 3px; line-height:1;">
            0
        </span>
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0"
        style="width:340px; max-width:90vw;
            border-radius:var(--radius-lg); border:none;
            box-shadow:0 16px 40px rgba(15,23,42,.16);
            overflow:hidden;">

        <div class="d-flex align-items-center justify-content-between px-3 py-2"
            style="background:var(--bg-soft);
                border-bottom:1px solid var(--border-soft);">

            <span style="font-size:.85rem; font-weight:700; color:var(--text-dark);">
                Notifikasi
            </span>

            <a href="{{ route('notifikasi.index') }}"
                style="font-size:.74rem; color:var(--primary);
                    font-weight:600; text-decoration:none;">
                Lihat Semua
            </a>
        </div>

        <div id="navbarNotifList" style="max-height:340px; overflow-y:auto;">

            <div class="text-center py-4" style="font-size:.8rem; color:var(--text-muted);">
                Memuat notifikasi...
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        if (window.__navbarNotifInitialized) {
            return;
        }

        window.__navbarNotifInitialized = true;

        const badge = document.getElementById('navbarNotifBadge');
        const list = document.getElementById('navbarNotifList');
        const button = document.getElementById('navbarNotifBtn');

        if (!badge || !list || !button) {
            return;
        }

        const pollUrl = @json(route('notifikasi.poll'));
        const showUrlBase = @json(url('notifikasi'));

        let requestBerjalan = false;
        let intervalId = null;
        let terakhirDimuat = 0;

        const INTERVAL_POLLING = 120000;
        const JEDA_MINIMUM = 15000;

        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '';

            return element.innerHTML;
        }

        function tipeIcon(tipe) {
            return {
                sukses: 'bi-check-circle-fill',
                peringatan: 'bi-exclamation-triangle-fill',
                bahaya: 'bi-x-circle-fill'
            } [tipe] || 'bi-info-circle-fill';
        }

        function tipeColor(tipe) {
            return {
                sukses: '#10b981',
                peringatan: '#f59e0b',
                bahaya: '#f43f5e'
            } [tipe] || '#6366f1';
        }

        function renderList(items) {
            if (!Array.isArray(items) || items.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-4"
                        style="font-size:.8rem; color:var(--text-muted);">
                        <i class="bi bi-bell-slash"
                            style="font-size:1.4rem; display:block;
                                margin-bottom:.4rem; opacity:.5;">
                        </i>
                        Belum ada notifikasi
                    </div>
                `;

                return;
            }

            list.innerHTML = items.map(item => {
                const judul = escapeHtml(item.judul);
                const pesan = escapeHtml(item.pesan);
                const waktu = escapeHtml(item.waktu);
                const tipe = escapeHtml(item.tipe);
                const id = Number(item.id);

                if (!Number.isInteger(id) || id <= 0) {
                    return '';
                }

                return `
                    <a href="${showUrlBase}/${id}"
                        style="text-decoration:none; display:block;">

                        <div class="d-flex align-items-start gap-2 px-3 py-2"
                            style="border-bottom:1px solid #f6f7fb;
                                ${!item.dibaca
                                    ? 'background:rgba(99,102,241,.05);'
                                    : ''}">

                            <div style="width:32px; height:32px;
                                border-radius:9px; flex-shrink:0;
                                background:${tipeColor(tipe)}1A;
                                color:${tipeColor(tipe)};
                                display:flex; align-items:center;
                                justify-content:center; font-size:.85rem;">

                                <i class="bi ${tipeIcon(tipe)}"></i>
                            </div>

                            <div style="min-width:0; flex:1;">
                                <div style="font-size:.8rem;
                                    font-weight:700;
                                    color:var(--text-dark);
                                    overflow:hidden;
                                    text-overflow:ellipsis;
                                    white-space:nowrap;">
                                    ${judul}
                                </div>

                                <div style="font-size:.74rem;
                                    color:var(--text-muted);
                                    margin-top:2px;
                                    overflow:hidden;
                                    text-overflow:ellipsis;
                                    display:-webkit-box;
                                    -webkit-line-clamp:2;
                                    -webkit-box-orient:vertical;">
                                    ${pesan}
                                </div>

                                <div style="font-size:.68rem;
                                    color:var(--text-light);
                                    margin-top:3px;">
                                    ${waktu}
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');
        }

        async function poll(force = false) {
            if (requestBerjalan || document.hidden) {
                return;
            }

            const sekarang = Date.now();

            if (!force && sekarang - terakhirDimuat < JEDA_MINIMUM) {
                return;
            }

            requestBerjalan = true;

            try {
                const response = await fetch(pollUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error(
                        `Polling notifikasi gagal: ${response.status}`
                    );
                }

                const data = await response.json();
                const jumlah = Number(data.jumlah) || 0;

                if (jumlah > 0) {
                    badge.textContent = jumlah > 9 ? '9+' : jumlah;
                    badge.classList.remove('d-none');
                    badge.style.display = 'flex';
                } else {
                    badge.classList.add('d-none');
                    badge.style.display = 'none';
                }

                renderList(data.terbaru ?? []);
                terakhirDimuat = Date.now();
            } catch (error) {
                console.warn(error.message);
            } finally {
                requestBerjalan = false;
            }
        }

        function mulaiPolling() {
            if (intervalId !== null) {
                return;
            }

            intervalId = window.setInterval(() => {
                poll(false);
            }, INTERVAL_POLLING);
        }

        function hentikanPolling() {
            if (intervalId === null) {
                return;
            }

            window.clearInterval(intervalId);
            intervalId = null;
        }

        poll(true);
        mulaiPolling();

        button.addEventListener('show.bs.dropdown', () => {
            poll(false);
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                hentikanPolling();
                return;
            }

            poll(false);
            mulaiPolling();
        });

        window.addEventListener('pagehide', hentikanPolling);
    })();
</script>
