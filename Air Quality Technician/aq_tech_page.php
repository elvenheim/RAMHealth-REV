<?php require_once('../scripts/database_connect.php');?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Air Quality Technician</title>
        <link rel="stylesheet" href="../styles/general.css">
        <link rel="stylesheet" href="../styles/aqtech/main_page.css">
        <link rel="stylesheet" href="../styles/aqtech/aq_tech_table.css">
        <link rel="shortcut icon" href="../images/apc-logo.ico"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
        <script src="../scripts/logout.js"></script>
    </head>
    <body>
        <div class="navbar">
            <div class="logo-container" onclick="location.href='../Home/homepage.php';">
                <div class="ram-health-logo">RAM Health</div>
            </div>
            <div class="log-out-container">
                <div class="log-out">
                    <span id="log_out_dropdown" name="log_out_dropdown" class="log-out-symbol fas fa-power-off" 
                        onselectstart="return false;" onclick="collapse_logout()">
                    </span>
                </div>
                <ul id="btn_logout" class="log-out-display" style="display: none;">
                    <form id="logout" name="logout-form" method="POST" action="../Login/session_logout.php">
                        <button class="logout-button" type="submit" name="logout">
                            <span class="fas fa-power-off"></span>
                            Logout
                        </button>
                    </form>
                </ul>
            </div>
        </div>
        <div class="page-content">
            <div class="header-container">
                <span><a class="param-table" href="aq_tech_page.php">Air Quality Parameters</a></span>
                <span><a class="sensor-table" href="aq_tech_sensors.php">Sensors Management</a></span>
                <span><a class="deleted-sensor" href="aq_tech_deleted_sensors.php">Deleted Sensors</a></span>
            </div>
            <a style="opacity: 1;" id="add-user-btn" class="add-btn" href="../Air Quality Technician/aq tech script/add_aq_sensor.php">
                <span class="fas fa-plus"></span>
                <span style="display: inline-block;">Add Sensor</span>
            </a>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Building Floor</th>
                            <th>Facility</th>
                            <th>Sensor ID</th>
                            <th>Sensor Name</th>
                            <th>Sensor Type</th>
                            <th>Date Added</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sensor-manage-table">
                        <?php include '../Air Quality Technician/sensor tables/sensor_manage_table.php'?>
                    </tbody>
                </table>
                <ul id="pagination" class="pagination">
                </ul>
            </div>
        </div>
    </body>
</html>

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
        url: '../Air Quality Technician/sensor tables/sensor_manage_table.php',
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
                loadTableContent();
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