<div class="admin-sidebar p-3 d-flex flex-column" style="width:220px;min-width:220px;">
    <div class="text-white fw-bold fs-5 mb-4 mt-2 px-2">
        <i class="bi bi-water me-2"></i>PrimeBoats
    </div>
    <nav class="nav flex-column flex-grow-1">
        <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="boats/index.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/boats/') !== false ? 'active' : '' ?>">
            <i class="bi bi-water me-2"></i>Boats
        </a>
    </nav>
    <div class="mt-auto">
        <a href="logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>
