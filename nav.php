<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<nav>
    <ul>
        <li>
            <img src="download.jpg" alt="logo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
        </li>

        <!-- Always show Dashboard -->
        <li><a href="admin_dashboard.php">Dashboard</a></li>

        <!-- Show Manage Permissions only for managers -->
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
    <li><a href="manage_permissions.php">Manage Permissions</a></li>
<?php endif; ?>

        <!-- Manage Medicines -->
        <?php if (in_array('manage_medicines', $_SESSION['permissions'])): ?>
            <li>
                <a href="#">Manage Medicines</a>
                <ul>
                    <?php if (in_array('view_medicines', $_SESSION['permissions'])): ?>
                        <li><a href="manage_medicines.php">View Medicines</a></li>
                    <?php endif; ?>
                    <?php if (in_array('add_medicine', $_SESSION['permissions'])): ?>
                        <li><a href="add_medicine.php">Add Medicine</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <!-- Manage Cosmetics -->
        <?php if (in_array('manage_cosmetics', $_SESSION['permissions'])): ?>
            <li>
                <a href="#">Manage Cosmetics</a>
                <ul>
                    <?php if (in_array('view_cosmetics', $_SESSION['permissions'])): ?>
                        <li><a href="manage_cosmetics.php">View Cosmetics</a></li>
                    <?php endif; ?>
                    <?php if (in_array('add_cosmetic', $_SESSION['permissions'])): ?>
                        <li><a href="add_cosmetic.php">Add Cosmetic</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <!-- Sales -->
        <?php if (in_array('sales', $_SESSION['permissions'])): ?>
            <li>
                <a href="#">Sales</a>
                <ul>
                    <?php if (in_array('medicine_sales', $_SESSION['permissions'])): ?>
                        <li><a href="sales_page.php">Medicine Sales</a></li>
                    <?php endif; ?>
                    <?php if (in_array('cosmetic_sales', $_SESSION['permissions'])): ?>
                        <li><a href="sale_pagec.php">Cosmetic Sales</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <!-- Manage Section -->
        <?php if (in_array('manage', $_SESSION['permissions'])): ?>
            <li>
                <a href="#">Manage</a>
                <ul>
                    <?php if (in_array('manage_companies', $_SESSION['permissions'])): ?>
                        <li><a href="manage_companies.php">Manage Companies</a></li>
                    <?php endif; ?>
                    <?php if (in_array('manage_stock', $_SESSION['permissions'])): ?>
                        <li><a href="manage_stock.php">Manage Stock (BIN CARD)</a></li>
                    <?php endif; ?>
                    <?php if (in_array('manage_expired_medicine', $_SESSION['permissions'])): ?>
                        <li><a href="manage_expired_medicine.php">Manage Expired Medicine</a></li>
                    <?php endif; ?>
                    <?php if (in_array('batch_reports', $_SESSION['permissions'])): ?>
                        <li><a href="batch_reports.php">Batch Reports</a></li>
                    <?php endif; ?>

                  

                    <?php if (in_array('add_employee', $_SESSION['permissions'])): ?>
                        <li><a href="add_employee.php">Add Employee</a></li>
                    <?php endif; ?>
                    <?php if (in_array('view_employees', $_SESSION['permissions'])): ?>
                        <li><a href="view_employees.php">View Employees</a></li>
                    <?php endif; ?>
                    <?php if (in_array('invoice_search', $_SESSION['permissions'])): ?>
                        <li><a href="invoice_search.php">Invoice Search</a></li>
                    <?php endif; ?>
                    <?php if (in_array('cosmetic_profits', $_SESSION['permissions'])): ?>
                        <li><a href="cosmoticp.php">Cosmetic Profits</a></li>
                    <?php endif; ?>
                    <?php if (in_array('total_profits', $_SESSION['permissions'])): ?>
                        <li><a href="profitpage.php">Total (C + M) Profits</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownLinks = document.querySelectorAll('nav ul li > a');

    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.nextElementSibling && this.nextElementSibling.tagName === 'UL') {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
            }
        });
    });

    document.addEventListener('click', function(e) {
        dropdownLinks.forEach(link => {
            const submenu = link.nextElementSibling;
            if (submenu && submenu.tagName === 'UL') {
                if (!link.contains(e.target) && !submenu.contains(e.target)) {
                    submenu.style.display = 'none';
                }
            }
        });
    });
});
</script>
