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
                            <div class="page-title-box">
                                <h4 class="page-title">Set Schedule</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-12 col-lg-6">
                            <div class="col-md-12 w-100">
                                <div class="card w-100">
                                    <div class="card-header">
                                        <b>Schedule Data</b>
                                    </div>
                                    <div class="card-body">
                                        <div id="example-table"></div>
                                        <!-- <button id="file-load-trigger">Open File</button> -->
                                        <button id="download-csv" class="btn btn-primary mt-2">Download CSV</button>
                                        <button id="download-xlsx" class="btn btn-primary mt-2">Download XLSX</button>
                                        <button id="download-pdf" class="btn btn-primary mt-2">Download PDF</button>
                                        <button id="preview" class="btn btn-primary mt-2">Preview</button>
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
<script>
    var is_digit = /^\d+$/;
    var get_id = 0;

    function default_form() {
        var faculty_id = document.querySelector('input[name=faculty_id]');
        var name = document.querySelector('input[name=faculty_name]');
        var username = document.querySelector('input[name=username]');
        var email = document.querySelector('input[name=email]');
        var user_type = document.querySelector('select[name=user_type]');
        var add_user_account = document.getElementById('add_user_account');
        if (faculty_id) {
            faculty_id.value = "";
        }
        if (name) {
            name.value = "";
        }
        if (username) {
            username.value = "";
        }
        if (email) {
            email.value = "";
        }
        if (user_type) {
            user_type.value = "";
        }
        if (user_type == 'Faculty') {
            var department = document.querySelector('input[name=department]');
            var status = document.querySelector('select[name=status]');
            if (department) {
                department.value = "";
            }
            if (status) {
                status.value = "";
            }
        }
        get_id = 0;

        if (add_user_account) {
            add_user_account.innerHTML = "ADD";

        }

    }
    $(document).ready(function() {
        $('#add_new').on('click', function(e) {
            default_form();
        });

        $(document).on('click', '#add_user_account', function(e) {
            var faculty_id = $('.faculty_id').val();
            var user_type = $('.user_type').val();
            var name = $('.faculty_name').val();
            var username = $('.username').val();
            var email = $('.email').val();
            var department = $('.department').val();
            var status = $('.status').val();
            // console.log(faculty_id, user_type, name, username, email, get_id);

            if (user_type != 'Admin' || user_type != 'Faculty') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unknown error Occured',
                    timer: 5000,
                })
            }

            if (faculty_id == "" || user_type == "" || name == "" || username == "" || email == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Something went wrong',
                    text: 'Please fill all blanks',
                    timer: 5000,
                })
                return;
            } else {
                $.ajax({
                    url: "<?php echo BASE_URL ?>/app/add_edit_user.php",
                    method: "post",
                    data: {
                        faculty_id: faculty_id,
                        user_type: user_type,
                        name: name,
                        username: username,
                        email: email,
                        department: department,
                        status: status,
                        global_id: get_id,
                    },
                    success: function(data) {
                        var returnData = JSON.parse(data);
                        // console.log(returnData);
                        if (returnData.result == "add") {
                            // alert('SUCCESS DAW: ');
                            var res = jQuery.parseJSON(data);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: res.msg,
                                showCancelButton: false,
                                confirmButtonText: 'COPY!'
                            }).then((result) => {
                                navigator.clipboard.writeText(res.msg);

                            });
                            default_form();
                            table.replaceData();
                        } else if (returnData.result == "success") {
                            Swal.fire({
                                title: "Successfully Saved!",
                                text: returnData.msg,
                                icon: "success",
                                // button: false,
                            });
                            default_form();
                            table.replaceData();
                        } else if (returnData.result == "reset") {
                            Swal.fire({
                                title: "Password has been reset!",
                                text: returnData.msg,
                                icon: "success",
                                // button: false,
                            });
                            default_form();
                            table.replaceData();
                        } else {
                            // alert('ERROR DAW: '+ returnData.msg);
                            Swal.fire({
                                title: "Warning!",
                                text: returnData.msg,
                                icon: "warning",
                                button: false,
                                timer: 5000,
                            });
                        }
                    }
                });
            }
        });
    });


    //create Tabulator on DOM element with id "example-table"
    var btnedit = function(cell, formatterParams) { //edit DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        linkBut.innerHTML = "<button class='btn btn-outline-dark btn-rounded btn-sm ml-1' title='Update' ><i class='fas fa-pencil-alt' ></i> EDIT</button>";
        $(linkBut).on('click', () => {

            $('.new_field').remove();
            $('.new_field1').remove();

            var user_type = document.querySelector('select[name=user_type]');
            var faculty_id = document.querySelector('input[name=faculty_id]');
            var faculty_name = document.querySelector('input[name=faculty_name]');
            var username = document.querySelector('input[name=username]');
            var email = document.querySelector('input[name=email]');
            var add_user_account = document.getElementById('add_user_account');
            if (faculty_id) {
                faculty_id.value = cell.getData().faculty_id;
            }
            if (user_type) {
                user_type.value = cell.getData().user_type;
            }
            if (faculty_name) {
                faculty_name.value = cell.getData().faculty_name;
            }
            if (username) {
                username.value = cell.getData().username;
            }
            if (email) {
                email.value = cell.getData().email;
            }

            get_id = cell.getData().id;
            // console.log(cell.getData());


            if (add_user_account) {
                add_user_account.innerHTML = "UPDATE";
            }

        });
        return cellEl.appendChild(linkBut);


        // return '<a href="#" id="edit" class="btn btn-outline-dark btn-rounded btn-sm ml-1"> <i class="fas fa-plus"></i> Edit</a>';

    };
    var btndelete_data = function(cell, formatterParams) { //delete DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        if(session_id == ''){
            var session_id = '';
        }
        <?php if($session_sched != '') { ?>
        session_id = <?php echo $session_sched; ?>;
        <?php } ?>

        if (id != session_id) {
            linkBut.innerHTML = "<button class='btn btn-outline-danger btn-rounded btn-sm ml-1' title='Delete'><i class='fas fa-plus'></i> DELETE</button>";
        }

        $(linkBut).on('click', () => {
            get_id = cell.getData().id;
            // console.log(cell.getData());
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo BASE_URL ?>/app/delete_schedule.php",
                        method: "POST",
                        data: {
                            summary_id: get_id
                        },
                        success: function(data) {
                            var res = jQuery.parseJSON(data);
                            if (res.status == "400") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: res.message,
                                    timer: 5000,
                                })
                                table.setData();
                            } else {
                                // alert('ERROR DAW: '+ returnData.msg);
                                Swal.fire({
                                    title: "An Error Occured!",
                                    text: returnData.msg,
                                    icon: "warning",
                                    button: false,
                                    timer: 5000,
                                });
                            }
                        }
                    });

                }
            })

        });
        return cellEl.appendChild(linkBut);

        // return '<a id="edit" class="btn btn-outline-dark btn-rounded btn-sm ml-1"> <i class="fas fa-plus"></i> Delete</a>';
    };

    var btnsetsched = function(cell, formatterParams) { //delete DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        if(session_id == ''){
            var session_id = '';
        }
        <?php if($session_sched != '') { ?>
        session_id = <?php echo $session_sched; ?>;
        <?php } ?>

        if (id != session_id) {
            linkBut.innerHTML = "<button class='btn btn-outline-dark btn-rounded btn-sm ml-1' title='set_sched'>Set Schedule</button>";
        } else {
            return innerHTML = "SCHEDULE SET";
        }

        $(linkBut).on('click', () => {
            get_id = cell.getData().id;
            // console.log(cell.getData());
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to set this schedule!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Set'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo BASE_URL ?>/app/permanent_sched.php",
                        method: "POST",
                        data: {
                            sched_id: get_id
                        },
                        success: function(data) {
                            var returnData = JSON.parse(data);
                            console.log(returnData);
                            if (returnData.result == "success") {
                                Swal.fire({
                                    title: "Successfully Changed!",
                                    text: returnData.msg,
                                    icon: "success",
                                    button: false,
                                    timer: 5000,
                                }).then(function() {
                                    location.reload();
                                });
                            } else if (returnData.result == "info") {
                                Swal.fire({
                                    title: "Schedule",
                                    text: returnData.msg,
                                    icon: "info",
                                    button: false,
                                    timer: 5000,
                                });
                            } else {
                                // alert('ERROR DAW: '+ returnData.msg);
                                Swal.fire({
                                    title: "Something Went Wrong!",
                                    text: returnData.msg,
                                    icon: "warning",
                                    button: false,
                                    timer: 5000,
                                });
                            }
                        }
                    });
                }
            })
        });
        return cellEl.appendChild(linkBut);

        // return '<a id="edit" class="btn btn-outline-dark btn-rounded btn-sm ml-1"> <i class="fas fa-plus"></i> Delete</a>';
    };

    var table = new Tabulator("#example-table", {

        height: "500px",
        movableColumns: true,
        responsiveLayout: "hide",

        placeholder: "No Data Found",
        ajaxURL: "<?php echo BASE_URL . 'set_schedule_tabulator.php' ?>",
        ajaxSorting: true,
        ajaxFiltering: true,
        headerFilterPlaceholder: "",
        // layout: "fitColumns",
        layout: "fitDataStretch",
        ajaxProgressiveLoad: "scroll",
        ajaxProgressiveLoadScrollMargin: 2,
        ajaxLoaderLoading: 'Fetching data from Database..',
        paginationSize: <?php echo QUERY_LIMIT; ?>,

        columns: [ //Define Table Columns
            {
                title: "Title",
                field: "title",
                sorter: "string",
                width: "40%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Date created",
                field: "date_time",
                sorter: "string",
                width: "40%",
                headerFilter: "input",
                hozAlign: "center",
                // responsive: 3,

            },
            {
                formatter: btnsetsched,
                width: "10%",
                hozAlign: "center",
                download: false,
                cellClick: function(e, cell) {
                    cell.getRow();
                }
            },
            {
                formatter: btndelete_data,
                width: "10%",
                hozAlign: "center",
                download: false,
                cellClick: function(e, cell) {
                    cell.getRow();
                }
            },

        ],
        ajaxRequesting: function(url, params) {
            if (typeof this.modules.ajax.showLoader() != "undefined") {
                this.modules.ajax.showLoader();
            }
        },

    });


    //trigger download of data.csv file
    document.getElementById("download-csv").addEventListener("click", function() {
        table.download("csv", "schedule.csv");
    });


    //trigger download of data.xlsx file
    document.getElementById("download-xlsx").addEventListener("click", function() {
        table.download("xlsx", "schedule.xlsx", {
            sheetName: "My Data"
        });
    });

    //trigger download of data.pdf file
    document.getElementById("download-pdf").addEventListener("click", function() {
        table.download("pdf", "schedule.pdf", {
            orientation: "portrait", //set page orientation to portrait
            title: "Schedule", //add title to report

            autoTable: { //advanced table styling
                theme: 'grid',
                styles: {
                    pageBreak: 'always'
                },
                headStyles: {
                    fontStyle: 'bold',
                    minCellWidth: 55,
                    cellPadding: 1,
                    halign: 'center',
                    fontSize: 12,
                    font: 'courier'
                },
                bodyStyles: {
                    minCellWidth: 55,
                    cellPadding: 1,
                    halign: 'center',
                },
                columnStyles: {
                    course_title: {
                        cellWidth: 100
                    }
                }
            },
        });
    });

    document.getElementById("preview").addEventListener("click", function() {
        table.downloadToTab("pdf", "schedule.pdf", {
            orientation: "portrait", //set page orientation to portrait
            title: "Schedule", //add title to report
            autoTable: { //advanced table styling
                theme: 'grid',
                styles: {
                    pageBreak: 'always'
                },
                headStyles: {
                    fontStyle: 'bold',
                    minCellWidth: 55,
                    cellPadding: 1,
                    halign: 'center',
                    fontSize: 12,
                    font: 'courier'
                },
                bodyStyles: {
                    minCellWidth: 55,
                    cellPadding: 1,
                    halign: 'center',
                },
                columnStyles: {
                    course_title: {
                        cellWidth: 100
                    }
                }
            },
        });

    });

    // document.getElementById("file-load-trigger").addEventListener("click", function() {
    //     table.setDataFromLocalFile("json", ".json");
    // });
</script>

</html>