//Pure Javascript to get the "affiliate" cookie value and redirect to the "url" GET variable, tagging the affiliate cookie value on the end

//Helper functions

function getQueryVariable(variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}

function getCookie(name) {
    var parts = document.cookie.split(name + "=");
    if (parts.length == 2){
        return parts.pop().split(";").shift();
    } else {
        return null;
    }
}


var redirectUrl = getQueryVariable("url");
var affiliate = getCookie("affiliate");
if (redirectUrl != false){
    window.location.href = redirectUrl + "?acd=" + affiliate;
}