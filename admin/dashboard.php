<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';
$pageSubtitle = 'Welcome back, ' . ($_SESSION['admin_name'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <link rel="stylesheet" href="assets/css/admin-extra.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="dashboard-container">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <section class="dashboard-main">

            <?php include __DIR__ . '/includes/header.php'; ?>

            <!-- ========================= STATISTICS SECTION ======================== -->

            <section class="stats-section">

                <!-- Total Tours -->
                <div class="stat-card">

                    <div class="stat-icon tours-icon">
                        <i class="fa-solid fa-suitcase"></i>
                    </div>

                    <div class="stat-details">

                        <span class="stat-title">
                            Total Tours
                        </span>

                        <h2>42</h2>

                        <p class="stat-growth">
                            <i class="fa-solid fa-arrow-up"></i>
                            12%
                            <span>from last month</span>
                        </p>

                    </div>

                </div>


                <!-- Total Bookings -->
                <div class="stat-card">

                    <div class="stat-icon bookings-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <div class="stat-details">

                        <span class="stat-title">
                            Total Bookings
                        </span>

                        <h2>1,256</h2>

                        <p class="stat-growth">
                            <i class="fa-solid fa-arrow-up"></i>
                            18%
                            <span>from last month</span>
                        </p>

                    </div>

                </div>


                <!-- Total Customers -->
                <div class="stat-card">

                    <div class="stat-icon customers-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div class="stat-details">

                        <span class="stat-title">
                            Total Customers
                        </span>

                        <h2>856</h2>

                        <p class="stat-growth">
                            <i class="fa-solid fa-arrow-up"></i>
                            10%
                            <span>from last month</span>
                        </p>

                    </div>

                </div>


                <!-- Total Revenue -->
                <div class="stat-card">

                    <div class="stat-icon revenue-icon">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>

                    <div class="stat-details">

                        <span class="stat-title">
                            Total Revenue
                        </span>

                        <h2>₹28,65,000</h2>

                        <p class="stat-growth">
                            <i class="fa-solid fa-arrow-up"></i>
                            22%
                            <span>from last month</span>
                        </p>

                    </div>

                </div>

            </section>

            <!-- =========================================================
     LAST DASHBOARD SECTION
========================================================= -->

            <section class="dashboard-bottom">


                <!-- =====================================================
         RECENT BOOKINGS
    ====================================================== -->

                <div class="dashboard-card recent-bookings">

                    <div class="card-header">

                        <h3>Recent Bookings</h3>

                        <a href="#">View All</a>

                    </div>


                    <div class="booking-table-wrapper">

                        <table class="booking-table">

                            <thead>

                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Tour</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>

                            </thead>


                            <tbody>

                                <tr>

                                    <td>WC1001</td>

                                    <td>Rahul Sharma</td>

                                    <td>Kashmir Trip</td>

                                    <td>20 Aug 2024</td>

                                    <td>
                                        <span class="status confirmed">
                                            Confirmed
                                        </span>
                                    </td>

                                </tr>


                                <tr>

                                    <td>WC1002</td>

                                    <td>Simran Kaur</td>

                                    <td>Manali Escape</td>

                                    <td>21 Aug 2024</td>

                                    <td>
                                        <span class="status pending">
                                            Pending
                                        </span>
                                    </td>

                                </tr>


                                <tr>

                                    <td>WC1003</td>

                                    <td>Aman Verma</td>

                                    <td>Goa Beach</td>

                                    <td>22 Aug 2024</td>

                                    <td>
                                        <span class="status confirmed">
                                            Confirmed
                                        </span>
                                    </td>

                                </tr>


                                <tr>

                                    <td>WC1004</td>

                                    <td>Neha Singh</td>

                                    <td>Kerala Delight</td>

                                    <td>23 Aug 2024</td>

                                    <td>
                                        <span class="status cancelled">
                                            Cancelled
                                        </span>
                                    </td>

                                </tr>


                                <tr>

                                    <td>WC1005</td>

                                    <td>Vikram Patel</td>

                                    <td>Ladakh Bike Trip</td>

                                    <td>24 Aug 2024</td>

                                    <td>
                                        <span class="status pending">
                                            Pending
                                        </span>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>



                <!-- =====================================================
         RECENT REVIEWS
    ====================================================== -->

                <div class="dashboard-card recent-reviews">

                    <div class="card-header">

                        <h3>Recent Reviews</h3>

                        <a href="#">View All</a>

                    </div>


                    <div class="review-list">


                        <!-- REVIEW 1 -->

                        <div class="review-item">

                            <div class="review-top">

                                <img src="assets/adminImg/admin.png" alt="Rahul Sharma">

                                <div class="review-user">

                                    <strong>Rahul Sharma</strong>

                                    <span>Kashmir Trip</span>

                                </div>

                                <div class="stars">
                                    ★★★★★
                                </div>

                            </div>

                            <div class="review-date">
                                20 Aug 2024
                            </div>

                            <p>
                                Amazing experience! Everything was perfect.
                            </p>

                        </div>


                        <!-- REVIEW 2 -->

                        <div class="review-item">

                            <div class="review-top">

                                <img src="assets/adminImg/admin.png" alt="Simran Kaur">

                                <div class="review-user">

                                    <strong>Simran Kaur</strong>

                                    <span>Manali Escape</span>

                                </div>

                                <div class="stars">
                                    ★★★★★
                                </div>

                            </div>

                            <div class="review-date">
                                19 Aug 2024
                            </div>

                            <p>
                                Beautiful places and wonderful team.
                            </p>

                        </div>


                        <!-- REVIEW 3 -->

                        <div class="review-item">

                            <div class="review-top">

                                <img src="assets/adminImg/admin.png" alt="Aman Verma">

                                <div class="review-user">

                                    <strong>Aman Verma</strong>

                                    <span>Goa Beach Package</span>

                                </div>

                                <div class="stars">
                                    ★★★★<span>★</span>
                                </div>

                            </div>

                            <div class="review-date">
                                18 Aug 2024
                            </div>

                            <p>
                                Great trip, hotels could be better.
                            </p>

                        </div>


                    </div>

                </div>

            </section>
        </section>
    </div>
    <script src="assets/js/dashboard.js"></script>
</body>

</html>