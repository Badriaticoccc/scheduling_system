<script src="<?php echo BASE_URL; ?>assets/js/vendor.min.js?v=<?php echo FILE_VERSION ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/app.min.js?v=<?php echo FILE_VERSION ?>"></script>

<!-- third party js -->
<script src="<?php echo BASE_URL; ?>assets/js/vendor/jquery-jvectormap-1.2.2.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/vendor/jquery-jvectormap-world-mill-en.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/select2.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/fullcalendar/main.js?v=<?php echo FILE_VERSION; ?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/csvtojson.js?v=<?php echo FILE_VERSION; ?>"></script>

<!-- third party js ends -->

<script src="<?php echo BASE_URL; ?>assets/js/standardtime.js?v=<?php echo FILE_VERSION; ?>"></script>

<script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/tabulator.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/xlsx.full.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jspdf.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jspdf.plugin.autotable.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sweetalert.min.js?v=<?php echo FILE_VERSION; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/underscore-min.js?v=<?php echo FILE_VERSION; ?>"></script>

<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/csvtojson.js?v=<?php echo FILE_VERSION ?>"></script>
<script src="<?php echo BASE_URL ?>assets/js/sweetalert.js?v=<?php echo FILE_VERSION ?>"></script>

<script>
    const wait = (delay = 0) =>
        new Promise(resolve => setTimeout(resolve, delay));

    const setVisible = (elementOrSelector, visible) =>
        (typeof elementOrSelector === 'string' ?
            document.querySelector(elementOrSelector) :
            elementOrSelector
        ).style.display = visible ? 'block' : 'none';

    setVisible('.page', false);
    setVisible('#loading', true);

    document.addEventListener('DOMContentLoaded', () =>
        wait(1000).then(() => {
            setVisible('.page', true);
            setVisible('#loading', false);
        }));
</script>