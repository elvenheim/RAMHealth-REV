<?php
    require_once('ec_table_connect.php');

    $table = isset($_GET['table']) ? $_GET['table'] : '8th floor r-acu15 daily';
    $rows_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $rows_per_page;

    // Query to get column names of the selected table
    $columns_query = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$db_name2' AND TABLE_NAME = '$table'";
    $columns_result = mysqli_query($con2, $columns_query);

    if (!$columns_result) {
        die("Failed to fetch columns: " . mysqli_error($con2));
    }

    // Fetch column names
    $columns = [];
    while ($column_row = mysqli_fetch_assoc($columns_result)) {
        $columns[] = $column_row['COLUMN_NAME'];
    }

    $count_query = "SELECT COUNT(*) as count FROM `$table`";
    $count_result = mysqli_query($con2, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_rows = $count_row['count'];

    $total_pages = ceil($total_rows / $rows_per_page);

    $sql = "SELECT * FROM `$table` LIMIT $offset, $rows_per_page";
    $result_table = mysqli_query($con2, $sql);

    // Print table header with column names
    echo "<thead><tr>";
    foreach ($columns as $column) {
        echo "<th>" . htmlspecialchars($column) . "</th>";
    }
    echo "</tr></thead>";

    // Print table rows
    echo "<tbody>";
    while ($row = mysqli_fetch_assoc($result_table)) {
        echo "<tr>";
        foreach ($columns as $column) {
            echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        if (totalPages - endPage < Math.floor(maxButtons / 2)) {
            startPage = Math.max(1, startPage - (Math.floor(maxButtons / 2) - (totalPages - endPage)));
        }

        paginationHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '"><a class="page-link previous" href="#" onclick="loadPage(' + (currentPage - 1) + ')">Previous</a></li>';

        for (var i = startPage; i <= endPage; i++) {
            paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '"><a class="page-link number" href="#" onclick="loadPage(' + i + ')">' + i + '</a></li>';
        }

        paginationHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '"><a class="page-link next" href="#" onclick="loadPage(' + (currentPage + 1) + ')">Next</a></li>';

        $('#pagination').html(paginationHtml);
    }

    function loadPage(page) {
        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        var table = '<?php echo $table; ?>';
        currentPage = page;
        updatePagination();
        loadTableContent(page, table);

        event.preventDefault();
    }

    function loadTableContent(page, table) {
        $.ajax({
            url: '../Energy Consumption Technician/parameters/ec_tech_table.php',
            type: 'GET',
            data: {
                page: page,
                table: table
            },
            success: function(data) {
                $('#ec-param-table').fadeTo('fast', 0.3, function() {
                    $(this).html(data).fadeTo('fast', 1, function() {
                        $(this).css('visibility', 'visible');
                    });
                });
                updatePagination();
            },
            error: function() {
                alert('Error loading table content.');
            }
        });
    }
</script>