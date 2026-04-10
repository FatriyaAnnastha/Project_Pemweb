function loginUser(){
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    if(email === "" && password === ""){
        alert("Anda belum mengisi email dan password!");
        return false;
    }

    if(email == ""){
        alert("Anda belum mengisi email!");
        return false;
    }

    if(password == ""){
        alert("Anda belum mengisi password!");
        return false;
    }

    
    alert("Login berhasil! 🎉");

   
    window.location.href = "beranda.html";

    return false;
}