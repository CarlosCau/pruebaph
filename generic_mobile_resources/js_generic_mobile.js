function openSubScreen(scrn){
    $(".modal").hide();
    $("#"+scrn).show();
}

function randomizePos(e){
    var offsetX= Math.random()*0.00500-0.0025;
    var offsetY= Math.random()*0.00500-0.0025;
    e.latitude=e.latitude+offsetY;
    e.longitude=e.longitude+offsetX;
    e.latlng=L.latLng([e.latitude, e.longitude]);
    return e;
}