<?php
require_once('housekeep_connect.php');

$rows_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'room_num'; // Default sorting column

$count_query = "SELECT COUNT(*) as count FROM deleted_room_num";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_rows = $count_row['count'];

$total_pages = ceil($total_rows / $rows_per_page);

// Fetch data from the database
$sql = "SELECT drn.*, bflr.bldg_floor_name
            FROM deleted_room_num drn
            JOIN building_floor bflr ON drn.bldg_floor = bflr.building_floor
            ORDER BY $sort_by ASC
            LIMIT $offset, $rows_per_page";
$result_table = mysqli_query($con, $sql);

// Loop through the data and create table rows
if ($total_rows == 0) {
    echo '<span class ="table-no-record"> No rooms are deleted in the database...' . "</span>";
} else {
    while ($row = mysqli_fetch_assoc($result_table)) {
        echo "<tr>";
        echo '<td style="min-width: 100px; max-width: 100px;">' . $row['bldg_floor_name'] . "</td>";
        echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_num'] . "</td>";
        echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_type'] . "</td>";
        echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_added_at'] . "</td>";
        echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_delete_at'] . "</td>";
        echo '<td class="action-buttons">';
        echo '<div>';
        echo '<button class="restore-button" type="button" onclick="restoreRow(\'' . $row['room_num'] . '\')"> 
                    <i class="fas fa-rotate-left"></i></button>';
        echo '<button class="delete-button" type="button" onclick="deleteRow(\'' . $row['room_num'] . '\')">';
        echo '<i class="fas fa-trash"></i>';
        echo '</div>';
        echo "</td>";
        echo "</tr>";
    }
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

        sortBy = $('#sort-by').val(); // Get the selected value of the sort by dropdown

        currentPage = page;
        updatePagination();
        loadTableContent(page, sortBy);

        // Prevent default behavior of anchor links
        event.preventDefault();
    }

    function loadTableContent(page, sortBy) {
        $.ajax({
            url: '../Housekeeper/housekeeper script/deleted_rooms_table.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy
            },
            success: function(data) {
                $('#room-manage-table').fadeOut('fast', function() {
                    $(this).html(data).fadeIn('fast');
                });
            },
            error: function() {
                alert('Error loading table content.');
            }
        });
    }

    function sortTable() {
        sortBy = $('#sort-by').val(); // Update the sort_by value
        $.ajax({
            url: '../Housekeeper/housekeeper script/deleted_rooms_table.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy
            },
            success: function(data) {
                $('#room-manage-table').fadeOut('fast', function() {
                    $(this).html(data).fadeIn('fast');
                });
            },
            error: function() {
                alert('Error sorting table content.');
            }
        });
    }
</script>

<script>
    function restoreRow(roomNum) {
        if (confirm("Do you want to restore this room?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Housekeeper/housekeeper script/restore_room.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Room has been successfully restored.");
                    location.reload();
                }
            };
            xhr.send("room_num=" + roomNum);
        }
    }

    function deleteRow(roomNum) {
        if (confirm("Do you want to PERMANENTLY DELETE this room?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Housekeeper/housekeeper script/permanent_delete_room.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Room has been successfully deleted.");
                    location.reload();
                }
            };
            xhr.send("room_num=" + roomNum);
        }
    }
</script>