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
    clearInterval(intInfo);
    intInfo = setInterval(function(){
        populateInfo();
    },$("#numBreadcrumbs").val()*1000)
}

function stopBreadcrumbs(){
    $("#btnBreadcrumbs").html("Off");
    clearInterval(intBreadcrumbs);
}

function populateInfo(){
    $(".info_cur_acc").html(posCurrent.accuracy.toFixed(1));
    if (!posCurrent.altitude){
        posCurrent.altitude="NA";
    } else {
        posCurrent.altitude=posCurrent.altitude.toFixed(1)
    }
    $("#info_cur_lat").val(posCurrent.latitude.toFixed(5));
    $("#info_cur_lng").val(posCurrent.longitude.toFixed(5));
    $("#info_cur_alt").val(posCurrent.altitude);
    $("#info_cur_tm").val(returnTimeFromUTC(posCurrent.timestamp));
    
    if (posPrevious){
        $("#info_prv_lat").val(posPrevious.latitude.toFixed(5));
        $("#info_prv_lng").val(posPrevious.longitude.toFixed(5));
        $("#info_prv_alt").val(posPrevious.altitude);
        $("#info_prv_tm").val(returnTimeFromUTC(posPrevious.timestamp));

        var dst=posPrevious.latlng.distanceTo(posCurrent.latlng);
        if ((posCurrent.altitude=="NA") || (posPrevious.altitude=="NA")) {
            var alt="NA";
        } else {
            var alt=posCurrent.altitude-posPrevious.altitude
        }
        var tm=(posCurrent.timestamp-posPrevious.timestramp)/1000
        var bng=L.GeometryUtil.bearing(posPrevious.latlng,posCurrent.latlng)
        if (alt=="NA"){
            var clr="NA";
        } else {
            var clr=(alt/tm*60*60).toFixed(1);
        }
        $("#info_dif_dst").val(dst.toFixed(1));
        $("#info_dif_alt").val(alt);
        $("#info_dif_tm").val(tm.toFixed(1));
        $("#info_dif_bng").val(bng.toFixed(1));
        $("#info_dif_vel").val(((dst/tm*60*60)/1000).toFixed(3));
        $("#info_dif_clr").val(clr);
    }
    posPrevious=posCurrent;
}
