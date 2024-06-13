<?php
    require_once('ec_param_connect.php');

    // Function to execute the query and display the results
    function displayGeneralData($con, $page, $rows_per_page, $sort_by)
    {
        $offset = ($page - 1) * $rows_per_page;

        $search_term = isset($_GET['search']) ? $_GET['search'] : '';

        // Query to fetch data from multiple tables with pagination and sorting
        $sql = "SELECT data.*, eas.arduino_bldg_floor, eas.arduino_room_num, bf.bldg_floor_name, st.sensor_type_name
                FROM (
                    SELECT 'acu' AS type, ec_sensor_acu_id AS sensor_id, ec_acu_date AS date_column, ec_acu_time AS time_column, ec_acu_current AS current_column
                    FROM ec_param_acu_data
                    UNION ALL
                    SELECT 'lights' AS type, ec_sensor_lights_id AS sensor_id, ec_lights_date AS date_column, ec_lights_time AS time_column, ec_lights_current AS current_column
                    FROM ec_param_lights_data
                    UNION ALL
                    SELECT 'others' AS type, ec_sensor_others_id AS sensor_id, ec_others_date AS date_column, ec_others_time AS time_column, ec_others_current AS current_column
                    FROM ec_param_others_data
                    UNION ALL
                    SELECT 'outlet' AS type, ec_sensor_outlet_id AS sensor_id, ec_outlet_date AS date_column, ec_outlet_time AS time_column, ec_outlet_current AS current_column
                    FROM ec_param_outlet_data
                    UNION ALL
                    SELECT 'util' AS type, ec_sensor_util_id AS sensor_id, ec_util_date AS date_column, ec_util_time AS time_column, ec_util_current AS current_column
                    FROM ec_param_util_data
                ) data
                JOIN ec_arduino_sensors eas ON data.sensor_id = eas.ec_arduino_sensors_id
                LEFT JOIN room_number rn ON eas.arduino_room_num = rn.room_num
                LEFT JOIN sensor_type st ON eas.ec_arduino_sensors_type = st.sensor_type_id
                LEFT JOIN building_floor bf ON rn.bldg_floor = bf.building_floor
                INNER JOIN (
                    SELECT sensor_id, MAX(CONCAT(date_column, ' ', time_column)) AS max_datetime
                    FROM (
                        SELECT 'acu' AS type, ec_sensor_acu_id AS sensor_id, ec_acu_date AS date_column, ec_acu_time AS time_column
                        FROM ec_param_acu_data
                        UNION ALL
                        SELECT 'lights' AS type, ec_sensor_lights_id AS sensor_id, ec_lights_date AS date_column, ec_lights_time AS time_column
                        FROM ec_param_lights_data
                        UNION ALL
                        SELECT 'others' AS type, ec_sensor_others_id AS sensor_id, ec_others_date AS date_column, ec_others_time AS time_column
                        FROM ec_param_others_data
                        UNION ALL
                        SELECT 'outlet' AS type, ec_sensor_outlet_id AS sensor_id, ec_outlet_date AS date_column, ec_outlet_time AS time_column
                        FROM ec_param_outlet_data
                        UNION ALL
                        SELECT 'util' AS type, ec_sensor_util_id AS sensor_id, ec_util_date AS date_column, ec_util_time AS time_column
                        FROM ec_param_util_data
                    ) subquery
                    GROUP BY sensor_id
                ) AS latest 
                ON data.sensor_id = latest.sensor_id 
                AND CONCAT(data.date_column, ' ', data.time_column) = latest.max_datetime
                WHERE st.sensor_type_name LIKE '%$search_term%'
                ORDER BY $sort_by ASC
                LIMIT $offset, $rows_per_page";

        $result_table = mysqli_query($con, $sql);

        if (!$result_table) {
            echo "Error: " . mysqli_error($con);
            return;
        }

        while ($row = mysqli_fetch_assoc($result_table)) {
            echo "<tr>";
            echo "<td>" . $row['bldg_floor_name'] . "</td>";
            echo "<td>" . $row['arduino_room_num'] . "</td>";
            echo "<td>" . $row['sensor_id'] . "</td>";
            echo "<td>" . $row['sensor_type_name'] . "</td>";
            echo "<td>" . $row['current_column'] . " amps</td>";
            echo "</tr>";
        }

        // Count total rows
        $count_query = "SELECT COUNT(*) as count FROM (
                            SELECT 'acu' AS type, ec_sensor_acu_id AS sensor_id
                            FROM ec_param_acu_data
                            UNION ALL
                            SELECT 'lights' AS type, ec_sensor_lights_id AS sensor_id
                            FROM ec_param_lights_data
                            UNION ALL
                            SELECT 'others' AS type, ec_sensor_others_id AS sensor_id
                            FROM ec_param_others_data
                            UNION ALL
                            SELECT 'outlet' AS type, ec_sensor_outlet_id AS sensor_id
                            FROM ec_param_outlet_data
                            UNION ALL
                            SELECT 'util' AS type, ec_sensor_util_id AS sensor_id
                            FROM ec_param_util_data
                        ) as count_table";
        $count_result = mysqli_query($con, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $total_rows = $count_row['count'];

        return array(
            'sql' => $sql,
            'total_rows' => $total_rows
        );
        }

    $rows_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'arduino_room_num';

    $result = displayGeneralData($con, $page, $rows_per_page, $sort_by);
    $sql = $result['sql'];
    $total_rows = $result['total_rows'];

    $total_pages = ceil($total_rows / $rows_per_page);
?>

<!-- Include jQuery and your pagination script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var currentPage = <?php echo $page; ?>;
    var totalPages = <?php echo $total_pages; ?>;
    var sortBy = <?php echo $sort_by; ?>;

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

        sortBy = $('#sort-by').val(); // Get the sort_by value from the dropdown
        var searchTerm = $('#search-input').val(); // Get the search term from the input

        currentPage = page;
        updatePagination();
        loadTableContent(page, sortBy, searchTerm); // Pass the search term

        // Prevent default behavior of anchor links
        event.preventDefault();
    }

    function searchTable() {
        var searchTerm = $('#search-input').val(); // Get the search term from the input
        loadTableContent(currentPage, sortBy, searchTerm); // Pass the search term to the loadTableContent function
    }

    function loadTableContent(page, sortBy, searchTerm) {
        $.ajax({
            url: '../Energy Consumption Technician/parameters/ec_general_table.php',
            type: 'GET',
            data: {
                page: page,
                sort_by: sortBy,
                search: searchTerm // Include the search term in the request
            },
            success: function(data) {
                $('#ec-param-table').fadeTo('fast', 0.3, function() {
                    $(this).html(data).fadeTo('fast', 1, function() {
                        // Ensure proper display after the fade-in completes
                        $(this).css('visibility', 'visible');
                    });
                });
                updatePagination(); // Update pagination with the new results
            },
            error: function() {
                alert('Error loading table content.');
            }
        });
    }

    function sortTable() {
        sortBy = $('#sort-by').val(); // Update the sort_by value
        var searchTerm = $('#search-input').val(); // Get the search term from the input

        $.ajax({
            url: '../Energy Consumption Technician/parameters/ec_general_table.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy,
                search: searchTerm // Include the search term in the request
            },
            success: function(data) {
                $('#ec-param-table').fadeTo('fast', 0.3, function() {
                    $(this).html(data).fadeTo('fast', 1, function() {
                        $(this).css('visibility', 'visible');
                    });
                });
                updatePagination(); // Update pagination with the new sorted results
            },
            error: function() {
                alert('Error sorting table content.');
            }
        });
    }
</script>