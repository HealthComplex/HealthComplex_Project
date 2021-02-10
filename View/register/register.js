function User(firstname,lastname,username,password,email,countryCode,city,address,phoneNumber){
    this.firstname=firstname;
    this.lastname=lastname;
    this.username=username;
    this.password=password;
    this.email=email;
    this.countryCode=countryCode;
    this.city=city;
    this.address=address;
    this.phoneNumber=phoneNumber;
}

$('#myform').click(function () {
    let firstname=$("#firstname").val();
    let lastname=$("#lastname").val();
    let email=$("#inputEmail3").val();
    let username=$("#inputUsername3").val();
    let password=$("#inputPassword3").val();
    let countryCode=$("#sel1").val();
    let city=$("#inputCity").val();
    let address=$("#inputAddress").val();
    let phoneNumber=$("#phoneNumber").val();
    if(firstname=="" || lastname=="" || email=="" || username=="" || password=="" || city=="" || address=="" || phoneNumber==""){
        $("p").text("please fill all the inputs completely!")
        return
    }
    let inputUser=new User(firstname,lastname,username,password,email,countryCode,city,address,phoneNumber);
    let jsonData=JSON.stringify(inputUser);
    $.post("http://localhost//HealthComplex_Project/Controller/mainController.php/User",
        jsonData
    ).fail(function (xhr, status, error) {
        $("p").text(xhr.responseText)
    }).done(function () {
        window.location.replace("../login/index.html?registered=1");
    });

});










