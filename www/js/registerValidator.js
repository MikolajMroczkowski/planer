function checkMailFormat(el) {
    var val = el.value;
    if (val.split('@')[1] != null) {
        if (val.split('@')[1] != "") {
            if (val.split('.')[1] != null) {
                if (val.split('.')[1] != "") {
                    if(val.replace(" ","")==val){
                        document.getElementById('mailInformer').innerText = "Nie mamy zastrzeżeń co do formatu e-maila"
                    }
                    else{
                        document.getElementById('mailInformer').innerText = "Protokół e-mail nigdy nie obsługiwał spacji"
                    }
                }
                else{
                    document.getElementById('mailInformer').innerText = "Kropka nie jest ostatnik znakiem domeny"
                }
            }
            else{
                document.getElementById('mailInformer').innerText = "Domena po @ zawiera kropkę"
            }
        } else {
            document.getElementById('mailInformer').innerText = "Po znaku @ powinna być wskazana domena"
        }
    } else {
        document.getElementById('mailInformer').innerText = "E-mail zawiera znak @"
    }
    setTimeout(function () {
        document.getElementById('mailInformer').innerText = ""
    },15000)
}
function checkPhoneFormat(el) {
    var val = el.value;
    var toNumber = el.value.replaceAll(" ","")
    toNumber = toNumber.replaceAll("-","")
    if(toNumber.length===9){
        if(!isNaN(toNumber/2)){
            document.getElementById('phoneInformer').innerText = "Numer Podany idealnie"
            var nmbr = "+48 ";
            for (var x = 0; x<3; x++){
                nmbr+=toNumber.slice(x*3,(x*3)+3)+"-"
            }
            el.value = nmbr.slice(0,15)
        }
        else{
            document.getElementById('phoneInformer').innerText = "Numer telefonu zawiera tylko cyfry"
        }
    }
    else if(toNumber.length===12){
        toNumber = toNumber.substring(1)
        if(!isNaN(toNumber/2)){
            document.getElementById('phoneInformer').innerText = "Numer Podany idealnie"
            var nmbr = "+"+toNumber.slice(0,2)+" ";
            toNumber=toNumber.slice(2,11)
            for (var x = 0; x<3; x++){
                nmbr+=toNumber.slice(x*3,(x*3)+3)+"-"
            }
            el.value = nmbr.slice(0,15)
        }
        else{
            document.getElementById('phoneInformer').innerText = "Numer telefonu zawiera tylko cyfry"
        }
    }
    else {
        document.getElementById('phoneInformer').innerHTML = "Zapisz numer telefonu w formacie 12 znakowym +XX XXX-XXX-XXX"
    }
    setTimeout(function () {
        document.getElementById('phoneInformer').innerText = ""
    },15000)
}