const btnSignIn = document.getElementById("sign-in"),
      btnSignUp = document.getElementById("sign-up"),
      containerFormRegister = document.querySelector(".register"),
      containerFormLogin = document.querySelector(".login");

btnSignIn.addEventListener("click", e => {
    // Mostrar login y ocultar registro
    containerFormRegister.classList.add("hide");
    containerFormLogin.classList.remove("hide");
});

btnSignUp.addEventListener("click", e => {
    // Mostrar registro y ocultar login
    containerFormLogin.classList.add("hide");
    containerFormRegister.classList.remove("hide");
});
