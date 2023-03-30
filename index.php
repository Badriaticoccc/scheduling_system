<?php
require 'config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;
include ISLOGIN;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include DOMAIN_PATH . "/app/global/meta_data.php";
    include DOMAIN_PATH . "/app/global/include_top.php";
    ?>
</head>

<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <div class="page"></div>
    <div id="loading"></div>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- SIDE VAR -->
        <?php
        include DOMAIN_PATH . "/app/global/side_var.php";
        ?>
        <!-- END SIDE VAR -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <?php
                include DOMAIN_PATH . "/app/global/top_var.php";
                ?>

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box mb-5">
                                <br>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-5 mt-5 col-lg-6 d-flex justify-content-center align-items-center">
                            <img src="<?php echo BASE_URL; ?>assets/images/ccc_logo.png" height="543" width="555">
                        </div>

                        <div class="col-xl-7 col-lg-6 d-flex justify-content-center align-items-center">
                            <div class="col-md-8 w-100">

                                <div class="sppb-column-addons">
                                    <div id="sppb-addon-wrapper-1484546085711" class="sppb-addon-wrapper">
                                        <div id="sppb-addon-1484546085711" class="clearfix ">
                                            <div class="sppb-addon sppb-addon-text-block 0 sppb-text-left ">
                                                <h3 class="sppb-addon-title">The CITY COLLEGE OF CALAMBA</h3>
                                                <div class="sppb-addon-content">
                                                    <p>City College of Calamba (CCC) is a public higher education institution in Laguna established in 2006 coinciding with the country's national hero, Jose Rizal's 145th birth anniversary. The institution was founded to provide low-cost and high-quality education to underprivileged. It is also subsidized by the city government and offers free tuition.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="sppb-addon-wrapper-1484546085714" class="sppb-addon-wrapper">
                                        <div id="sppb-addon-1484546085714" class="clearfix ">
                                            <div class="sppb-addon sppb-addon-text-block 0 sppb-text-left ">
                                                <h3 class="sppb-addon-title">VISION &amp; MISSION</h3>
                                                <div class="sppb-addon-content">
                                                    <p>The City College of Calamba envisions itself as an accredited premier academic institution in the region, providing quality learning opportunities to financially challenged but deserving students in order to produce competent, conscientious, committed, and compassionate global professionals.</p>
                                                    <p>&nbsp;</p>
                                                    <p>In pursuit of this vision, WE, the faculty, staff, and students of the City College of Calamba recognize our vital roles in collaboratively honing the professional by promoting social responsibility, moral uprightness, and national servitude, guided by the ideals, philosophies, and values of our national hero, Dr. Jose Rizal.</p>
                                                    <p>&nbsp;</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- end row -->


                </div>
                <!-- container -->

            </div>
            <!-- content -->

            <!-- Footer Start -->
            <?php include DOMAIN_PATH . "/call_func/footer.php"; ?>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <?php include DOMAIN_PATH . "/app/global/include_buttom.php"; 
    include DOMAIN_PATH . "/app/set_sched.php";
    
    ?>


</body>

</html>