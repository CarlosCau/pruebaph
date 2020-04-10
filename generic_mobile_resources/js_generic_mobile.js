function openSubScreen(scrn){
    $(".modal").hide();
    $("#"+scrn).show();
}

//function randomizePos(e){
//    var offsetX = Math.random()*0.00500-0.0025;
//    var offsetY = Math.random()*0.00500-0.0025;
//    e.latitude=e.latitude+offsetY;
//    e.longitude=e.longitude+offsetX;
//    e.latlng=L.latLng([e.latitude, e.longitude]);
//    return e;
//}

function randomizePos(e){
    return e;
}

function startAutolocate(){
    $("#btnAutolocate").html("On");
    clearInterval(intAutolocate);
    intAutolocate=setInterval(function(){
        var radius = Math.min(200, posCurrent.accuracy/2)
        radius = Math.max(10, radius)
        if (mrkCurrentLocation) {
            mrkCurrentLocation.remove();
        }
        mrkCurrentLocation = L.circle(posCurrent.latlng, {radius:radius}).addTo(mymap);
        mymap.setView(posCurrent.latlng, 17);
    }, $("#numAutolocate").val()*1000)
}

function stopAutolocate(){
    $("#btnAutolocate").html("Off");
    clearInterval(intAutolocate);
}

function startBreadcrumbs(){
    $("#btnBreadcrumbs").html("On");
    clearInterval(intBreadcrumbs);
    intBreadcrumbs=setInterval(function(){
        var radius = Math.min(200, posCurrent.accuracy/2)
        radius = Math.max(10, radius)
        var mrkBreadcrumb = L.circle(posCurrent.latlng, {radius:radius, color:'green'}).addTo(mymap);
        lyrBreadcrumbs.addLayer(mrkBreadcrumb);
    }, $("#numBreadcrumbs").val()*1000)
}

function stopBreadcrumbs(){
    $("#btnBreadcrumbs").html("Off");
    clearInterval(intBreadcrumbs);
}
