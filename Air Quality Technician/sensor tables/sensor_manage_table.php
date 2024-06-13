<?php
require_once('aq_sensor_connect.php');

$rows_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;
$floor_by = isset($_GET['floor_by']) ? $_GET['floor_by'] : 'none';
$sensor_by = isset($_GET['sensor_by']) ? $_GET['sensor_by'] : 'none';

$conditions = [];

if ($floor_by !== 'none') {
    $conditions[] = "rn.bldg_floor = '$floor_by'";
}

if ($sensor_by) {
    if ($sensor_by === 'all') {
        $conditions[] = "st.sensor_type_id IN (1, 2, 3, 4, 5)";
    } else {
        $conditions[] = "st.sensor_type_id = '$sensor_by'";
    }
}

$where_clause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$count_query = "SELECT COUNT(*) as count 
                    FROM aq_sensor aq 
                    LEFT JOIN room_number rn ON aq.aq_sensor_room_num = rn.room_num 
                    LEFT JOIN sensor_type st ON aq.aq_sensor_type = st.sensor_type_id 
                    $where_clause";
$count_result = mysqli_query($con, $count_query);

if (!$count_result) {
    die("Query failed: " . mysqli_error($con));
}

$count_row = mysqli_fetch_assoc($count_result);
$total_rows = $count_row['count'];
$total_pages = ceil($total_rows / $rows_per_page);

$sql = "SELECT aq.*, st.sensor_type_name, rn.room_num, rn.bldg_floor
                FROM aq_sensor aq
                LEFT JOIN room_number rn ON aq.aq_sensor_room_num = rn.room_num
                LEFT JOIN sensor_type st ON aq.aq_sensor_type = st.sensor_type_id
                $where_clause
                ORDER BY rn.room_num ASC, aq.aq_sensor_status DESC
                LIMIT $offset, $rows_per_page";

$result_table = mysqli_query($con, $sql);

if (!$result_table) {
    die("Query failed: " . mysqli_error($con));
}

while ($row = mysqli_fetch_assoc($result_table)) {
    echo "<tr" . ($row['aq_sensor_status'] == 0 ? " class=\"disabled\"" : '') . ">";
    echo '<td style="width: 100px">' . $row['bldg_floor'] . "</td>";
    echo "<td>" . $row['room_num'] . "</td>";
    echo "<td>" . $row['aq_sensor_id'] . "</td>";
    echo "<td>" . $row['aq_sensor_name'] . "</td>";
    echo "<td>" . $row['sensor_type_name'] . "</td>";
    echo "<td>" . $row['aq_sensor_added_at'] . "</td>";
    echo "<td>";
    echo '<form class="status-form">';
    echo '<input type="hidden" name="aq_sensor_id" value="' . $row['aq_sensor_id'] . '">';
    echo '<select name="aq_sensor_status" onchange="updateStatus(this.form);">';
    echo '<option class="status-enabled" value="1"' . ($row['aq_sensor_status'] == 1 ? ' selected' : '') . '>Enabled</option>';
    echo '<option class="status-disabled" value="0"' . ($row['aq_sensor_status'] == 0 ? ' selected' : '') . '>Disabled</option>';
    echo '</select>';
    echo '</form>';
    echo "</td>";
    echo '<td class="action-buttons">';
    echo '<div>';
    echo '<button class="edit-button" type="button" onclick="editRow(\'' . $row['aq_sensor_id'] . '\')"> 
                        <i class="fas fa-edit"></i></button>';
    echo '<button class="delete-button" type="button" onclick="deleteRow(\'' . $row['aq_sensor_id'] . '\')"> 
                        <i class="fas fa-trash"></i></button>';
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
    var floorBy = '<?php echo $floor_by; ?>';
    var sensorBy = '<?php echo $sensor_by; ?>';

    $(document).ready(function() {
        updatePagination();
    });

    function updatePagination() {
        var paginationHtml = '';
        var maxButtons = 5;

        if (totalPages > 1) {
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
        }

        $('#pagination').html(paginationHtml);
    }

    function loadPage(page) {
        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        floorBy = $('#floor-filter').val();
        sensorBy = $('#sensor-type-filter').val();

        currentPage = page;
        updatePagination();
        loadTableContent(page, floorBy, sensorBy);

        // Prevent default behavior of anchor links
        event.preventDefault();
    }

    function loadTableContent(page, floorBy, sensorBy) {
        $.ajax({
            url: '../Air Quality Technician/sensor tables/sensor_manage_table.php',
            type: 'GET',
            data: {
                page: page,
                floor_by: floorBy,
                sensor_by: sensorBy
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

    function sortFloor() {
        currentPage = 1; // Reset to the first page when sorting
        floorBy = $('#floor-filter').val();
        sensorBy = $('#sensor-type-filter').val();
        updatePagination();
        loadTableContent(currentPage, floorBy, sensorBy);
    }

    function sortSensorType() {
        currentPage = 1; // Reset to the first page when sorting
        floorBy = $('#floor-filter').val();
        sensorBy = $('#sensor-type-filter').val();
        updatePagination();
        loadTableContent(currentPage, floorBy, sensorBy);
    }
</script>

<!-- other table scripts here -->
<script>
    function updateStatus(form) {
        var formData = $(form).serialize();
        var originalStatus = $(form).find('select[name="aq_sensor_status"]').data('original-status');

        if (confirm("Are you sure you want to update the sensor status?")) {
            $.ajax({
                url: '../Air Quality Technician/sensor tables/update_status.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        var statusSelect = $(form).find('select[name="aq_sensor_status"]');
                        if (response.aq_sensor_status == 1) {
                            statusSelect.css('background-color', '#646467');
                        } else {
                            statusSelect.css('background-color', '#ccc');
                        }
                        statusSelect.data('original-status', response.aq_sensor_status);
                    }
                    loadTableContent(currentPage, floorBy, sensorBy);
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                }
            });
        } else {
            loadTableContent(currentPage, floorBy, sensorBy);
        }
    }

    function deleteRow(sensorID) {
        if (confirm("Are you sure you want to delete this sensor?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Air Quality Technician/sensor tables/aq_delete_sensor.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText);
                    loadTableContent(currentPage, floorBy, sensorBy);
                }
            };
            xhr.send("sensor_id=" + sensorID);
        }
    }

    function editRow(AQsensorId) {
        if (confirm("Do you want to edit this sensor?")) {
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "../Air Quality Technician/sensor tables/edit_sensor_page.php"); // Replace with your edit page URL

            // Create a hidden input field to pass the employee ID
            var input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", "aq_sensor_id");
            input.setAttribute("value", AQsensorId);

            // Append the input field to the form
            form.appendChild(input);

            // Append the form to the document body
            document.body.appendChild(form);

            // Submit the form
            form.submit();
        }
    }
</script>