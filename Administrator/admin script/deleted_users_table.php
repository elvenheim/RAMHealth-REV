<?php
    require_once('admin_connect.php');

    $rows_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $rows_per_page;
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'deleted_employee_id'; // Default sorting column

    $count_query = "SELECT COUNT(*) as count FROM deleted_users";
    $count_result = mysqli_query($con, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $total_rows = $count_row['count'];

    $total_pages = ceil($total_rows / $rows_per_page);

    $sql = "SELECT du.*, u.user_role, GROUP_CONCAT(r.role_name ORDER BY r.role_name SEPARATOR ', ') AS role_names
        FROM deleted_users du
        JOIN user u ON u.deleted_employee_id = du.deleted_employee_id
        JOIN role_type r ON FIND_IN_SET(r.role_id, u.user_role) > 0
        GROUP BY du.deleted_employee_id
        ORDER BY $sort_by ASC
        LIMIT $offset, $rows_per_page";

    $result_table = mysqli_query($con, $sql);

    if ($total_rows == 0) {
    echo '<span class ="table-no-record"> No users are deleted in the database...' . "</span>";
    } else {
        while ($row = mysqli_fetch_assoc($result_table)) {
            echo "<tr>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['deleted_employee_id'] . "</td>";
            echo '<td style="min-width: 150px; max-width: 150px;">' . $row['deleted_employee_fullname'] . "</td>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['deleted_employee_email'] . "</td>";
            // roles of user, additional with the overflow cells
            $roles = explode(',', $row['role_names']);
            $numberOfRoles = count($roles);
            $cellClass = ($numberOfRoles >= 2) ? 'overflow-cell' : '';
            echo '<td class="' . $cellClass . '">' . implode(', ', $roles);
            // Pop-up window for overflow content
            echo '<div class="overflow-popup">' . implode(', ', $roles) . '</div>';
            echo '</td>';
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['deleted_employee_create_at'] . "</td>";
            echo '<td style="min-width: 100px; max-width: 100px;">' . $row['employee_delete_at'] . "</td>";
            echo '<td class="action-buttons">';
            echo '<div>';
            echo '<button class="restore-button" type="button" onclick="restoreRow(' . $row['deleted_employee_id'] . ')"> 
                    <i class="fas fa-rotate-left"></i></button>';
            echo '<button class="delete-button" type="button" onclick="deleteRow(' . $row['deleted_employee_id'] . ')"> 
                    <i class="fas fa-trash"></i></button>';
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

        sortBy = $('#sort-by').val(); // Get the sort_by value from the dropdown

        currentPage = page;
        updatePagination();
        loadTableContent(page, sortBy); // Pass both page and sort_by parameters

        // Prevent default behavior of anchor links
        event.preventDefault()
    }

    function loadTableContent(page, sortBy) {
        $.ajax({
            url: '../Administrator/admin script/deleted_users_table.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy
            },
            success: function(data) {
                $('#deleted-users-table').fadeOut('fast', function() {
                    $(this).html(data).fadeIn('fast');
                });
            },
            error: function() {
                alert('Error loading table content.');
            }
        });
    }

    function sortTable() {
        var sortBy = $('#sort-by').val();
        $.ajax({
            url: '../Administrator/admin script/deleted_users_table.php',
            type: 'GET',
            data: {
                page: currentPage,
                sort_by: sortBy
            },
            success: function(data) {
                $('#deleted-users-table').fadeOut('fast', function() {
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
    function restoreRow(delemployeeId) {
        if (confirm("Do you want to restore this user?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Administrator/admin script/restore_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        alert("User has been successfully restored.");
                        location.reload();
                    } else {
                        alert("Error deleting user: " + xhr.responseText);
                    }
                }
            };
            xhr.send("deleted_employee_id=" + delemployeeId);
        }
    }

    // Script for Deleting a User
    function deleteRow(employeeId) {
        var currentEmployeeId = <?php echo $_SESSION['employee_id']; ?>;
        if (employeeId == currentEmployeeId) {
            alert("You cannot delete your own account.");
            return;
        }
        if (confirm("Do you want to PERMANENTLY DELETE this user?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Administrator/admin script/admin_permanent_delete.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        alert("User has been successfully deleted.");
                        location.reload();
                    } else {
                        alert("Error deleting user: " + xhr.responseText);
                    }
                }
            };
            xhr.send("employee_id=" + employeeId);
        }
    }
</script>