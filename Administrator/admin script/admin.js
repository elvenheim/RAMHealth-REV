function adduser_popup() {
  console.log("adduser_popup function called");
  var popup = document.getElementById("adduser-popup");
  var popupBg = document.getElementById("adduser-popup-bg");
  var popup_close_btn = document.getElementById("close-btn");

  if (popupBg.style.opacity === "0" && popup.style.opacity === "0") {
    popupBg.style.opacity = "1";
    popup.style.opacity = "1";
    popupBg.style.pointerEvents = "visible";
    popup.style.pointerEvents = "visible";
    popup_close_btn.style.cursor = "pointer";
  } else {
    popupBg.style.opacity = "0";
    popup.style.opacity = "0";
    popupBg.style.pointerEvents = "none";
    popup.style.pointerEvents = "none";
    popup_close_btn.style.cursor = "default";
    document.getElementById("add_user").reset();
  }
  popup_close_btn.setAttribute("onclick", "adduser_popup()");
}

function cancelEdit() {
  window.location.href = '../Administrator/admin script/users_table.php'; // Replace with the desired page URL to redirect the user
}

function uncheckAll(checkbox) {
  var checkboxes = document.querySelectorAll('.checkbox-list input[type="checkbox"]');
  for (var i = 0; i < checkboxes.length; i++) {
      checkboxes[i].checked = checkbox.checked;
  }
}


function sortTable(columnIndex) {
    let table, rows, switching, i, x, y, shouldSwitch, sortIndicator;
    table = document.querySelector(".user-management-table");
    switching = true;
    sortIndicator = document.querySelector(
        `th:nth-child(${columnIndex + 1}) .sort-indicator`
    );
  
    let sortOrder = sortIndicator.getAttribute("data-sort-order") || "asc";
    
    while (switching) {
        switching = false;
        rows = table.rows;
        
        for (i = 1; i < rows.length - 1; i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("td")[columnIndex];
            y = rows[i + 1].getElementsByTagName("td")[columnIndex];
  
            let xValue = parseFloat(x.innerHTML.toLowerCase());
            let yValue = parseFloat(y.innerHTML.toLowerCase());
            
            if (sortOrder === "asc") {
                if (xValue > yValue) {
                    shouldSwitch = true;
                    break;
                }
            } else {
                if (xValue < yValue) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
} 


function sortDeleteTable(columnIndex) {
  let table, rows, switching, i, x, y, shouldSwitch, sortIndicator;
  table = document.querySelector(".admin-deleted-user-table");
  switching = true;
  sortIndicator = document.querySelector(
      `th:nth-child(${columnIndex + 1}) .sort-indicator`
  );

  let sortOrder = sortIndicator.getAttribute("data-sort-order") || "asc";
  
  while (switching) {
      switching = false;
      rows = table.rows;
      
      for (i = 1; i < rows.length - 1; i++) {
          shouldSwitch = false;
          x = rows[i].getElementsByTagName("td")[columnIndex];
          y = rows[i + 1].getElementsByTagName("td")[columnIndex];

          let xValue = parseFloat(x.innerHTML.toLowerCase());
          let yValue = parseFloat(y.innerHTML.toLowerCase());
          
          if (sortOrder === "asc") {
              if (xValue > yValue) {
                  shouldSwitch = true;
                  break;
              }
          } else {
              if (xValue < yValue) {
                  shouldSwitch = true;
                  break;
              }
          }
      }
      
      if (shouldSwitch) {
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          switching = true;
      }
  }
  
  // Toggle the sorting order
  if (sortOrder === "asc") {
      sortIndicator.setAttribute("data-sort-order", "desc");
      sortIndicator.innerHTML = "&#x25BC;";
  } else {
      sortIndicator.setAttribute("data-sort-order", "asc");
      sortIndicator.innerHTML = "&#x25B2;";
  }
}