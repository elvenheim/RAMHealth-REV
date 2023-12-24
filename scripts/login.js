function show_password(){
    var password = document.getElementById('password_field');
    var icon = document.querySelector('.fa-eye');
    if (password.type === "password") {
    password.type = "text";
    password.style.marginTop = "20px"; 
    icon.style.color = "#E7AE41";
    }   else{
        password.type = "password";
        icon.style.color = "#343A40";
    }
};

function closePopup() {
    document.getElementById('errorPopup').style.display = 'none';
}

function showPopup() {
    document.getElementById('errorPopup').style.display = 'block';
}

window.onload = function() {
    if (window.location.search.includes('error')) {
        showPopup();
    }
}
