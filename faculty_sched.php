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

                                <h4 class="page-title">Faulty Schedule</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="col-xl-12 col-lg-6">
                        <div class="col-md-8 w-100">
                            <div class="card w-100">
                                <div class="card-header">
                                    <b>Faculty Schedule List</b>
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
    var table = new Tabulator("#example-table", {
        height: "600px",
        movableColumns: true,
        placeholder: "No Data Found",
        ajaxURL: "class_sched_tabulator.php",
        ajaxSorting: true,
        ajaxFiltering: true,

        headerFilterPlaceholder: "",
        layout: "fitColumns",
        ajaxProgressiveLoad: "scroll",
        ajaxProgressiveLoadScrollMargin: 2,
        ajaxLoaderLoading: 'Fetching data from Database..',
        paginationSize: <?php echo 10000000; ?>,

        columns: [ //Define Table Columns
            {
                title: "Teacher",
                field: "name",
                sorter: "string",
                width: "15%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Course Code",
                field: "subject",
                sorter: "string",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Course Title",
                field: "course_title",
                sorter: "string",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Section",
                field: "section",
                sorter: "string",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center",
                responsive: 3,
            },
            {
                title: "Start From",
                field: "schedule_from",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center",
                formatter:"datetime",
                formatterParams:{inputFormat:"hh:mm A", outputFormat:"hh:mm A", invalidPlaceholder:"Invalid Time"},
                accessorDownload: standardtime
            },
            {
                title: "End",
                field: "schedule_to",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center",
                formatter:"datetime",
                formatterParams:{inputFormat:"hh:mm A", outputFormat:"hh:mm A", invalidPlaceholder:"Invalid Time"},
                accessorDownload: standardtime
            },
            {
                title: "Day",
                field: "day",
                sorter: "string",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Room",
                field: "room",
                sorter: "string",
                width: "14%",
                headerFilter: "input",
                hozAlign: "center"
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
        table.download("csv", "faculty_sched.csv");
    });


    //trigger download of data.xlsx file
    document.getElementById("download-xlsx").addEventListener("click", function() {
        table.download("xlsx", "faculty_sched.xlsx", {
            sheetName: "My Data"
        });
    });

    //trigger download of data.pdf file
    document.getElementById("download-pdf").addEventListener("click", function() {
        table.download("pdf", "faculty_sched.pdf", {
            orientation: "landscape", //set page orientation to portrait
            title: "Faculty Schedule", //add title to report
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
        table.downloadToTab("pdf", "faculty_sched.pdf", {
            orientation: "landscape", //set page orientation to portrait
            title: "Faculty Schedule", //add title to report
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
</script>