<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }

    .stat-card h4 {
        font-size: 0.9rem;
        margin-bottom: 8px;
        color: #6c757d;
    }

    .stat-card {
        background: rgba(9, 99, 126, 0.06);
        border: 1px solid rgba(9, 99, 126, 0.12);
    }

    .stat-number {
        font-size: 1.6rem;
        font-weight: 700;
        color: #09637E;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #343a40;
    }

    .page-header {
        background: #002C76;
        color: #fff;
        border-radius: 8px;
        padding: 16px 20px;
    }

    .page-header .page-title,
    .page-header .text-muted {
        color: #fff !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h3 class="page-title">Dashboard</h3>
    <div class="text-muted">Welcome, <?= esc(session()->get('username') ?? 'User') ?></div>
</div>

<div class="row">
    <div class="col-md-3 grid-margin">
        <div class="card stat-card">
            <div class="card-body">
                <h4>Total Incidents</h4>
                <div class="stat-number">2,847</div>
                <div class="text-muted">All Provinces (2020-2024)</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin">
        <div class="card stat-card">
            <div class="card-body">
                <h4>Total Fatalities</h4>
                <div class="stat-number">1,256</div>
                <div class="text-muted">Death Rate: 44.1%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin">
        <div class="card stat-card">
            <div class="card-body">
                <h4>Highest Risk Province</h4>
                <div class="stat-number">Pangasinan</div>
                <div class="text-muted">612 incidents (21.5%)</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin">
        <div class="card stat-card">
            <div class="card-body">
                <h4>Most Affected Age Group</h4>
                <div class="stat-number">0-14 Years</div>
                <div class="text-muted">38.2% of incidents</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Incidents per Province</div>
                <div class="chart-container">
                    <canvas id="provinceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Remarks Status</div>
                <div class="chart-container">
                    <canvas id="remarksChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Incidents by Sex</div>
                <div class="chart-container">
                    <canvas id="sexChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Incidents by Age Group</div>
                <div class="chart-container">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Incidents by Year</div>
                <div class="chart-container">
                    <canvas id="yearChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Incidents by Holiday</div>
                <div class="chart-container">
                    <canvas id="occasionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Incidents by Residence</div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <label for="residenceFilter" class="text-muted mb-0">Filter by:</label>
                    <select id="residenceFilter" onchange="updateResidenceChart()" class="form-control form-control-sm" style="max-width: 180px;">
                        <option value="province">Province</option>
                        <option value="municipality">Municipality</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="residenceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="section-title">Contributing Factors</div>
                <div class="chart-container">
                    <canvas id="factorsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/staradmin/vendors/chart.js/chart.umd.js') ?>"></script>
<script>
    const chartColors = ['#09637E', '#0E7EA0', '#0B4F63', '#5FA8B3', '#C94A4A', '#D6B443', '#3FAF7B', '#94C7CF'];
    const accentRed = '#C94A4A';
    const accentYellow = '#D6B443';
    const accentGreen = '#3FAF7B';

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

    new Chart(document.getElementById('remarksChart'), {
        type: 'pie',
        data: {
            labels: ['Saved', 'Deceased', 'Missing'],
            datasets: [{
                data: [892, 1256, 118],
                backgroundColor: [accentGreen, accentRed, accentYellow],
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

    new Chart(document.getElementById('sexChart'), {
        type: 'pie',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                data: [72.5, 27.5],
                backgroundColor: ['#09637E', accentRed],
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

    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: {
            labels: ['0-14 Years', '15-24 Years', '25-34 Years', '35-44 Years', '45+ Years'],
            datasets: [{
                label: 'Percentage (%)',
                data: [38.2, 28.5, 18.3, 10.2, 4.8],
                backgroundColor: accentRed,
                borderColor: accentRed,
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

    new Chart(document.getElementById('yearChart'), {
        type: 'line',
        data: {
            labels: ['2020', '2021', '2022', '2023', '2024'],
            datasets: [{
                label: 'Total Incidents',
                data: [512, 598, 625, 702, 410],
                borderColor: accentYellow,
                backgroundColor: 'rgba(214, 180, 67, 0.15)',
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

    new Chart(document.getElementById('occasionChart'), {
        type: 'bar',
        data: {
            labels: ['Christmas', 'New Year', 'Holy Week', 'All Saints Day', 'Independence Day', 'Halloween', 'Summer Break', 'Other Holidays'],
            datasets: [{
                label: 'Number of Incidents',
                data: [523, 487, 412, 298, 234, 189, 645, 159],
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

    let residenceChartInstance;

    const residenceDataByProvince = {
        labels: ['Ilocos Norte', 'Ilocos Sur', 'La Union', 'Pangasinan'],
        data: [456, 523, 378, 612],
        backgroundColor: chartColors.slice(0, 4)
    };

    const residenceDataByMunicipality = {
        labels: ['San Fernando', 'Dagupan', 'Laoag', 'Vigan', 'Alaminos', 'Candon', 'Batac', 'San Juan'],
        data: [234, 198, 167, 189, 145, 123, 156, 134],
        backgroundColor: chartColors
    };

    function updateResidenceChart() {
        const filterValue = document.getElementById('residenceFilter').value;
        const data = filterValue === 'province' ? residenceDataByProvince : residenceDataByMunicipality;

        if (residenceChartInstance) {
            residenceChartInstance.destroy();
        }

        residenceChartInstance = new Chart(document.getElementById('residenceChart'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.backgroundColor,
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
    }

    updateResidenceChart();

    new Chart(document.getElementById('factorsChart'), {
        type: 'bar',
        data: {
            labels: ['Unable to Swim', 'Lack of Supervision', 'Intoxication', 'Sudden Illness', 'Water Hazards', 'No Life Jacket'],
            datasets: [{
                label: 'Percentage (%)',
                data: [52.3, 31.2, 18.5, 14.7, 22.1, 28.4],
                backgroundColor: [accentRed, '#09637E', accentYellow, accentGreen, '#0E7EA0', '#0B4F63'],
                borderColor: [accentRed, '#09637E', accentYellow, accentGreen, '#0E7EA0', '#0B4F63'],
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
<?= $this->endSection() ?>
