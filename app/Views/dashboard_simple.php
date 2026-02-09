<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    .dashboard-simple {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .welcome {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .welcome h1 {
        color: #09637E;
        margin-bottom: 15px;
    }

    .welcome p {
        color: #666;
        font-size: 18px;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #09637E 0%, #0B4F63 100%);
        color: #fff;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
    }

    .stat-number {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card h2 {
        color: #09637E;
        margin-bottom: 15px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="dashboard-simple">
    <div class="welcome">
        <h1>Welcome to IWAS Dashboard!</h1>
        <p>Integrated Water Safety Program - Dashboard is loading successfully!</p>
        <p style="margin-top: 10px; color: #28a745; font-weight: bold;">✓ If you can see this green text, the page is rendering correctly!</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number">2,266</div>
            <div class="stat-label">Total Incidents</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">892</div>
            <div class="stat-label">Saved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">1,256</div>
            <div class="stat-label">Deceased</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">118</div>
            <div class="stat-label">Missing</div>
        </div>
    </div>

    <div class="card">
        <h2>Dashboard Content</h2>
        <p>This is a simplified version of the dashboard to test if your browser can render it.</p>
        <p style="margin-top: 10px;">The full dashboard with charts will be restored once we confirm this works.</p>

        <a href="<?= base_url('/logout') ?>" class="btn btn-primary btn-sm">Logout</a>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    console.log('✓ Dashboard JavaScript is running');
    console.log('Page loaded successfully at:', new Date().toLocaleTimeString());
</script>
<?= $this->endSection() ?>
