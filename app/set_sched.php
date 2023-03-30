<script>
    $('#set_sched').on('click', function() {
        <?php 
            $sql = mysqli_query($db_connect, "SELECT id,title FROM permanent_sched_summary ORDER BY date_time DESC");
        
        ?>
            (async () => {

                var htmlContent = "<select class='swal2-input' id='swal'>" +
                    "<?php 
                            
                            echo "<option hidden value=" . $session_sched . ">" . $session_title . "</option>";
                            while ($row = mysqli_fetch_assoc($sql)) {
                                echo "<option value=" . $row['id'] . ">" . $row['title'] . "</option>";
                            }
                        
                        //     echo "<option value=''>NO DATA</option>";
                        // } ?></select>";

                const {
                    value: formValues
                } = await Swal.fire({
                    title: 'SET SCHEDULE',
                    html: htmlContent,
                    showCancelButton: true,
                    showConfirmButton: true,
                    preConfirm: () => {
                        return [
                            document.getElementById('swal').value,
                        ]
                    }
                })
                if (formValues == '') {
                    Swal.fire({
                        title: "Something Went Wrong!",
                        text: 'No data found',
                        icon: "warning",
                        button: false,
                        timer: 5000,
                    });
                    return;
                }
                if (formValues) {
                    var data_a = $('#swal').val();

                    console.log(data_a);
                    // Swal.fire(JSON.stringify(formValues))
                    $.ajax({
                        url: "<?php echo BASE_URL ?>/app/permanent_sched.php",
                        method: "post",
                        data: {
                            data_a: data_a,

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
            })()
    });
</script>