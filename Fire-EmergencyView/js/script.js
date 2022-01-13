function afficherOuCacherLesMarker(map,marker_to_edit,etat_btn){
    map.eachLayer(function (layer) { 
        if (layer.options.name === marker_to_edit) {
            if (layer._icon.style.display == 'none'){
                layer._icon.style.display = '';
                console.log("add layer")
            }else{
                layer._icon.style.display = 'none';
                console.log("remove layer")
            }
        }
    });
    
}

/**
 * API 
 * */


var URL = 'http://192.168.5.136:8001'
var TOKEN = 'CB814D37E278A63D3666B1A1604AD0F5C5FD7E177267F62B8D719F49182F410A'
skip = 0
limit = 1000

function getIncidents() {
    let requestURL = `${URL}/incidents/?token_api=${TOKEN}&skip=${skip}&limit=${limit}`;
    let request = new XMLHttpRequest();
    request.open("GET", requestURL, false);
    request.send(null);
    if (request.status === 200) {
        // by default the response comes in the string format, we need to parse the data into JSON
        return JSON.parse(request.response);
    } else {
        console.error(`error ${request.status} ${request.statusText}`);
        return []
    }
    
    
    
}
function getDetecteurs() {
    let requestURL = `${URL}/detecteurs/?token_api=${TOKEN}&skip=${skip}&limit=${limit}`;
    let request = new XMLHttpRequest();
    request.open("GET", requestURL, false);
    request.send(null);
    if (request.status === 200) {
        // by default the response comes in the string format, we need to parse the data into JSON
        return JSON.parse(request.response);
    } else {
        console.error(`error ${request.status} ${request.statusText}`);
        return []
    }
}


function getCasernes() {
    let requestURL = `${URL}/casernes/?token_api=${TOKEN}&skip=${skip}&limit=${limit}`;
    let request = new XMLHttpRequest();
    request.open("GET", requestURL, false);
    request.send(null);
    if (request.status === 200) {
        // by default the response comes in the string format, we need to parse the data into JSON
        return JSON.parse(request.response);
    } else {
        console.error(`error ${request.status} ${request.statusText}`);
        return []
    }
}

function getPompiersOfCaserne(id_caserne) {
    let requestURL = `${URL}/pompiers/${id_caserne}/?token_api=${TOKEN}&skip=${skip}&limit=${limit}`;
    let request = new XMLHttpRequest();
    request.open("GET", requestURL, false);
    request.send(null);
    if (request.status === 200) {
        // by default the response comes in the string format, we need to parse the data into JSON
        return JSON.parse(request.response);
    } else {
        console.error(`error ${request.status} ${request.statusText}`);
        return []
    }
}

function getVehiculesOfCaserne(id_caserne) {
    let requestURL = `${URL}/vehicules/${id_caserne}/?token_api=${TOKEN}&skip=${skip}&limit=${limit}`;
    let request = new XMLHttpRequest();
    request.open("GET", requestURL, false);
    request.send(null);
    if (request.status === 200) {
        // by default the response comes in the string format, we need to parse the data into JSON
        return JSON.parse(request.response);
    } else {
        console.error(`error ${request.status} ${request.statusText}`);
        return []
    }
}
