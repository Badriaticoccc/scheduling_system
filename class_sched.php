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

                                <h4 class="page-title">Class Schedule</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="col-xl-12 col-lg-6">
                        <div class="col-md-8 w-100">
                            <div class="card w-100">
                                <div class="card-header">
                                    <b>Class Schedule List</b>
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

        height: "500px",
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
                title: "Section",
                field: "section",
                sorter: "string",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center",
                responsive: 3,
            },
            {
                title: "Course Code",
                field: "subject",
                sorter: "string",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Course Title",
                field: "course_title",
                sorter: "string",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center",
            },
            {
                title: "Start From",
                field: "schedule_from",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center",
                formatter: "datetime",
                formatterParams: {
                    inputFormat: "hh:mm A",
                    outputFormat: "hh:mm A",
                    invalidPlaceholder: "Invalid Time"
                },
                accessorDownload: standardtime
            },
            {
                title: "End",
                field: "schedule_to",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center",
                formatter: "datetime",
                formatterParams: {
                    inputFormat: "hh:mm A",
                    outputFormat: "hh:mm A",
                    invalidPlaceholder: "Invalid Time"
                },
                accessorDownload: standardtime
            },
            {
                title: "Days",
                field: "day",
                sorter: "string",
                width: "10%",
                headerFilter: "input",
                hozAlign: "center"

            },
            {
                title: "Room",
                field: "room",
                sorter: "string",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Unit",
                field: "unit",
                sorter: "string",
                width: "11%",
                headerFilter: "input",
                hozAlign: "center"
            },
            {
                title: "Teacher",
                field: "name",
                sorter: "string",
                width: "12%",
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

    // function standardtime(value, data, type, component, row) {


    //     value = value.split(':'); // convert to array

    //     // fetch
    //     var hours = Number(value[0]);
    //     var minutes = Number(value[1]);
    //     var seconds = Number(value[2]);

    //     // calculate
    //     var timeValue;

    //     if (hours > 0 && hours <= 12) {
    //         timeValue = "" + hours;
    //     } else if (hours > 12) {
    //         timeValue = "" + (hours - 12);
    //     } else if (hours == 0) {
    //         timeValue = "12";
    //     }

    //     timeValue += (minutes < 10) ? ":0" + minutes : ":" + minutes; // get minutes
    //     timeValue += (hours >= 12) ? " PM" : " AM"; // get AM/PM
    //     return timeValue;
    // }



    //trigger download of data.csv file
    document.getElementById("download-csv").addEventListener("click", function() {
        table.download("csv", "class_sched.csv");
    });

    //trigger download of data.xlsx file
    document.getElementById("download-xlsx").addEventListener("click", function() {
        table.download("xlsx", "class_sched.xlsx", {
            sheetName: "My Data"
        });
    });


    //trigger download of data.pdf file
    document.getElementById("download-pdf").addEventListener("click", function() {
        table.download("pdf", "class_sched.pdf", {
            orientation: "landscape", //set page orientation to landscape
            title: "Class Schedule", //add title to report
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
        table.downloadToTab("pdf", "class_sched.pdf", {
            orientation: "landscape", //set page orientation to portrait
            title: "Class Schedule", //add title to report
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