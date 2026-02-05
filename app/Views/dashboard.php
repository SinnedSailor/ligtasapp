<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #FFDE15;
            color: #002C76;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 60px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar-title {
            font-size: 24px;
            font-weight: 600;
        }

        .navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-email {
            font-size: 14px;
            opacity: 0.9;
        }

        .logout-btn {
            background: rgba(0, 44, 118, 0.2);
            color: #002C76;
            border: 1px solid rgba(0, 44, 118, 0.3);
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: rgba(0, 44, 118, 0.3);
        }

        /* Main Layout */
        .main-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 60px);
            overflow: hidden;
            margin-top: 60px;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 250px;
            background: #002C76;
            padding: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            position: fixed;
            height: calc(100vh - 60px);
            left: 0;
            display: flex;
            flex-direction: column;
        }

        /* Profile Section */
        .profile-section {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 222, 21, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            position: relative;
        }

        .profile-avatar:hover {
            background: rgba(255, 222, 21, 0.5);
            transform: scale(1.05);
        }

        .profile-avatar:hover::after {
            content: 'Edit Profile';
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }

        .profile-edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #FFDE15;
            color: #002C76;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid white;
        }

        .profile-name {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-email {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 14px;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255, 222, 21, 0.1);
            border-left-color: #FFDE15;
        }

        .nav-item.active {
            color: white;
            background: rgba(255, 222, 21, 0.15);
            border-left-color: #FFDE15;
        }

        .nav-icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* Content Area */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            margin-left: 250px;
            height: calc(100vh - 60px);
        }

        @media (max-width: 768px) {
            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 15px 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .nav-menu {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .nav-item {
                padding: 12px 15px;
                font-size: 12px;
                gap: 8px;
            }

            .nav-item span:last-child {
                display: none;
            }
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 15px 25px;
        }

        .welcome-section {
            background: white;
            border-radius: 8px;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 15px;
        }

        .welcome-section h1 {
            color: #1C4D8D;
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .welcome-section p {
            color: #666;
            font-size: 13px;
        }

        .import-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .import-section h2 {
            color: #1e3c72;
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2a5298;
        }

        .import-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .import-btn {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .import-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 82, 152, 0.4);
        }

        .file-name {
            color: #666;
            font-size: 14px;
            margin-left: 10px;
        }

        .charts-section {
            margin-bottom: 15px;
        }

        .section-title {
            color: #1C4D8D;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 3px solid #2a5298;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .chart-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chart-card h3 {
            color: #1C4D8D;
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .chart-container {
            position: relative;
            height: 320px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h4 {
            color: #1C4D8D;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .stat-number {
            color: #1C4D8D;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #999;
            font-size: 11px;
        }

        /* Table Styles */
        .table-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            overflow-x: auto;
        }

        .table-section h2 {
            color: #1e3c72;
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2a5298;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #ddd;
        }

        table td {
            padding: 12px;
            border: 1px solid #ddd;
            color: #333;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #f0f4ff;
        }

        .edit-btn, .delete-btn, .save-btn, .cancel-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.2s;
            margin-right: 4px;
        }

        .edit-btn {
            background: #2a5298;
            color: white;
        }

        .edit-btn:hover {
            background: #1e3c72;
        }

        .delete-btn {
            background: #f5222d;
            color: white;
        }

        .delete-btn:hover {
            background: #d9001b;
        }

        .save-btn {
            background: #52c41a;
            color: white;
        }

        .save-btn:hover {
            background: #3f8600;
        }

        .cancel-btn {
            background: #bfbfbf;
            color: white;
        }

        .cancel-btn:hover {
            background: #8c8c8c;
        }

        .table-input {
            width: 100%;
            padding: 6px;
            border: 1px solid #2a5298;
            border-radius: 4px;
            font-size: 12px;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            color: #999;
            font-size: 11px;
            margin-top: 10px;
        }

        /* Confirmation Modal Styles */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .confirmation-modal.show {
            display: flex;
        }

        .confirmation-dialog {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            min-width: 350px;
        }

        .confirmation-dialog h2 {
            color: #1C4D8D;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .confirmation-dialog p {
            color: #666;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .confirmation-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-yes, .btn-no {
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            min-width: 120px;
        }

        .btn-yes {
            background: #1C4D8D;
            color: white;
        }

        .btn-yes:hover {
            background: #2563A8;
        }

        .btn-no {
            background: #f5222d;
            color: white;
        }

        .btn-no:hover {
            background: #d9001b;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 280px;
            }

            .welcome-section h1 {
                font-size: 22px;
            }

            table {
                font-size: 12px;
            }

            table th, table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">Integrated Water Safety Program</div>
        <div class="navbar-right">
            <a href="#" class="logout-btn" onclick="showLogoutConfirmation(event)">Logout</a>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="confirmation-modal" id="logoutModal">
        <div class="confirmation-dialog">
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to logout?</p>
            <div class="confirmation-buttons">
                <button class="btn-yes" onclick="confirmLogout()">Yes</button>
                <button class="btn-no" onclick="cancelLogout()">No</button>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <!-- Profile Section -->
            <div class="profile-section">
                <a href="<?= base_url('/user-profile') ?>" class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                    <span class="profile-edit-icon"><i class="bi bi-pencil-fill"></i></span>
                </a>
                <div class="profile-name"><?= strtoupper(session()->get('username') ?? 'User') ?></div>
                <div class="profile-email"><?= session()->get('role_name') ?? 'No Role' ?></div>
            </div>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <a href="<?= base_url('/dashboard') ?>" class="nav-item active">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('/ordinance') ?>" class="nav-item">
                    <i class="bi bi-file-earmark-arrow-up nav-icon"></i>
                    <span>Ordinance</span>
                </a>
                <a href="<?= base_url('/pops') ?>" class="nav-item">
                    <i class="bi bi-shield-check nav-icon"></i>
                    <span>POPS Plan</span>
                </a>
                <a href="<?= base_url('/incident-report') ?>" class="nav-item">
                    <i class="bi bi-exclamation-triangle nav-icon"></i>
                    <span>Incident Report</span>
                </a>
                <?php if (session()->get('is_admin')): ?>
                    <a href="<?= base_url('/admin-panel') ?>" class="nav-item">
                        <i class="bi bi-shield-lock nav-icon"></i>
                        <span>Admin Panel</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="content-wrapper">

    <div class="container">
        <div class="welcome-section">
            <h1>DASHBOARD</h1>
            <p style="font-size: 13px; color: #999; margin-top: 5px;">Welcome, <?= session()->get('username') ?></p>
        </div>

        <!-- Summary Statistics -->
        <div class="charts-section">
            <h2 class="section-title">Summary Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Incidents</h4>
                    <div class="stat-number">2,847</div>
                    <div class="stat-label">All Provinces (2020-2024)</div>
                </div>
                <div class="stat-card">
                    <h4>Total Fatalities</h4>
                    <div class="stat-number">1,256</div>
                    <div class="stat-label">Death Rate: 44.1%</div>
                </div>
                <div class="stat-card">
                    <h4>Highest Risk Province</h4>
                    <div class="stat-number">Pangasinan</div>
                    <div class="stat-label">612 incidents (21.5%)</div>
                </div>
                <div class="stat-card">
                    <h4>Most Affected Age Group</h4>
                    <div class="stat-number">0-14 Years</div>
                    <div class="stat-label">38.2% of incidents</div>
                </div>
            </div>
        </div>

        <!-- Drowning Incidents by Province -->
        <div class="charts-section">
            <h2 class="section-title">Drowning Incidents by Province</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Incidents per Province</h3>
                    <div class="chart-container">
                        <canvas id="provinceChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Province Distribution (%)</h3>
                    <div class="chart-container">
                        <canvas id="provincePercentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demographics -->
        <div class="charts-section">
            <h2 class="section-title">Demographics</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Incidents by Sex</h3>
                    <div class="chart-container">
                        <canvas id="sexChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Incidents by Age Group</h3>
                    <div class="chart-container">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Temporal Trends -->
        <div class="charts-section">
            <h2 class="section-title">Temporal Trends</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Incidents by Year</h3>
                    <div class="chart-container">
                        <canvas id="yearChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Incidents by Occasion</h3>
                    <div class="chart-container">
                        <canvas id="occasionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location & Factors -->
        <div class="charts-section">
            <h2 class="section-title">Residence & Risk Factors</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Incidents by Residence</h3>
                    <div class="chart-container">
                        <canvas id="residenceChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Contributing Factors</h3>
                    <div class="chart-container">
                        <canvas id="factorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2026 Integrated Water Safety Program. All rights reserved.</p>
    </div>
        </div>
    </div>

    <script>
        // Chart Colors
        const chartColors = ['#1C4D8D', '#2563A8', '#2E78C3', '#378DDE', '#40A2F9', '#5DB3FF', '#7AC4FF', '#97D5FF'];

        // Province Chart
        new Chart(document.getElementById('provinceChart'), {
            type: 'bar',
            data: {
                labels: ['Ilocos Norte', 'Ilocos Sur', 'La Union', 'Pangasinan'],
                datasets: [{
                    label: 'Number of Incidents',
                    data: [456, 523, 378, 612],
                    backgroundColor: chartColors.slice(0, 4),
                    borderColor: chartColors.slice(0, 4),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Province Percentage Chart
        new Chart(document.getElementById('provincePercentChart'), {
            type: 'doughnut',
            data: {
                labels: ['Ilocos Norte', 'Ilocos Sur', 'La Union', 'Pangasinan'],
                datasets: [{
                    data: [16.0, 18.4, 13.3, 21.5],
                    backgroundColor: chartColors.slice(0, 4),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Sex Chart
        new Chart(document.getElementById('sexChart'), {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [72.5, 27.5],
                    backgroundColor: ['#5459AC', '#F2AEBB'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Age Group Chart
        new Chart(document.getElementById('ageChart'), {
            type: 'bar',
            data: {
                labels: ['0-14 Years', '15-24 Years', '25-34 Years', '35-44 Years', '45+ Years'],
                datasets: [{
                    label: 'Percentage (%)',
                    data: [38.2, 28.5, 18.3, 10.2, 4.8],
                    backgroundColor: '#2a5298',
                    borderColor: '#2a5298',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: true } },
                scales: { x: { beginAtZero: true } }
            }
        });

        // Year Chart
        new Chart(document.getElementById('yearChart'), {
            type: 'line',
            data: {
                labels: ['2020', '2021', '2022', '2023', '2024'],
                datasets: [{
                    label: 'Total Incidents',
                    data: [512, 598, 625, 702, 410],
                    borderColor: '#2a5298',
                    backgroundColor: 'rgba(42, 82, 152, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Occasion Chart
        new Chart(document.getElementById('occasionChart'), {
            type: 'bar',
            data: {
                labels: ['Swimming', 'Bathing', 'Fishing', 'Boating', 'Wading', 'Playing', 'Other'],
                datasets: [{
                    label: 'Number of Incidents',
                    data: [485, 634, 523, 412, 298, 367, 128],
                    backgroundColor: chartColors,
                    borderColor: chartColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Residence Chart
        new Chart(document.getElementById('residenceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Urban', 'Rural', 'Coastal'],
                datasets: [{
                    data: [42.3, 38.7, 19.0],
                    backgroundColor: ['#1e3c72', '#2a5298', '#3d5a80'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Risk Factors Chart
        new Chart(document.getElementById('factorsChart'), {
            type: 'bar',
            data: {
                labels: ['Unable to Swim', 'Lack of Supervision', 'Intoxication', 'Sudden Illness', 'Water Hazards', 'No Life Jacket'],
                datasets: [{
                    label: 'Percentage (%)',
                    data: [52.3, 31.2, 18.5, 14.7, 22.1, 28.4],
                    backgroundColor: ['#f5222d', '#fa541c', '#fa8c16', '#faad14', '#fadb14', '#1e3c72'],
                    borderColor: ['#f5222d', '#fa541c', '#fa8c16', '#faad14', '#fadb14', '#1e3c72'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: true } },
                scales: { x: { beginAtZero: true } }
            }
        });
    </script>

    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById('logoutModal').classList.add('show');
        }

        function confirmLogout() {
            window.location.href = '<?= base_url('/logout') ?>';
        }

        function cancelLogout() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        // Close modal when clicking outside of it
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                cancelLogout();
            }
        });
    </script>
</body>
</html>
