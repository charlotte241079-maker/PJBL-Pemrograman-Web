
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

    {{-- MENU UTAMA --}}
    <div class="sidebar-label">MENU UTAMA</div>

    <nav class="nav-menu">

        <a href="{{ url('/dashboard') }}"
           class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ url('/stok-bahan') }}"
           class="nav-link {{ request()->is('stok-bahan*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Stok Bahan</span>
        </a>

        <a href="{{ url('/pembelian') }}"
           class="nav-link {{ request()->is('pembelian*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i>
            <span>Pemasukan</span>
        </a>

        <a href="{{ url('/pengeluaran') }}"
           class="nav-link {{ request()->is('pengeluaran*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Pengeluaran</span>
        </a>

        {{-- ANALISIS & DATA --}}
        <div class="sidebar-label mt-4">ANALISIS & DATA</div>

        <a href="{{ url('/analisis-data') }}"
           class="nav-link {{ request()->is('analisis-data*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Analisis Data</span>
        </a>

        <a href="{{ url('/rekap-laporan') }}"
           class="nav-link {{ request()->is('rekap-laporan*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Rekap Laporan</span>
        </a>

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

    {{-- SIDEBAR FOOTER --}}
    <div class="sidebar-footer">
        <i class="bi bi-leaf"></i>

        <div>
            <strong>Grow Your Business</strong>
            <small>Manage smarter, grow better.</small>
        </div>
    </div>

</aside>
