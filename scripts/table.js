// Import form submission
function updateTableName() {
    // Get the selected file
    const fileInput = document.getElementById('csvFile');
    const fileName = fileInput.files[0].name;

    // Remove the file extension
    const fileNameWithoutExtension = fileName.split('.').slice(0, -1).join('.');

    // Update the value of table_name input
    document.getElementById('table_name').value = fileNameWithoutExtension;
    
    // Submit form
    document.getElementById('importForm').submit();
}

// Export table script
function submitExportForm() {
    const tableName = document.getElementById('table_name').value;
    if (tableName) {
        document.getElementById('exportForm').submit();
    } else {
        alert('Table name is not set.');
    }
}

// Function for selecting a table
function changeTable() {
    var selectedTable = document.getElementById('table-select').value;

    // Update the content based on selected table
    var tableContainer = document.getElementById('aq-param-table');
    var tablePath = '';

    if (selectedTable === 'aq_param_five') {
        tablePath = '../Air Quality Technician/aq tech script/aq_param_table_five.php';
    } else if (selectedTable === 'aq_param_daily') {
        tablePath = '../Air Quality Technician/aq tech script/aq_param_table_daily.php';
    }

    // Use AJAX to load the PHP file content dynamically
    $.ajax({
        url: tablePath,
        type: 'GET',
        success: function(data) {
            $('#aq-param-table').fadeTo('fast', 0.3, function() {
                $(this).html(data).fadeTo('fast', 1, function() {
                    // Ensure proper display after the fade-in completes
                    $(this).css('visibility', 'visible');
                    // Reinitialize pagination after updating table content
                    updatePagination();
                });
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading table content:', status, error);
            alert('Error loading table content.');
        }
    });

    // Update the value of the hidden input field
    document.getElementById("table_export").value = selectedTable;

    // Optional: Log to console for debugging
    console.log("Selected Table:", selectedTable);
}


// Function to submit form when export button is clicked
function submitForm() {
    changeTable(); // Ensure table_name is updated before submitting
    document.getElementById('exportForm').submit();
}