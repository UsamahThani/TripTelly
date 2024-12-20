<?php
session_start();
include 'fn_adminTelly.php';
include 'dbconnect.php';
$pageTitle = "Write at line 5";

if (!isset($_SESSION["adminID"])) {
    header("../index.php");
}

$adminData = fetchCurrentAdminData($_SESSION['adminID']);

?>
<html lang="en">

<head>
    <?php include "view_head.php" ?>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <?php include "view_adminNavbar.php" ?>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include "view_adminSidebar.php" ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <?php
                // for testing purpose. comment when not use
                // var_dump($adminData)
                include "view_toaster.php";
                ?>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Title</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">From</li>
                        <li class="breadcrumb-item active">Current</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Card Name</h4>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <?php include "view_adminFooter.php" ?>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>