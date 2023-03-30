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

                                <h4 class="page-title">Faculty List</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-3 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    Faculty Form
                                </div>
                                <?php include('add_faculty.php'); ?>
                            </div>
                        </div>

                        <div class="col-xl-9 col-lg-6">
                            <div class="col-md-8 w-100">
                                <div class="card w-100">
                                    <div class="card-header">
                                        <b>Faculty Data</b>
                                    </div>
                                    <div class="m-2">
                                        <a id="add_new" class="btn btn-outline-dark btn-rounded btn-sm ml-1">
                                            <!-- <i class="fa-solid fa-plus"></i> -->Add New Faculty
                                        </a>
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
    var get_id = 0;

    function default_form() {
        var faculty_id = document.querySelector('input[name=faculty_id]');
        var name = document.querySelector('input[name=name]');
        var department = document.querySelector('input[name=department]');
        var email = document.querySelector('input[name=email]');
        var status = document.querySelector('select[name=status]');
        var add_faculty = document.getElementById('add_faculty');
        if (faculty_id) {
            faculty_id.value = "";
        }
        if (name) {
            name.value = "";
        }
        if (department) {
            department.value = "";
        }
        if (email) {
            email.value = "";
        }
        if (status) {
            status.value = "";
        }

        get_id = 0;

        if (add_faculty) {
            add_faculty.innerHTML = "ADD";

        }

    }
    $(document).ready(function() {
        $('#add_new').on('click', function(e) {
            default_form();

        });

        $('#add_faculty').on('click', function(e) {
            var faculty_id = $('.faculty_id').val();
            var name = $('.name').val();
            var department = $('.department').val();
            var email = $('.email').val();
            var status = $('.status').val();

            console.log(faculty_id, name, department, email, status);

            if (faculty_id == "" || name == "" || department == "" || email == "" || status == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Something went wrong',
                    text: 'Please fill all blanks!',
                    timer: 5000,
                })

                if (faculty_id == "") {
                    $("#faculty_id").addClass("red-border");
                } 
                if (faculty_id != "") {
                    $("#faculty_id").addClass("none-border");
                }
                if (name == "") {
                    $("#name").addClass("red-border");
                } 
                if (name != "") {
                    $("#name").addClass("none-border");
                }
                if (department == "") {
                    $("#department").addClass("red-border");
                } 
                if (department != "") {
                    $("#department").addClass("none-border");
                }
                if (email == "") {
                    $("#email").addClass("red-border");
                } 
                if (email != "") {
                    $("#email").addClass("none-border");
                }
                if (status == "" || status == null) {
                    $(".status").addClass("red-border");
                } else {
                    $(".status").addClass("none-border");
                }
                // if (status != "" || status != null) {
                //     $(".status").addClass("none-border");
                // }
                
            } else {
                $.ajax({
                    url: "<?php echo BASE_URL ?>/app/add_edit_faculty.php",
                    method: "post",
                    data: {
                        faculty_id: faculty_id,
                        name: name,
                        department: department,
                        email: email,
                        status: status,
                        global_id: get_id,
                    },
                    success: function(data) {
                        var returnData = JSON.parse(data);
                        // console.log(returnData);
                        if (returnData.result == "success") {
                            // alert('SUCCESS DAW: ');
                            Swal.fire({
                                title: "Data has been Altered!",
                                text: returnData.msg,
                                icon: "success",
                                // button: false,
                                timer: 5000,
                            });
                            default_form();
                            table.replaceData();
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
        });
    });


    //create Tabulator on DOM element with id "example-table"
    var btnedit = function(cell, formatterParams) { //edit DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        linkBut.innerHTML = "<button class='btn btn-outline-dark btn-rounded btn-sm ml-1' title='Update' ><i class='fas fa-pencil-alt' ></i> EDIT</button>";
        $(linkBut).on('click', () => {
            var faculty_id = document.querySelector('input[name=faculty_id]');
            var name = document.querySelector('input[name=name]');
            var department = document.querySelector('input[name=department]');
            var email = document.querySelector('input[name=email]');
            var status = document.querySelector('select[name=status]');
            var add_faculty = document.getElementById('add_faculty');
            if (faculty_id) {
                faculty_id.value = cell.getData().faculty_id;
            }
            if (name) {
                name.value = cell.getData().name;
            }
            if (department) {
                department.value = cell.getData().department;
            }
            if (email) {
                email.value = cell.getData().email;
            }
            if (status) {
                status.value = cell.getData().status;
            }

            get_id = cell.getData().id;
            // console.log(cell.getData());

            if (add_faculty) {
                add_faculty.innerHTML = "UPDATE";
            }

        });
        return cellEl.appendChild(linkBut);


        // return '<a href="#" id="edit" class="btn btn-outline-dark btn-rounded btn-sm ml-1"> <i class="fas fa-plus"></i> Edit</a>';

    };


    var btndelete_data = function(cell, formatterParams) { //delete DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        linkBut.innerHTML = "<button class='btn btn-outline-danger btn-rounded btn-sm ml-1' title='Delete'><i class='fas fa-plus'></i> DELETE</button>";
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
                        url: "<?php echo BASE_URL ?>/app/delete_faculty.php",
                        method: "POST",
                        data: {
                            faculty_id: get_id
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

    var table = new Tabulator("#example-table", {
        height: "500px",
        movableColumns: true,
        placeholder: "No Data Found",
        ajaxURL: "<?php echo BASE_URL . 'tabulator.php' ?>",
        ajaxSorting: true,
        ajaxFiltering: true,

        headerFilterPlaceholder: "",
        layout: "fitColumns",
        ajaxProgressiveLoad: "scroll",
        ajaxProgressiveLoadScrollMargin: 2,
        ajaxLoaderLoading: 'Fetching data from Database..',
        paginationSize: <?php echo QUERY_LIMIT; ?>,

        columns: [ //Define Table Columns

            {
                title: "Faculty ID",
                field: "faculty_id",
                sorter: "string",
                width: "15%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Name",
                field: "name",
                sorter: "string",
                width: "15%",
                headerFilter: "input",
                hozAlign: "center",
                responsive: 3,

            },
            {
                title: "Department",
                field: "department",
                width: "15%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Email",
                field: "email",
                sorter: "string",
                width: "15%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Status",
                field: "status",
                sorter: "string",
                width: "15%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                formatter: btnedit,
                width: "14%",
                hozAlign: "center",
                download: false,
                cellClick: function(e, cell) {
                    cell.getRow();
                }
            },
            {
                formatter: btndelete_data,
                width: "12%",
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
        table.download("csv", "faculty_list.csv");
    });


    //trigger download of data.xlsx file
    document.getElementById("download-xlsx").addEventListener("click", function() {
        table.download("xlsx", "faculty_list.xlsx", {
            sheetName: "My Data"
        });
    });

    //trigger download of data.pdf file
    document.getElementById("download-pdf").addEventListener("click", function() {
        table.download("pdf", "faculty_list.pdf", {
            orientation: "portrait", //set page orientation to portrait
            title: "Faculty List", //add title to report

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
        table.downloadToTab("pdf", "faculty_list.pdf", {
            orientation: "portrait", //set page orientation to portrait
            title: "Faculty List", //add title to report
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