<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">

    <!-- LOGO -->
    <a href="<?php echo BASE_URL ?>index.php" class="logo text-center logo-dark">
        <span class="logo-sm">
            <img src="<?php echo BASE_URL; ?>assets/images/ccc_logo.png" height="50px">
        </span>
    </a>

    <!-- LOGO -->
    <a href="<?php echo BASE_URL ?>index.php" class="logo text-center logo-dark">
        <span class="logo-lg">
            <img src="<?php echo BASE_URL; ?>assets/images/ccc_logo.png" height="50px" width="50px">
        </span>

    </a>
    <div class="h-100" id="leftside-menu-container" data-simplebar="">

        <!--- Sidemenu -->

        <ul class="side-nav">

            <li class="side-nav-title side-nav-item">Navigation</li>

            <?php if ($role == "Admin") { ?>
                <li class="side-nav-item">
                    <a href="user_account.php" class="side-nav side-nav-link d-flex justify-content-start">
                        <i class="mdi mdi-account-circle-outline"></i>
                        <span> User Account </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="faculty_list.php" class="side-nav side-nav-link d-flex justify-content-start">
                        <i class="dripicons-user-group"></i>
                        <span> Faculty List </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#schedule" aria-expanded="false" aria-controls="barangay" class="side-nav-link d-flex justify-content-start">
                        <i class="dripicons-to-do"></i>
                        <span> Schedule </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="schedule">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="class_sched.php">Class Schedule</a>
                            </li>

                            <li>
                                <a href="faculty_sched.php">Faculty Schedule</a>

                            </li>

                            <li>
                                <a href="room_assignment.php">Room Schedule</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a href="generate_sched.php" class="side-nav side-nav-link d-flex justify-content-start">
                        <i class="uil uil-calendar-alt"></i>
                        <span> Generate Sched </span>
                    </a>
                </li>
            <?php } else { ?>
                <li class="side-nav-item">
                    <a href="class_sched.php" class="side-nav side-nav-link d-flex justify-content-start">
                        <i class="dripicons-to-do"></i>
                        <span> Class Schedule </span>
                    </a>
                </li>
            <?php } ?>
            <li class="side-nav-item">
                <a href="set_schedule.php" class="side-nav side-nav-link d-flex justify-content-start">
                    <i class="dripicons-checklist"></i>
                    <span> Set Schedule </span>
                </a>
            </li>

        </ul>

    </div>


</div>
<!-- Sidebar -left -->