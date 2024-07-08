<?php
    require_once('ec_param_connect.php');

    // Initialize the selected table variable
    $selected_table = '';

    // Check if there is a table selected in the URL
    if (isset($_GET['table'])) {
        $selected_table = $_GET['table'];
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
        <option value="" disabled>Select Table</option>
        <?php
        while ($row = mysqli_fetch_assoc($tableResult)) {
            $table_name = $row['table_name'];
            $selected = ($table_name === $selected_table) ? 'selected' : '';
            echo '<option value="' . $table_name . '" ' . $selected . '>' . $table_name . '</option>';
        }
        ?>
    </select>
</div>

<script>
    // Function to preserve selected table in dropdown after redirect
    function chooseTable() {
        var table = document.getElementById("table-by").value;
        if (table) {
            window.location.href = 'ec_tech_page.php?table=' + table;
        }
    }

    // Preserve selected option on page load
    document.addEventListener("DOMContentLoaded", function() {
        var tableBy = document.getElementById("table-by");
        var selectedTable = '<?php echo $selected_table; ?>';
        if (selectedTable) {
            tableBy.value = selectedTable;
        }
    });
</script>