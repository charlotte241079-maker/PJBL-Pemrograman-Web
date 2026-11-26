
<aside class="sidebar" id="sidebar">

    {{-- BRAND --}}
    <div class="brand">
        <div class="brand-icon">
            <i class="bi bi-shop"></i>
        </div>

        <div>
            <h5>UMKM<span>Hub.</span></h5>
            <small>Management System</small>
        </div>
    </div>


    {{-- ==========================================
         SIDEBAR ADMIN
    =========================================== --}}
    @if (auth()->check() && auth()->user()->isAdmin())

        {{-- MENU ADMIN --}}
        <div class="sidebar-label">MENU ADMIN</div>

        <nav class="nav-menu">

            {{-- DASHBOARD ADMIN --}}
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard Admin</span>
            </a>


            {{-- MANAJEMEN UMKM --}}
            <div class="sidebar-label mt-4">MANAJEMEN UMKM</div>

            {{-- PERSETUJUAN PENDAFTARAN --}}
            @if (Route::has('admin.pendaftaran.index'))
                <a href="{{ route('admin.pendaftaran.index') }}"
                   class="nav-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Persetujuan Pendaftaran</span>
                </a>
            @endif

            {{-- DATA UMKM --}}
            @if (Route::has('admin.umkm.index'))
                <a href="{{ route('admin.umkm.index') }}"
                   class="nav-link {{ request()->routeIs('admin.umkm.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Data UMKM</span>
                </a>
            @endif


            {{-- PUSAT PEMANTAUAN --}}
            <div class="sidebar-label mt-4">PUSAT PEMANTAUAN</div>

            {{-- PEMBELIAN --}}
            <a href="{{ url('/admin/pembelian') }}"
               class="nav-link {{ request()->is('admin/pembelian*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                <span>Pembelian</span>
            </a>

            {{-- PENGELUARAN --}}
            <a href="{{ url('/admin/pengeluaran') }}"
               class="nav-link {{ request()->is('admin/pengeluaran*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Pengeluaran</span>
            </a>

        </nav>


    {{-- ==========================================
         SIDEBAR UMKM
    =========================================== --}}
    @elseif (auth()->check() && auth()->user()->isUmkm())

        {{-- MENU UTAMA --}}
        <div class="sidebar-label">MENU UTAMA</div>

        <nav class="nav-menu">

            {{-- DASHBOARD --}}
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>


            {{-- STOK BAHAN --}}
            <a href="{{ url('/stok-bahan') }}"
               class="nav-link {{ request()->is('stok-bahan*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Stok Bahan</span>
            </a>


            {{-- PEMBELIAN --}}
            <a href="{{ url('/pembelian') }}"
               class="nav-link {{ request()->is('pembelian*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                <span>Pemasukan</span>
            </a>


            {{-- PENGELUARAN --}}
            <a href="{{ url('/pengeluaran') }}"
               class="nav-link {{ request()->is('pengeluaran*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Pengeluaran</span>
            </a>


            {{-- ANALISIS DAN DATA --}}
            <div class="sidebar-label mt-4">ANALISIS & DATA</div>


            {{-- ANALISIS DATA --}}
            <a href="{{ url('/analisis-data') }}"
               class="nav-link {{ request()->is('analisis-data*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Analisis Data</span>
            </a>


            {{-- REKAP LAPORAN --}}
            <a href="{{ url('/rekap-laporan') }}"
               class="nav-link {{ request()->is('rekap-laporan*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Rekap Laporan</span>
            </a>


            {{-- IMPORT EXPORT --}}
            <a href="{{ url('/import-export') }}"
               class="nav-link {{ request()->is('import-export*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Import / Export</span>
            </a>


            {{-- CATATAN --}}
            <a href="{{ route('notes.index') }}"
               class="nav-link {{ request()->routeIs('notes.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Catatan</span>
            </a>

        </nav>

    @endif


    {{-- ==========================================
         SIDEBAR FOOTER
    =========================================== --}}
    <div class="sidebar-footer">
        <i class="bi bi-leaf"></i>

        <div>
            <strong>Grow Your Business</strong>
            <small>Manage smarter, grow better.</small>
        </div>
    </div>

</aside>
