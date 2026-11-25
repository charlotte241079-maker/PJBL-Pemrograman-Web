@php
    $userName = auth()->user()->name ?? 'Admin';
    $initial = strtoupper(mb_substr($userName, 0, 1));
@endphp

<header class="top-navbar">

    <div class="d-flex align-items-center gap-3">
        <button type="button" class="menu-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <h5 class="mb-0">Selamat Datang, Juragan!</h5>
            <small class="text-muted"><i>Let's make more money today.</i></small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">

        <div class="date-info d-none d-md-flex">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>

        <div class="dropdown">
            <button type="button" class="profile-button dropdown-toggle"
                    data-bs-toggle="dropdown">
                <div class="profile-avatar">{{ $initial }}</div>
                <span class="d-none d-md-inline">{{ $userName }}</span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text small text-muted">
                        {{ auth()->user()->email ?? 'admin@umkmhub.id' }}
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>