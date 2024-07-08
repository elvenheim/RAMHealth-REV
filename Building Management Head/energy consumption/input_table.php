<?php
require_once('ec_connect.php');

// Initialize the selected table and selected columns variables
$selected_table = '';
$selected_columns = [];

// Check if there is a table selected in the URL
if (isset($_GET['table'])) {
    $selected_table = $_GET['table'];
}

// Function to fetch column names from a given table
function getColumnNames($tableName, $con2)
{
    $columnQuery = "SHOW COLUMNS FROM `$tableName`";
    $columnResult = mysqli_query($con2, $columnQuery);

    if (!$columnResult) {
        die("Error fetching columns: " . mysqli_error($con2));
    }

    $columns = [];
    while ($row = mysqli_fetch_assoc($columnResult)) {
        $columns[] = $row['Field'];
    }

    return $columns;
}

// Query to get all table names in the database
$tableQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'ramhealth_energy'";
$tableResult = mysqli_query($con2, $tableQuery);

if (!$tableResult) {
    die("Database query failed: " . mysqli_error($con2));
}
?>

<div class="sorting-dropdown" style="margin-left: 10px; margin-right: 10px;">
    <label for="table-by">Choose Table:</label>
    <select id="table-by" onchange="chooseTable()">
        <option value="" selected disabled>Select Table</option>
        <?php
        while ($row = mysqli_fetch_assoc($tableResult)) {
            $table_name = $row['table_name'];
            $selected = ($table_name === $selected_table) ? 'selected' : '';
            echo '<option value="' . $table_name . '" ' . $selected . '>' . $table_name . '</option>';
        }
        ?>
    </select>
</div>


<div class="sorting-dropdown">
    <form id="column-form">
        <div class="dropdown" style="position: relative; display: inline-block;">
            <button id="room-by" type="button" class="dropbtn">Select Room</button>
            <div class="dropdown-content" style="display: none; position: absolute; background-color: #f9f9f9; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); padding: 5px;">
                <label><input type="checkbox" id="check-all"> Check All</label><br>
                <?php
                if (!empty($selected_table)) {
                    $columns = getColumnNames($selected_table, $con2);
                    $count = 0;
                    foreach ($columns as $column) {
                        // Exclude the last column
                        if ($column === end($columns)) {
                            continue;
                        }

                        echo '<label><input type="checkbox" name="columns[]" value="' . $column . '"> ' . $column . '</label><br>';
                        $count++;

                        if ($count >= 20) {
                            break;
                        }
                    }
                }
                ?>
                <button type="button" class="submit-btn" onclick="submitColumns()">Submit</button>
            </div>
        </div>
    </form>
</div>

<script>
    function chooseTable() {
        var tableSelect = document.getElementById("table-by");
        var selectedTable = tableSelect.options[tableSelect.selectedIndex].value;

        // Construct the new URL with the selected table as a parameter
        var newUrl = 'ec_dashboard.php?table=' + selectedTable;

        // Redirect to the new URL
        window.location.href = newUrl;
    }

    document.getElementById('check-all').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.dropdown-content input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = document.getElementById('check-all').checked;
        });
    });

    // Show/Hide the dropdown content
    document.querySelector('.dropbtn').addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent the click event from bubbling up to the document
        var dropdownContent = this.nextElementSibling;
        if (dropdownContent.style.display === 'none' || dropdownContent.style.display === '') {
            dropdownContent.style.display = 'block';
        } else {
            dropdownContent.style.display = 'none';
        }
    });

    function submitColumns() {
        var form = document.getElementById('column-form');
        var formData = new FormData(form);
        formData.append('table', '<?php echo $selected_table; ?>');

        $.ajax({
            url: 'elements/ec_rooms_monitor.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('.right-dashboard').html(response);
            }
        });

        $.ajax({
            url: 'elements/ec_current_total.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('.total-consume').html(response);
            }
        });

        $.ajax({
            url: 'elements/ec_current_total.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('.total-consume').html(response);
            }
        });

        $.ajax({
            url: 'elements/ec_peak.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('.peak-consume').html(response);
            }
        });

        $(document).ready(function() {
            var table = '<?php echo $selected_table; ?>'; // Define table name
            var columns = formData.getAll('columns[]'); // Get selected columns

            $.ajax({
                url: 'elements/ec_rooms_bar.php',
                type: 'POST',
                data: {
                    table: table,
                    columns: columns
                },
                success: function(response) {
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.error) {
                            console.error('Error from server:', jsonResponse.error);
                        } else {
                            renderBarChart(jsonResponse.dataPoints, jsonResponse.columns);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response:', response);
                    }

                    renderBarChart(chartData, columnNames);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching chart data:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            });
        });
    }
</script>