<?php
    require_once('ec_sensor_connect.php');

    $rows_per_page = 10;

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $offset = ($page - 1) * $rows_per_page;

    $count_query = "SELECT COUNT(*) as count FROM deleted_aq_sensors";
    $count_result = mysqli_query($con, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_rows = $count_row['count'];

    $total_pages = ceil($total_rows / $rows_per_page);

    $sql ="SELECT decs.*, epg.ec_panel_grouping_id, epl.ec_panel_label_id, bf.bldg_floor_name, decs.arduino_room_num,
                easl.ec_arduino_sensor_label_id, st.sensor_type_name
        FROM deleted_ec_sensors AS decs
        LEFT JOIN ec_arduino_sensor_linking AS easl ON decs.ec_arduino_sensor_id = easl.ec_arduino_deleted_sensor

        LEFT JOIN ec_panel_grouping epg ON easl.ec_panel_grouping_id = epg.ec_panel_grouping_id 
        LEFT JOIN ec_panel_label epl ON easl.ec_panel_label_id = epl.ec_panel_label_id 
        LEFT JOIN ec_arduino_label_sensor eals ON easl.ec_arduino_sensor_label_id = eals.ec_arduino_sensor_label_id 
        LEFT JOIN ec_arduino_sensors eas ON easl.ec_arduino_sensors_id = eas.ec_arduino_sensors_id

        LEFT JOIN room_number rn ON decs.arduino_bldg_floor = rn.bldg_floor AND decs.arduino_room_num = rn.room_num
        LEFT JOIN building_floor bf ON rn.bldg_floor = bf.building_floor 
        LEFT JOIN sensor_type st ON decs.arduino_sensors_type = st.sensor_type_id
        ORDER BY bf.building_floor ASC
        LIMIT $offset, $rows_per_page";

    $result_table = mysqli_query($con, $sql);

    while ($row = mysqli_fetch_assoc($result_table)){
        echo '<tr data-sensor-id="' . $row['ec_arduino_sensor_id'] . '"' . '>';
        echo "<td>" . $row['ec_panel_grouping_id'] . "</td>";
        echo "<td>" . $row['ec_panel_label_id'] . "</td>";
        echo "<td>" . $row['bldg_floor_name'] . "</td>";
        echo "<td>" . $row['arduino_room_num'] . "</td>";
        echo "<td>" . $row['ec_arduino_sensor_label_id'] . "</td>";
        echo "<td>" . $row['ec_arduino_sensor_id'] . "</td>";
        echo "<td>" . $row['sensor_type_name'] . "</td>";
        echo "<td>" . $row['arduino_sensors_added_at'] . "</td>";
        echo "<td>" . $row['arduino_sensors_deleted_at'] . "</td>";
        echo '<td class="action-buttons">';
        echo '<div>';
        echo '<button class="restore-button" type="button" onclick="restoreRow(\'' . $row['ec_arduino_sensor_id'] . '\')"> 
                <i class="fas fa-rotate-left"></i></button>';
        echo '</div>';
        echo "</td>";
        echo "</tr>";
    }
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script for pagination -->
<script>
    var currentPage = <?php echo $page; ?>;
    var totalPages = <?php echo $total_pages; ?>;

    $(document).ready(function() {
        updatePagination();
    });

    function updatePagination() {
        var paginationHtml = '';
        var maxButtons = 5;

        var startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
        var endPage = Math.min(totalPages, startPage + maxButtons - 1);

        if (endPage - startPage + 1 < maxButtons) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }

        // Adjust start and end page based on the total number of pages
        if (totalPages - endPage < Math.floor(maxButtons / 2)) {
            startPage = Math.max(1, startPage - (Math.floor(maxButtons / 2) - (totalPages - endPage)));
        }

        // Previous button
        paginationHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '"><a class="page-link previous" href="#" onclick="loadPage(' + (currentPage - 1) + ')">Previous</a></li>';

        for (var i = startPage; i <= endPage; i++) {
            paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '"><a class="page-link number" href="#" onclick="loadPage(' + i + ')">' + i + '</a></li>';
        }

        // Next button
        paginationHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '"><a class="page-link next" href="#" onclick="loadPage(' + (currentPage + 1) + ')">Next</a></li>';

        $('#pagination').html(paginationHtml);
    }


    function loadPage(page) {
        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        currentPage = page;
        updatePagination();
        loadTableContent();

        // Prevent default behavior of anchor links
        event.preventDefault();
    }

    function loadTableContent() {
        $.ajax({
            url: '../Air Quality Technician/sensor tables/deleted_sensor_table.php',
            type: 'GET',
            data: {
                page: currentPage
            },
            success: function(data) {
                $('#sensor-manage-table').fadeOut('fast', function() {
                    $(this).html(data).fadeIn('fast');
                });
            },
            error: function() {
                alert('Error loading table content.');
            }
        });
    }
</script>

<!-- other table scripts here -->
<script>
    function restoreRow(sensorID) {
        if (confirm("Are you sure you want to restore this sensor?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Air Quality Technician/sensor tables/aq_sensor_restore.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        alert("Sensor has been successfully restored.");
                        loadTableContent();
                    } else {
                        alert("Error deleting sensor: " + xhr.responseText);
                    }
                }
            };
            xhr.send("sensor_id=" + sensorID);
        }
    }
</script>