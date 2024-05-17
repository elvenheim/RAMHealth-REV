<?php
    require_once('housekeep_connect.php');

    $rows_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $rows_per_page;
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'bldg_floor'; // Default sorting column

    $count_query = "SELECT COUNT(*) as count FROM room_number";
    $count_result = mysqli_query($con, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_rows = $count_row['count'];
    $total_pages = ceil($total_rows / $rows_per_page);

    // Fetch data from the database with sorting
    $sql = "SELECT rn.*, bldg.bldg_floor_name 
                        FROM room_number rn 
                        JOIN building_floor bldg ON rn.bldg_floor = bldg.building_floor
                        ORDER BY $sort_by ASC
                        LIMIT $offset, $rows_per_page";
    $result_table = mysqli_query($con, $sql);

    // Loop through the data and create table rows
    if ($total_rows == 0) {
        echo '<span class ="table-no-record"> No rooms are registered in the database...</span>';
    } else {
        while ($row = mysqli_fetch_assoc($result_table)) {
            echo "<tr>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['bldg_floor_name'] . "</td>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_num'] . "</td>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_type'] . "</td>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['room_added_at'] . "</td>";
            echo '<td class="action-buttons">';
            echo '<div>';
            echo '<button class="edit-button" type="button" onclick="editRow(\'' . $row['room_num'] . '\')">';
            echo '<i class="fas fa-edit"></i>';
            echo '</button>';
            echo '<button class="delete-button" type="button" onclick="deleteRow(\'' . $row['room_num'] . '\')">';
            echo '<i class="fas fa-trash"></i>';
            echo '</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
    }
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- script for pagination -->
<script>
    var currentPage = <?php echo $page; ?>;
    var totalPages = <?php echo $total_pages; ?>;
    var sortBy = '<?php echo $sort_by; ?>'; // Store the sort_by parameter

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

        currentPage = page;
        updatePagination();
        loadTableContent(page, sortBy); // Pass both page and sort_by parameters

        // Prevent default behavior of anchor links
        event.preventDefault();
    }

    function loadTableContent(page, sortBy) {
        $.ajax({
            url: '../Housekeeper/housekeeper script/room_manage_table.php',
            type: 'GET',
            data: {
                page: page,
                sort_by: sortBy // Include sort_by parameter
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
            url: '../Housekeeper/housekeeper script/room_manage_table.php',
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
    function deleteRow(roomNum) {
        if (confirm("Do you want to delete this room?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Housekeeper/housekeeper script/delete_room.php", true);
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

    function editRow(roomNum) {
        if (confirm("Do you want to edit this room?")) {
            // Assuming you have a form to edit the room details
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "../Housekeeper/housekeeper script/housekeep_edit_room.php"); // Replace with your edit page URL

            // Create a hidden input field to pass the room number
            var input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", "room_num");
            input.setAttribute("value", roomNum);

            // Append the input field to the form
            form.appendChild(input);

            // Append the form to the document body
            document.body.appendChild(form);

            // Submit the form
            form.submit();
        }
    }
</script>