<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_ec.php';

$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Approved products per month
$approvedPerMonth = array_fill(0, 12, 0);
$query = "
    SELECT MONTH(created_at) AS month, COUNT(*) AS count
    FROM products
    WHERE status = 2 AND YEAR(created_at) = ?
    GROUP BY MONTH(created_at)
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $selectedYear);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $approvedPerMonth[(int)$row['month'] - 1] = (int)$row['count'];
}
$stmt->close();

$currentMonthApproved = 0;
if ($selectedYear == date('Y')) {
    $currentMonthApproved = $approvedPerMonth[(int)date('n') - 1];
}

// Year options
$yearOptions = [];
$res = $conn->query("SELECT MIN(YEAR(created_at)) as min_year FROM products");
if ($row = $res->fetch_assoc()) {
    $minYear = $row['min_year'] ?? date('Y');
    for ($y = $minYear; $y <= date('Y'); $y++) {
        $yearOptions[] = $y;
    }
}

// Top liked confirmed products
$topLikedProducts = [];
$query = "
    SELECT product_name, likes
    FROM products
    WHERE status = 2
    ORDER BY likes DESC
    LIMIT 5
";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $topLikedProducts[] = [
        'name' => $row['product_name'],
        'likes' => (int)$row['likes']
    ];
}

$productNames = array_column($topLikedProducts, 'name');
$productLikes = array_column($topLikedProducts, 'likes');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>
    <link rel="stylesheet" href="assets/modules/chart.min.css">
    <link rel="icon" type="image/png" href="assets/img/dost.png">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .card-statistic-1 {
            transition: transform 0.2s ease;
        }
        .card-statistic-1:hover {
            transform: scale(1.02);
        }
        select.form-select {
            min-width: 120px;
        }
        .year-filter-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            font-family: Arial, sans-serif;
        }
        .year-filter-label {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }
        .year-filter-select {
            padding: 8px 16px;
            font-size: 16px;
            border: 2px solid #47c363;
            border-radius: 999px;
            background-color: #f9f9f9;
            color: #333;
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s;
        }
        .year-filter-select:hover,
        .year-filter-select:focus {
            border-color: #369d4b !important;
            background-color: #eaffea !important;
            box-shadow: 0 0 0 3px rgba(71, 195, 99, 0.4) !important;
            outline: none !important;
        }
    </style>
</head>
<body class="layout-4">
<div class="page-loader-wrapper">
    <span class="loader"><span class="loader-inner"></span></span>
</div>
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <?php include('includes/topnav.php'); ?>
        <?php include('includes/sidebar.php'); ?>
        <div class="main-content">
            <section class="section">
                <div class="section-header mb-4">
                    <h1 class="h3 text-dark">Products Analytics</h1>
                </div>
                <div class="section-body">
                    <!-- Submissions Over Time Chart -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-success text-white rounded-top d-flex justify-between align-center" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                                    <h5 class="mb-0">Number of Submissions Over Time</h5>
                                    <form method="GET" class="year-form" style="display: flex; align-items: center; gap: 10px;">
                                        <label for="year" class="year-filter-label" style="color: white; margin-bottom: 0;">Select Year:</label>
                                        <select name="year" id="year" onchange="this.form.submit()" class="year-filter-select">
                                            <?php foreach ($yearOptions as $year): ?>
                                                <option value="<?= $year ?>" <?= $year == $selectedYear ? 'selected' : '' ?>>
                                                    <?= $year ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div id="apex-timeline-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Liked Products Chart -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-success text-white rounded-top">
                                    <h5 class="mb-0">Top Liked Products</h5>
                                </div>
                                <div class="card-body">
                                    <div id="top-liked-products-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
</div>

<!-- JS Assets -->
<script src="assets/bundles/lib.vendor.bundle.js"></script>
<script src="js/CodiePie.js"></script>
<script src="assets/modules/chart.min.js"></script>
<script src="js/scripts.js"></script>

<script>
    // Submissions Over Time Chart
    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '45%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        series: [{
            name: 'Approved Products',
            data: <?= json_encode($approvedPerMonth) ?>
        }],
        xaxis: {
            categories: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ],
            labels: { style: { fontSize: '13px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            decimalsInFloat: 0,
            labels: { style: { fontSize: '13px' } }
        },
        grid: {
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } }
        },
        colors: ['#47c363']
    };
    new ApexCharts(document.querySelector("#apex-timeline-chart"), options).render();

    // Top Liked Products Chart
    var likedOptions = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '45%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        series: [{
            name: 'Likes',
            data: <?= json_encode($productLikes) ?>
        }],
        xaxis: {
            categories: <?= json_encode($productNames) ?>,
            labels: { style: { fontSize: '13px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            decimalsInFloat: 0,
            labels: { style: { fontSize: '13px' } }
        },
        grid: {
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } }
        },
        colors: ['#47c363']
    };
    new ApexCharts(document.querySelector("#top-liked-products-chart"), likedOptions).render();
</script>
</body>
</html>
