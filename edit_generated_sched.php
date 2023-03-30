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

                                <h4 class="page-title">Schedule</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="col-xl-12 col-lg-6">
                        <div class="col-md-8 w-100">
                            <div class="card w-100">
                                <div class="card-header">
                                    <b>Edit Schedule</b>
                                </div>
                                <div class="card-body">
                                    <div id="example-table"></div>
                                    <!-- <button id="file-load-trigger">Open File</button> -->
                                    <button id="download-csv" class="btn btn-primary mt-2">Download CSV</button>
                                    
                                    <button id="download-xlsx" class="btn btn-primary mt-2">Download XLSX</button>
                                    <button id="download-pdf" class="btn btn-primary mt-2">Download PDF</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="dark-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header modal-colored-header bg-dark">
                                    <h4 class="modal-title" style="color:white" id="dark-header-modalLabel">Update Schedule</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label class="control-label">Faculty ID</label>
                                                <input type="text" class="form-control faculty_id" name="faculty_id" id="faculty_id" readonly>
                                            </div>
                                            <div class="form-group col-6">
                                                <label class="control-label">Faculty Name</label>
                                                <input type="text" class="form-control teacher" name="teacher" id="teacher" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label class="control-label">Course Code</label>
                                                    <input type="text" class="form-control course_code" name="course_code" id="course_code">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Course Title</label>
                                                    <input type="text" class="form-control course_title" name="course_title" id="course_title">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Section</label>
                                                    <input type="text" class="form-control section" name="section" id="section">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Day</label>
                                                    <!-- <select name="day" id="day" class="form-select day">
                                                        <option value="" hidden selected> ~ Choose Day ~</option>
                                                        <?php $day = ['M', 'T', 'W', 'TH', 'F', 'S', 'SU'];
                                                        foreach ($day as $value) { ?>
                                                            <option value="<?= $value ?>"><?= $value ?></option>
                                                        <?php } ?>
                                                    </select> -->
                                                    <input type="text" class="form-control day" name="day" id="day">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">From Time</label>
                                                    <input type="text" class="form-control from_time" name="from_time" id="from_time">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">To Time</label>
                                                    <input type="text" class="form-control to_time" name="to_time" id="to_time">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <a type="button" name="update_sched" id="update_sched" class="btn btn-dark">Save changes</a>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

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
    var btnedit = function(cell, formatterParams) { //edit DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        linkBut.innerHTML = "<button type='button' class='btn btn-outline-dark btn-rounded btn-sm ml-1' data-bs-toggle='modal' data-bs-target='#dark-header-modal' ><i class='fas fa-pencil-alt' ></i> EDIT</button>";
        $(linkBut).on('click', () => {
            var faculty_id = document.querySelector('input[name=faculty_id]');
            var teacher = document.querySelector('input[name=teacher]');
            var course_code = document.querySelector('input[name=course_code]');
            var course_title = document.querySelector('input[name=course_title]');
            var section = document.querySelector('input[name=section]');
            var day = document.querySelector('input[name=day]');
            var from_time = document.querySelector('input[name=from_time]');
            var to_time = document.querySelector('input[name=to_time]');
            if (faculty_id) {
                faculty_id.value = cell.getData().faculty_id;
            }
            if (teacher) {
                teacher.value = cell.getData().teacher;
            }
            if (course_code) {
                course_code.value = cell.getData().course_code;
            }
            if (course_title) {
                course_title.value = cell.getData().course_title;
            }
            if (section) {
                section.value = cell.getData().section;
            }
            if (day) {
                day.value = cell.getData().day;
            }
            if (from_time) {
                from_time.value = cell.getData().from_time;
            }
            if (to_time) {
                to_time.value = cell.getData().to_time;
            }

            get_id = cell.getData().id;
            console.log(cell.getData());

        });

        return cellEl.appendChild(linkBut);

    };

    var btndelete_data = function(cell, formatterParams) { //delete DOM element
        var cellEl = cell.getElement(); //get cell DOM element
        var linkBut = document.createElement("span");
        var id = cell.getData().id;
        linkBut.innerHTML = "<button class='btn btn-outline-dark btn-rounded btn-sm ml-1' title='Delete'><i class='fas fa-plus'></i> DELETE</button>";
        $(linkBut).on('click', () => {
            get_id = cell.getData().id;
            console.log(cell.getData());
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
                        url: "<?php echo BASE_URL ?>/app/delete_generated_sched.php",
                        method: "POST",
                        data: {
                            faculty_id: get_id
                        },
                        success: function(data) {
                            var res = jQuery.parseJSON(data);
                            if (res.status == "400") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
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
        // ajaxSorting: true,
        // ajaxFiltering: true,
        height: "500px",
        // tooltips: true,
        // printAsHtml: true,
        movableColumns: true,
        // selectable: true,
        //layout: "fitDataTable",
        // paginationSizeSelector: [10, 20, 30, 40, 50],
        placeholder: "No Data Found",
        ajaxURL: "edit_tabulator.php",
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
                width: "10%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Teacher",
                field: "teacher",
                sorter: "string",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Course Code",
                field: "course_code",
                sorter: "string",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Course Title",
                field: "course_title",
                sorter: "string",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Section",
                field: "section",
                sorter: "string",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center",
                responsive: 3,
            },
            {
                title: "Day",
                field: "day",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "From Time",
                field: "from_time",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "To Time",
                field: "to_time",
                sorter: "string",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                formatter: btnedit,
                width: "10%",
                hozAlign: "center",
                cellClick: function(e, cell) {
                    cell.getRow();
                }
            },
            {
                formatter: btndelete_data,
                width: "10%",
                hozAlign: "center",
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

    // 
    //trigger download of data.csv file
    document.getElementById("download-csv").addEventListener("click", function() {
        table.download("csv", "data.csv");
    });


    //trigger download of data.xlsx file
    document.getElementById("download-xlsx").addEventListener("click", function() {
        table.download("xlsx", "data.xlsx", {
            sheetName: "My Data"
        });
    });

    //trigger download of data.pdf file
    document.getElementById("download-pdf").addEventListener("click", function() {
        table.download("pdf", "data.pdf", {
            orientation: "landscape", //set page orientation to portrait
            title: "Schedule", //add title to report
        });
    });
    $(document).on('click', '#update_sched', function(e) {
        var faculty_id = $('.faculty_id').val();
        var teacher = $('.teacher').val();
        var course_code = $('.course_code').val();
        var course_title = $('.course_title').val();
        var section = $('.section').val();
        var day = $('.day').val();
        var from_time = $('.from_time').val();
        var to_time = $('.to_time').val();

        console.log(faculty_id, teacher, course_code, course_title, section, day, from_time, to_time);

        if (faculty_id == "" || teacher == "" || course_code == "" || course_title == "" || section == "" || day == "" || from_time == "" || to_time == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Something went wrong',
                text: 'Please fill all blanks!',
                timer: 5000,
            })
        } else {
            $.ajax({
                url: "<?php echo BASE_URL ?>/app/update_sched.php",
                method: "POST",
                data: {
                    faculty_id: faculty_id,
                    teacher: teacher,
                    course_code: course_code,
                    course_title: course_title,
                    section: section,
                    day: day,
                    from_time: from_time,
                    to_time: to_time
                },
                success: function(response) {
                    var returnData = JSON.parse(response);
                    console.log(returnData);
                    if (returnData.result == "success") {
                        Swal.fire({
                            title: "Data has been Altered!",
                            text: returnData.msg,
                            icon: "success",
                            timer: 5000,
                        });
                        $('#dark-header-modal').modal('hide');
                        table.replaceData();
                    } else {
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
</script>