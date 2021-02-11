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
    }).done(function () {
        window.location.replace("../trip_planning/index.html");
    });



});