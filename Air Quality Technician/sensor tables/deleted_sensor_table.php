<?php     
    require_once('aq_sensor_connect.php');
    
    $rows_per_page = 10;

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $offset = ($page - 1) * $rows_per_page;

    $count_query = "SELECT COUNT(*) as count FROM deleted_aq_sensors";
    $count_result = mysqli_query($con, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_rows = $count_row['count'];

    $total_pages = ceil($total_rows / $rows_per_page);

    $sql = "SELECT daq.*, st.sensor_type_name, rn.room_num, rn.bldg_floor
            FROM deleted_aq_sensors daq
            LEFT JOIN room_number rn ON daq.deleted_aq_sensor_room_num = rn.room_num
            LEFT JOIN sensor_type st ON daq.deleted_aq_sensor_type_id = st.sensor_type_id
            GROUP BY daq.deleted_aq_sensor_id
            ORDER BY rn.bldg_floor DESC
            LIMIT $offset, $rows_per_page";
            
    $result_table = mysqli_query($con, $sql);

    while ($row = mysqli_fetch_assoc($result_table)){
        echo "<tr>";
        echo "<td>" . $row['bldg_floor'] . "</td>";
        echo "<td>" . $row['room_num'] . "</td>";
        echo "<td>" . $row['deleted_aq_sensor_id'] . "</td>";
        echo "<td>" . $row['deleted_aq_sensor_name'] . "</td>";
        echo "<td>" . $row['sensor_type_name'] . "</td>";
        echo "<td>" . $row['deleted_aq_sensor_add_at'] . "</td>";
        echo "<td>" . $row['deleted_aq_sensor_deleted_at'] . "</td>";
        echo '<td class="action-buttons">';
        echo '<div>';
        echo '<button class="restore-button" type="button" onclick="restoreRow(\'' . $row['deleted_aq_sensor_id'] . '\')"> 
                <i class="fas fa-rotate-left"></i></button>';
        echo '<button class="delete-button" type="button" onclick="deleteRow(\'' . $row['deleted_aq_sensor_id'] . '\')"> 
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

    $(document).ready(function () {
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
        data: { page: currentPage },
        success: function (data) {
            $('#sensor-manage-table').fadeOut('fast', function () {
                $(this).html(data).fadeIn('fast');
            });
        },
        error: function () {
            alert('Error loading table content.');
        }
    });
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
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    } else {
        window.location.reload();
    }
}

function deleteRow(sensorID) {
    if (confirm("Are you sure you want to delete this sensor?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "air_technician_deleted_sensor.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                window.location.reload();
            }
        };
        xhr.send("sensor_id=" + sensorID);
    }
}

function editRow(AQsensorId) {
	if (confirm("Do you want to edit this sensor?")){
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "fetch_aq_sensor_details.php"); // Replace with your edit page URL
    
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