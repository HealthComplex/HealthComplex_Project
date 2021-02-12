function sendAjaxRequest(method,url,data){ /// for requests with authorization!
    let xhttp=new XMLHttpRequest();
    let returned=null;
    xhttp.onreadystatechange=function () {
        if(this.readyState==4){
            if(this.status==200){
                returned=JSON.parse(this.responseText);
            }
            else{
                let response=JSON.parse(this.responseText);
                returned=response;
                if(response=="invalid token!") {
                    returned="login";
                }
                else{
                    if(response=="expired token!") {
                        let data = refreshRequest();
                        if (data === false) {
                            returned= "login";
                        }
                        localStorage.removeItem("accessToken");
                        localStorage.setItem("accessToken",data)
                        data=repeatRequest(method,url,data);
                        returned=data;
                    }
                }
            }
        }
    };
    xhttp.open(method,url,false)
    head="Bearer "+window.localStorage.getItem('accessToken');
    xhttp.setRequestHeader("Authorization",head);
    if(data==null) xhttp.send()
    else xhttp.send(data)
    return returned;
}


function repeatRequest(method,url,data){
    let xmlHttpRequest=new XMLHttpRequest();
    let response=null;
    xmlHttpRequest.onreadystatechange=function () {
        if(this.readyState==4){
            response=JSON.parse(this.responseText);
        }
    }
    xmlHttpRequest.open(method,url,false)
    head="Bearer "+window.localStorage.getItem('accessToken');
    xmlHttpRequest.setRequestHeader("Authorization",head);
    if(data==null) xmlHttpRequest.send()
    else xmlHttpRequest.send(data)
    return response;
}

function refreshRequest(){
    let xmlHttpRequest=new XMLHttpRequest();
    let returned=null;
    xmlHttpRequest.onreadystatechange=function(){
        if(this.readyState==4){
            if(this.status==200) returned= JSON.parse(this.responseText);
            else {
                returned= false;
                window.localStorage.removeItem("accessToken")
            }
        }
    }
    xmlHttpRequest.open("GET","http://localhost//HealthComplex_Project/Controller/mainController.php/refresh",false);
    head="Bearer "+window.localStorage.getItem('accessToken');
    xmlHttpRequest.setRequestHeader("Authorization",head);
    xmlHttpRequest.send();
    return returned;
}



