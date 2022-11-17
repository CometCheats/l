function changePassword(){
    let newPassword = prompt("Enter your new password", "");
    if (newPassword == null || newPassword == "") {
        alert("Invalid Password");
    }else{
        let currentPassword = prompt("Enter your old password", "");
        if (currentPassword == null || currentPassword == "") {
            alert("Invalid Password");
        }else{
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                location.reload();
            }
            xmlhttp.open("GET", "resetPassword.php?new=" + newPassword + "&old=" + currentPassword);
            xmlhttp.send();
        }
    }
}