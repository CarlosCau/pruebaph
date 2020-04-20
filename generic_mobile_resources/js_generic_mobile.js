function openSubScreen(scrn){
    $(".modal").hide();
    $("#"+scrn).show();
}

/*function randomizePos(e){
    var offsetX = Math.random()*0.00500-0.0025;
    var offsetY = Math.random()*0.00500-0.0025;
    e.latitude=e.latitude+offsetY;
    e.longitude=e.longitude+offsetX;
    e.latlng=L.latLng([e.latitude, e.longitude]);
    return e;
}*/

function randomizePos(e){
    return e;
}

function startAutolocate(){
    $("#btnAutolocate").html("On");
    storeSettings();
    clearInterval(intAutolocate);
    intAutolocate=setInterval(function(){
        if (mrkCurrentLocation) {
            mrkCurrentLocation.remove();
        }
        if($("#btnFilter").html()=="On"){
            var flt = $("#numFilter").val();
        } else {
            var flt= 100000;
        }
        if (posCurrent.accuracy<flt){
            var radius = Math.min(200, posCurrent.accuracy/2)
            radius = Math.max(10, radius)
            mrkCurrentLocation = L.circle(posCurrent.latlng, {radius:radius}).addTo(mymap);
            mymap.setView(posCurrent.latlng, 17);
        }
    }, $("#numAutolocate").val()*1000)
}

function stopAutolocate(){
    $("#btnAutolocate").html("Off");
    storeSettings();
    clearInterval(intAutolocate);
}

function startBreadcrumbs(){
    $("#btnBreadcrumbs").html("On");
    storeSettings();
    clearInterval(intBreadcrumbs);
    addBreadcrumb();
    intBreadcrumbs=setInterval(function(){
        if($("#btnFilter").html()=="On"){
            var flt = $("#numFilter").val();
        } else {
            var flt= 100000;
        }
        if (posCurrent.accuracy<flt){
            addBreadcrumb();
        }
    }, $("#numBreadcrumbs").val()*1000)
        clearInterval(intInfo);
        intInfo = setInterval(function(){
            populateInfo();
        },$("#numBreadcrumbs").val()*1000)
}

function stopBreadcrumbs(){
    $("#btnBreadcrumbs").html("Off");
    storeSettings();
    clearInterval(intBreadcrumbs);
    addBreadcrumb();
}

function addBreadcrumb(){
    if (posCurrent) {
        var radius = Math.min(200, posCurrent.accuracy/2)
        radius = Math.max(10, radius)
        var mrkBreadcrumb = L.circle(posCurrent.latlng, {radius:radius, color:'green'});
        mrkBreadcrumb.bindPopup("<h4>"+L.stamp(mrkBreadcrumb)+"</h4>Time: "+returnTimeFromUTC(posCurrent.timestamp)+"<br>Accuracy: "+posCurrent.accuracy+" m");
        lyrBreadcrumbs.addLayer(mrkBreadcrumb); 
        populatePoints();
    }
}

function populateInfo(){
    if(posCurrent){
        $(".info_cur_acc").html(posCurrent.accuracy.toFixed(0));
        if (isNaN(posCurrent.altitude)){
            posCurrent.altitude="NA";
        } else {
            posCurrent.altitude=posCurrent.altitude.toFixed(1);
        }
        $("#info_cur_lat").val(posCurrent.latitude.toFixed(6));
        $("#info_cur_lng").val(posCurrent.longitude.toFixed(6));
        $("#info_cur_alt").val(posCurrent.altitude);
        $("#info_cur_tm").val(returnTimeFromUTC(posCurrent.timestamp));

        if (posPrevious){
            $("#info_prv_lat").val(posPrevious.latitude.toFixed(6));
            $("#info_prv_lng").val(posPrevious.longitude.toFixed(6));
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

}

function populatePoints(){
    var lyrPrevious;
    var dst;
    var dstSum=0;
    var bng;
    var start;
    var tm;
    var strPopup;
    var strTable="<table class='table'><tr class='table-header'><th>ID</th><th>Time</th><th>Dist (m)</th><th>Bearing</th><th></th></tr>";
    lyrBreadcrumbs.eachLayer(function(lyr){
        if(lyrPrevious){
            strPopup=lyr.getPopup().getContent();
            start= strPopup.indexOf("Time: ")+6;
            tm= strPopup.substring(start, start+8);
            dst = lyrPrevious.getLatLng().distanceTo(lyr.getLatLng());
            dstSum+=dst;
            bng = L.GeometryUtil.bearing(lyrPrevious.getLatLng(), lyr.getLatLng())
            strTable+="<tr><td>"+L.stamp(lyr)+"</td><td>"+tm+"</td><td>"+dst.toFixed(1)+"</td><td>"+bng.toFixed(0)+"</td><td><span class='btnFindPt' data-id='"+L.stamp(lyr)+"'><i class='fa fa-search'></i></span></td></tr>";
            lyrPrevious=lyr;
        } else {
            strPopup=lyr.getPopup().getContent();
            start= strPopup.indexOf("Time: ")+6;
            tm= strPopup.substring(start, start+8);
            strTable+="<tr><td>"+L.stamp(lyr)+"</td><td>"+tm+"</td><td>NA</td><td>NA</td><td><span class='btnFindPt' data-id='"+L.stamp(lyr)+"'><i class='fa fa-search'></i></span></td></tr>";
            lyrPrevious=lyr;
        }
    });
    strTable+="<tr class='table-header'><th>Total</th><th></th><th>"+dstSum.toFixed(0)+"</th><th></th><th></th></tr>";
    strTable+="</table>";
    strTable+="<button id='btnClearCrumbs' class='btn btn-danger btn-block btn-no-top-margin'>Clear Breadcrumbs</button>";
    $("#points").html(strTable);
    $(".btnFindPt").click(function(){
        var id = $(this).attr("data-id");
        var ll = lyrBreadcrumbs.getLayer(id).getLatLng();
        mymap.setView(ll,17);
        openSubScreen();
    });
    $("#btnClearCrumbs").click(function(){
        if (confirm("Are you sure you want delete all the crumbs?")){
            lyrBreadcrumbs.clearLayers();
            if ($("#btnBreadcrumbs").html()=="On") {
                startBreadcrumbs();
            }
            populatePoints();
        }   
    });
}

function populateFeatures(tbl){
    $("#hdrFeatures").html(tbl);
    $.ajax({
        url:"generic_mobile_resources/php_generic_list.php",
        //url:"php/load_table.php",
        data:{tbl:tbl, user:user.username},
        type:'POST',
        success: function(response){
            $("#features").html(response);
            $(".btnFindGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                findFeature(table, id);
            });
            $(".btnNavGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                alert("Navigating to feature "+id+" in table "+table);
            });
            $(".btnEditGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                alert("Editing feature "+id+" in table "+table);
            });
            $(".btnDeleteGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                alert("Deleting feature "+id+" in table "+table);
            });
            openSubScreen("divFeatures");
        }, 
        error: function(xhr, status, error){
            $("#features").html("ERROR: "+error);
            openSubScreen("divFeatures");
        } 
    })
}

function populateFeaturesAO(tbl){
    $("#hdrFeatures").html(tbl);
    $.ajax({
        url:"generic_mobile_resources/php_generic_listAO.php",
        data:{tbl:tbl, user:user.username},
        type:'POST',
        success: function(response){
            $("#features").html(response);
            $(".btnFindGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                findFeature(table, id);
            });
            $(".btnNavGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                alert("Navigating to feature "+id+" in table "+table);
            });
            $(".btnEditGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                alert("Editing feature "+id+" in table "+table);
            });
            $(".btnDeleteGen").click(function(){
                var id = $(this).attr("data-id");
                var table = $(this).attr("data-table");
                alert("Deleting feature "+id+" in table "+table);
            });
            openSubScreen("divFeatures");
        }, 
        error: function(xhr, status, error){
            $("#features").html("ERROR: "+error);
            openSubScreen("divFeatures");
        } 
    })
}

function populateCollect(tbl){
    $("#gen_id").val("New");
    $("#gen_name").val("");
    $("#gen_comments").val("");
    $("#gen_lat").val(posCurrent.latitude.toFixed(6));
    $("#gen_lng").val(posCurrent.longitude.toFixed(6));
    $("#gen_geojson").val("");
    $("#hdrGenForm").html(tbl);
    $("#btnGenFormInsert").html("Insert into "+tbl);
    $("#btnGenFormInsert").show();
    $("#btnGenFormUpdate").hide();
    if (tbl=="generic_point"){
        $("#gen_form_pt").show();
        $("#gen_form_ln").hide();
    } else{
        $("#gen_form_pt").hide();
        $("#gen_form_ln").show();
    }
    openSubScreen("divGenForm");
}

function calculateAverage(){
    $("#divAverage").show();
    $("#btnScreenPoint").attr("disabled",true);
    $("#btnGPSAverage").attr("disabled",true);
    $("#btnGPSPoint").attr("disabled",true);
    $("#btnLayers").attr("disabled",true);
    dtAverageFinish=new Date(Date.now()+60000);
    arAverage=[];
    intAverage=setInterval(function(){
        var dt=new Date();
        var seconds = (dtAverageFinish-dt)/1000;
        if(seconds > 0){
            $("#divAverage").html(seconds.toFixed(0)+"s remaining");
            $("#mode").html("AVERAGING ("+seconds.toFixed(0)+"s)");
            arAverage.push(posCurrent.latlng);
            populateAverage(arAverage);
        }else{
            $("#mode").html("Basic");
            $("#divAverage").hide();
            $("#btnScreenPoint").attr("disabled",false);
            $("#btnGPSAverage").attr("disabled",false);
            $("#btnGPSPoint").attr("disabled",false);
            $("#btnLayers").attr("disabled",false);
            openSubScreen("divGenForm");
            clearInterval(intAverage);
        }
    }, 1000)
};

function populateAverage(arLL){
    var sumLat=0;
    var sumLng=0;
    arLL.forEach(function(ll,ndx){
        sumLat+=ll.lat;
        sumLng+=ll.lng;
    });
    $("#gen_lat").val((sumLat/arLL.length).toFixed(6));
    $("#gen_lng").val((sumLng/arLL.length).toFixed(6));
}


function storeSettings(){
    var jsnSettings={};
    jsnSettings.autolocate=$("#btnAutolocate").html();
    jsnSettings.numAutolocate=$("#numAutolocate").val();
    jsnSettings.breadcrumbs=$("#btnBreadcrumbs").html();
    jsnSettings.numBreadcrumbs=$("#numBreadcrumbs").val();
    jsnSettings.filter=$("#btnFilter").html();
    jsnSettings.numFilter=$("#numFilter").val();
    localStorage.jsnSettings=JSON.stringify(jsnSettings);
}

function refreshGPt() {
    $.ajax({url:'php/load_data.php', 
        data: {tbl:'generic_point', where:"createdby='"+user.username+"'"},
        type: 'POST',
        success: function(response){
            if (response.substring(0,5)=="ERROR"){
                alert(response);
            } else {
                jsnGPt = JSON.parse(response);
                if (lyrGPt) {
                    ctlLayers.removeLayer(lyrGPt);
                    lyrGPt.remove();
                }
                lyrGPt = L.geoJSON(jsnGPt, {pointToLayer:returnGPt}).addTo(mymap);
                ctlLayers.addOverlay(lyrGPt, "Generic Points");
            }
        }, 
        error: function(xhr, status, error){
           alert("ERROR: "+error);
        } 
    });
}

function returnGPt(jsn, ll){
    if (!jsn.properties.comments){
        jsn.properties.comments="";
    }
    return L.circleMarker(ll,{radius:10,color:'orange'}).bindPopup("<h4>"+jsn.properties.name+"</h4>"+jsn.properties.comments+"<br>Created by: "+jsn.properties.createdby);
}

function refreshGLn() {
    $.ajax({url:'php/load_data.php', 
        data: {tbl:'generic_line', where:"createdby='"+user.username+"'"},
        type: 'POST',
        success: function(response){
            if (response.substring(0,5)=="ERROR"){
                alert(response);
            } else {
                jsnGLn = JSON.parse(response);
                if (lyrGLn) {
                    ctlLayers.removeLayer(lyrGLn);
                    lyrGLn.remove();
                }
                lyrGLn = L.geoJSON(jsnGLn, {onEachFeature:processGLn, style:{color:'orange'}}).addTo(mymap);
                ctlLayers.addOverlay(lyrGLn, "Generic Lines");
            }
        }, 
        error: function(xhr, status, error){
           alert("ERROR: "+error);
        } 
    });
}

function processGLn(jsn, lyr){
    if (!jsn.properties.comments){
        jsn.properties.comments="";
    }
    lyr.bindPopup("<h4>"+jsn.properties.name+"</h4>"+jsn.properties.comments+"<br>Created by: "+jsn.properties.createdby);
}

function refreshGPly() {
    $.ajax({url:'php/load_data.php', 
        data: {tbl:'generic_poly', where:"createdby='"+user.username+"'"},
        type: 'POST',
        success: function(response){
            if (response.substring(0,5)=="ERROR"){
                alert(response);
            } else {
                jsnGPly = JSON.parse(response);
                if (lyrGPly) {
                    ctlLayers.removeLayer(lyrGPly);
                    lyrGPly.remove();
                }
                lyrGPly = L.geoJSON(jsnGPly, {onEachFeature:processGPly, style:{color:'orange'}}).addTo(mymap);
                ctlLayers.addOverlay(lyrGPly, "Generic Polygons");
            }
        }, 
        error: function(xhr, status, error){
           alert("ERROR: "+error);
        } 
    });
}

function processGPly(jsn, lyr){
    if (!jsn.properties.comments){
        jsn.properties.comments="";
    }
    lyr.bindPopup("<h4>"+jsn.properties.name+"</h4>"+jsn.properties.comments+"<br>Created by: "+jsn.properties.createdby);
}

function refreshAO() {
    $.ajax({url:'php/load_data.php', 
        data: {tbl:'ao_colonias_igg'},
        type: 'POST',
        success: function(response){
            if (response.substring(0,5)=="ERROR"){
                alert(response);
            } else {
                jsnAO = JSON.parse(response);
                if (lyrAO) {
                    ctlLayers.removeLayer(lyrAO);
                    lyrAO.remove();
                }
                lyrAO = L.geoJSON(jsnAO, {onEachFeature:processAO, style:{color:'orange'}}).addTo(mymap);
                ctlLayers.addOverlay(lyrAO, "Generic Mnz");
            }
        }, 
        error: function(xhr, status, error){
           alert("ERROR: "+error);
        } 
    });
}

function processAO(jsn, lyr){
    if (!jsn.properties.comments){
        jsn.properties.comments="";
    }
    lyr.bindPopup("<h4>"+jsn.properties.nombre+"</h4>");
}

function findFeature(tbl, id){
    $.ajax({
        url:"php/load_data.php",
        data:{tbl:tbl, where:"id="+id},
        type:"POST",
        success: function(response){
            if (response.substring(0,5)=="ERROR"){
                $("#features").append(response);
            } else {
                stopAutolocate();
                var jsn=JSON.parse(response).features[0];
                if(lyrSearch){
                    lyrSearch.remove();
                }
                if (jsn.geometry.type=="Point"){
                    var ll=L.latLng(jsn.geometry.coordinates[1],jsn.geometry.coordinates[0])
                    lyrSearch=L.circleMarker(ll,{radius:15, color:'darkred',weight:6}).addTo(mymap);
                    mymap.setView(ll, 17);
                } else{
                    lyrSearch=L.geoJSON(jsn,{style:{color:'darkred',weight:8}}).addTo(mymap);
                    mymap.fitBounds(lyrSearch.getBounds());
                }
                openSubScreen();
            }
        }, 
        error: function(xhr, status, error){
           $("#features").append("ERROR: "+error);
        } 
    });
}

function insertGenForm(tbl){
    var jsn=returnFormData("inpGenForm");
    if (jsn.name==""){
        alert("Please enter a name for this geometry");
    } else {
        jsn.tbl=tbl;
        delete jsn.id;
        if (tbl="generic_point"){
            var geojson={};
            geojson.type="Point";
            geojson.coordinates=[Number(jsn.lng), Number(jsn.lat)];
            jsn.geojson=JSON.stringify(geojson);
        }
        delete jsn.lng;
        delete jsn.lat;
        insertRecord(jsn, function(response){
            openSubScreen();
            switch(tbl){
                case "generic_point":
                    refreshGPt();
                    break;
            }
        });
    }
}