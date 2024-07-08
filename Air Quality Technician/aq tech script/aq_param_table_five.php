<?php
require_once('aq_param_connect.php');

$rows_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'room_id'; // Default sorting column
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : '';

$count_query = "SELECT COUNT(*) as count FROM aq_param_five";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_rows = $count_row['count'];

$total_pages = ceil($total_rows / $rows_per_page);

$sql = "SELECT aqpf.*
        FROM aq_param_five aqpf
        WHERE room_id = '$room_id'
        ORDER BY $sort_by ASC
        LIMIT $offset, $rows_per_page";
$result_table = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result_table)) {
    echo "<tr>";
    echo "<td>" . $row['room_id'] . "</td>";
    echo "<td>" . $row['co2_level'] . "</td>";
    echo "<td>" . $row['rel_humid'] . "</td>";
    echo "<td>" . $row['pm_one'] . "</td>";
    echo "<td>" . $row['pm_two_five'] . "</td>";
    echo "<td>" . $row['pm_ten'] . "</td>";
    echo "<td>" . $row['param_temp'] . "</td>";
    echo "<td>" . $row['heat_index'] . "</td>";
    echo "<td>" . $row['date_acquired'] . "</td>";
    echo "</tr>";
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script for pagination -->
<script>
    var currentPage = <?php echo $page; ?>;
    var totalPages = <?php echo $total_pages; ?>;
    var sortBy = '<?php echo $sort_by; ?>'; // Store the sort_by parameter
    var selectedRoom = '<?php echo $room_id; ?>' // Store the selected room

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

    function loadPage(page, sortBy, selectedRoom) {
        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }

        sortBy = $('#sort-by').val(); // Get the sort_by value from the dropdown
        selectedRoom = $('#facility-sort').val(); // Get the selected room value


        currentPage = page;
        updatePagination();
        loadTableContent(page, sortBy, selectedRoom);

        // Prevent default behavior of anchor links
        event.preventDefault();
    }

    function loadTableContent(page, sortBy, selectedRoom) {
        $.ajax({
            url: '../Air Quality Technician/aq tech script/aq_param_table_five.php',
            type: 'GET',
            data: {
                page: page,
                sort_by: sortBy,
                room_id: selectedRoom
            },
            success: function(data) {
                $('#aq-param-table').fadeTo('fast', 0.3, function() {
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

        $.ajax({
            url: '../Air Quality Technician/aq tech script/aq_param_table_five.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy,
                room_id: selectedRoom
            },
            success: function(data) {
                $('#aq-param-table').fadeTo('fast', 0.3, function() {
                    $(this).html(data).fadeTo('fast', 1, function() {
                        $(this).css('visibility', 'visible');
                    });
                });
            },
            error: function() {
                alert('Error sorting table content.');
            }
        });
    }

    function selectRoom() {
        selectedRoom = $('#facility-sort').val(); // Update the sort_by value

        console.log(selectedRoom);
        $.ajax({
            url: '../Air Quality Technician/aq tech script/aq_param_table_five.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy,
                room_id: selectedRoom
            },
            success: function(data) {
                $('#aq-param-table').fadeTo('fast', 0.3, function() {
                    $(this).html(data).fadeTo('fast', 1, function() {
                        $(this).css('visibility', 'visible');
                    });
                });
            },
            error: function() {
                alert('Error sorting table content.');
            }
        });
    }
</script>