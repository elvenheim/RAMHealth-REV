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