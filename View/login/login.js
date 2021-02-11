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
        let xhttp=new XMLHttpRequest();
        xhttp.onreadystatechange=function () {
            if(this.readyState==4){
                if(this.status==200){
                    window.location.replace("../adminPanel/account.html");
                }
            }
        }
        let xmlHttpRequest=new XMLHttpRequest()
        xmlHttpRequest.onreadystatechange=function () {
            if(this.readyState==4){
                if(this.status==200){
                    window.location.replace("../trip_planning/index.html");
                }
            }
        }
        xhttp.open("GET","http://localhost//HealthComplex_Project/Controller/mainController.php/authAdmin",false);
        xmlHttpRequest.open("GET","http://localhost//HealthComplex_Project/Controller/mainController.php/authUser",false);
        xhttp.send()
        xmlHttpRequest.send()
    });



});