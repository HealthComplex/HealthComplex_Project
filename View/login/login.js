$("#myform").click(function () {
    let username=$("#inputUsername3").val()
    let password=$("#inputPassword3").val()
    let obj={
        "username":username,
        "password":password
    };
    let jsonObj=JSON.stringify(obj);
    $.post(
        "http://localhost//HealthComplex_Project/Controller/mainController.php/login",
        jsonObj
    ).fail(function (xhr, status, error) {
        $("p").text(xhr.responseText)
    }).done(function (data, textStatus, jqXHR) {
        let x=JSON.parse(jqXHR.responseText);
        localStorage.setItem("accessToken",x)
        alert("login successful!")
        window.location.replace("../../index.html");
    });
});