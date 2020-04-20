<?php include("includes/init.php");?>
<!--<?php 
    if (logged_in()) {
        $username=$_SESSION['username'];
        if (!verify_user_group($pdo, $username, "Guest")) {
            set_msg("User '{$username}' does not have permission to view this page");
            redirect('index.php');
        }
    } else {
        set_msg("Please log-in and try again");
        redirect('index.php');
    } 
?>-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Generic Data Collection">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        
        <title>Generic Data Collection</title>
        <link rel="stylesheet" href="src/leaflet.css">
        <link rel="stylesheet" href="src/css/bootstrap.css">
        <link rel="stylesheet" href="src/plugins/Leaflet.PolylineMeasure.css">
        <link rel="stylesheet" href="src/plugins/easy-button.css">
        <link rel="stylesheet" href="src/css/font-awesome.min.css">
        <link rel="stylesheet" href="src/plugins/leaflet.awesome-markers.css">
        <link rel="stylesheet" href="src/plugins/MarkerCluster.css">
        <link rel="stylesheet" href="src/plugins/MarkerCluster.Default.css">
        <link rel="stylesheet" href="generic_mobile_resources/css_generic_mobile.css">

        
        <script src="src/leaflet.js"></script>
        <script src="src/jquery-3.3.1.min.js"></script>
        <script src="src/plugins/Leaflet.PolylineMeasure.js"></script>
        <script src="src/plugins/easy-button.js"></script>
        <script src="src/plugins/leaflet-providers.js"></script>
        <script src="src/plugins/leaflet.awesome-markers.min.js"></script>
        <script src="src/plugins/leaflet.markercluster.js"></script>
        <script src="src/plugins/leaflet.geometryutil.js"></script>
        <script src="js/general_functions.js"></script>
        <script src="js/general_editing.js"></script>
        <script src="generic_mobile_resources/js_generic_mobile.js"></script>
        <script src="https://maps.google.com/maps/api/js?key=AIzaSyCQQSs0dPp_21EUf6eATD97BM6e432mi_E"></script>
        <script src="src/plugins/Leaflet.GoogleMutant.js"></script>
    </head>
    <body>
       
       <!-- Map screen -->
       
        <div id="divHeader" class="col-xs-12">
             <div class="pull-left col-xs-3 div-no-padding">(<span class="time_since_fix"></span>s)</div>
             <div class="col-xs-6 text-center div-no-padding">
                 <span id="btnStreamStop" class="stream-controls"><i class="fa fa-stop-circle fa-2x"></i></span>
             </div> 
             <div class="col-xs-3 div-no-padding"><span class="pull-right">(&plusmn; <span class="info_cur_acc"></span>m)</span></div>
             <div class="col-xs-12" id="mode" class="text-center"></div>
             <div class="container p-3 my-3 bg-dark text-white">
              <h3>PERCEPCIÓN LOCAL DEL RIESGO</h3>
              <hr class="my-1">
              <p>Levantamiento de Información en Campo</p>
             </div>
        </div>
        <div id="divMap" class="col-xs-12">
            <div id="divCross"><i class="fa fa-crosshairs fa-2x"></i></div>
        </div>
        <div id="divFooter" class="col-xs-12">
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <button id="btnMap" class="btn btn-light"><i class="fa fa-globe fa-2x"></i></button>
                </div>
                <div class="btn-group">
                    <button id="btnInfo" class="btn btn-light"><i class="fa fa-info fa-2x"></i></button>
                </div>
                <div class="btn-group">
                    <button id="btnLayers" class="btn btn-light"><i class="fa fa-object-ungroup fa-2x"></i></button>
                </div>
                <div class="btn-group">
                    <button id="btnPoints" class="btn btn-light"><i class="fa fa-list-alt fa-2x"></i></button>
                </div>
                <div class="btn-group">
                    <button id="btnSettings" class="btn btn-light"><i class="fa fa-cog fa-2x"></i></button>
                </div>
            </div>
        </div>
        
        <!-- Subscreens -->
        
        <div id="divInfo" class="modal">
            <div class="sub-header"><h3 class="text-center">Info</h3></div>
            <div id="info">
                <div class="col-xs-12"><h4 class="text-center">Current Pos (<span class="time_since_fix"></span>s &plusmn; <span class="info_cur_acc"></span>m)</h4></div>
                <div class="col-xs-6">Latitude</div>
                <div class="col-xs-6">Longitude</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_cur_lat" id="info_cur_lat" placeholder="Current Latitude" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_cur_lng" id="info_cur_lng" placeholder="Current Longitude" readonly>
                </div>
                <div class="col-xs-6">Altitude</div>
                <div class="col-xs-6">Time</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_cur_alt" id="info_cur_alt" placeholder="Current Altitude" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_cur_tm" id="info_cur_tm" placeholder="Current Time" readonly>
                </div>
                
                <div class="col-xs-12"><h4 class="text-center">Previous Pos</h4></div>
                <div class="col-xs-6">Latitude</div>
                <div class="col-xs-6">Longitude</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_prv_lat" id="info_prv_lat" placeholder="Previous Latitude" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_prv_lng" id="info_prv_lng" placeholder="Previous Longitude" readonly>
                </div>
                <div class="col-xs-6">Altitude</div>
                <div class="col-xs-6">Time</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_prv_alt" id="info_prv_alt" placeholder="Previous Altitude" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_prv_tm" id="info_prv_tm" placeholder="Previous Time" readonly>
                </div>   
                    
                <div class="col-xs-12"><h4 class="text-center">Difference</h4></div>
                <div class="col-xs-6">Distance</div>
                <div class="col-xs-6">Altitude</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_dif_dst" id="info_dif_dst" placeholder="Distance" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_dif_alt" id="info_dif_alt" placeholder="Altitude Change" readonly>
                </div>
                <div class="col-xs-6">Bearing</div>
                <div class="col-xs-6">Time</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_dif_bng" id="info_dif_bng" placeholder="Bearing" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_dif_tm" id="info_dif_tm" placeholder="Time" readonly>
                </div>   
                <div class="col-xs-6">Velocity (km/hr)</div>
                <div class="col-xs-6">Climb Rate (m/hr)</div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_dif_vel" id="info_dif_vel" placeholder="Velocity" readonly>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" name="info_dif_clr" id="info_dif_clr" placeholder="Climbing Rate" readonly>
                </div>
                <div id="loginInfo" class="text-center col-xs-12"></div>
            </div>
        </div>
        <div id="divLayers" class="modal">
            <div class="sub-header"><h3 class="text-center">Layers</h3></div>
            <div id="layers">
                <table class="table">
                    <tr><td>gen_point</td><td><i id="gen_point_collect" class="fa fa-plus"></i></td><td><i id="gen_point_list" class="fa fa-list-alt"></i></td><td><i id="gen_point_refresh" class="fa fa-refresh"></i></td><td><i id="gen_point_download" class="fa fa-cloud-download"></i></td></tr>
                    <tr><td>gen_line</td><td><i id="gen_line_collect" class="fa fa-plus"></i></td><td><i id="gen_line_list" class="fa fa-list-alt"></i></td><td><i id="gen_line_refresh" class="fa fa-refresh"></i></td><td><i id="gen_line_download" class="fa fa-cloud-download"></i></td></tr>
                    <tr><td>gen_poly</td><td><i id="gen_poly_collect" class="fa fa-plus"></i></td><td><i id="gen_poly_list" class="fa fa-list-alt"></i></td><td><i id="gen_poly_refresh" class="fa fa-refresh"></i></td><td><i id="gen_poly_download" class="fa fa-cloud-download"></i></td></tr>
                    <tr><td>COLONIAS</td><td><i id="gen_ao_list" class="fa fa-list-alt"></i></td></tr>
                </table>
            </div>
        </div>
        <div id="divFeatures" class="modal">
            <div class="sub-header"><h3 id="hdrFeatures" class="text-center">Features</h3></div>
            <div id="features"></div>
        </div>
        <div id="divGenForm" class="modal">
            <div class="sub-header"><h3 id="hdrGenForm" class="text-center">Features</h3></div>
            <div id="gen_form">
               <div class="form-group">
                    <label class="control-label col-xs-3" for="gen_id">ID:</label>
                    <div class="col-xs-9">
                          <input type="text" class="form-control inpGenForm" name="id" id="gen_id" placeholder="ID" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-3" for="gen_name">Name:</label>
                    <div class="col-xs-9">
                          <input type="text" class="form-control inpGenForm" name="name" id="gen_name" placeholder="Name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-3" for="gen_descr">Comments:</label>
                    <div class="col-xs-9">
                          <input type="text" class="form-control inpGenForm" name="descr" id="gen_descr" placeholder="Description">
                    </div>
                </div>
            </div>
            <div id="gen_form_pt">
                <div class="form-group">
                    <label class="control-label col-xs-3" for="gen_lat">Latitude:</label>
                    <div class="col-xs-9">
                          <input type="text" class="form-control inpGenForm" name="lat" id="gen_lat" placeholder="Latitude" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-3" for="gen_lng">Longitude:</label>
                    <div class="col-xs-9">
                          <input type="text" class="form-control inpGenForm" name="lng" id="gen_lng" placeholder="Longitude" readonly>
                    </div>
                </div>
                <div id="divAverage" class="col-xs-12 text-center">60s remaining</div>
                <div class="btn-group btn-group-justified col-xs-12">
                    <div class="btn-group">
                        <buttton id="btnGPSPoint" class="btn btn-info btn-block">GPS</buttton>
                    </div>
                    <div class="btn-group">
                        <buttton id="btnGPSAverage" class="btn btn-info btn-block">Average</buttton>
                    </div>
                    <div class="btn-group">
                        <buttton id="btnScreenPoint" class="btn btn-info btn-block">Screen</buttton>
                    </div>
                </div>

                
            </div>
            <div id="gen_form_ln">
                <div class="form-group">
                    <label class="control-label col-xs-3" for="gen_geojson">GeoJSON:</label>
                    <div class="col-xs-9">
                          <input type="text" class="form-control inpGenForm" name="geojson" id="gen_geojson" placeholder="GeoJSON">
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <buttton id="btnGenFormInsert" class="btn btn-success btn-block">Insert</buttton>
                <buttton id="btnGenFormUpdate" class="btn btn-warning btn-block">Update</buttton>
            </div>
            
        </div>
        <div id="divPoints" class="modal">
            <div class="sub-header"><h3 class="text-center">Points</h3></div>
            <div id="points"></div>
        </div>
        <div id="divSettings" class="modal">
            <div class="sub-header"><h3 class="text-center">Settings</h3></div>
            <div id="settings">
                <div class="col-xs-8">
                    <h4 class="setting-label">Autolocate: (<span id="valAutolocate">9</span>s)</h4>
                </div>
                <div class="col-xs-4">
                    <button id="btnAutolocate" class="btn btn-warning btn-block">Off</button>
                </div>
                <div id="sldrAutolocate" class="col-xs-12">
                    <div class="col-xs-1 div-no-padding">3s</div>
                    <div class="col-xs-10 div-no-padding">
                        <input id="numAutolocate" type="range" min="3" max="30" step="3" value="9">
                    </div>
                    <div class="col-xs-1 div-no-padding">30s</div>
                </div>
                <div class="col-xs-8">
                    <h4 class="setting-label">Breadcrumbs: (<span id="valBreadcrumbs">10</span>s)</h4>
                </div>
                <div class="col-xs-4">
                    <button id="btnBreadcrumbs" class="btn btn-warning btn-block">Off</button>
                </div>
                <div id="sldrBreadcrumbs" class="col-xs-12">
                    <div class="col-xs-1 div-no-padding">5s</div>
                    <div class="col-xs-10 div-no-padding">
                        <input id="numBreadcrumbs" type="range" min="5" max="60" step="5" value="10">
                    </div>
                    <div class="col-xs-1 div-no-padding">60s</div>
                </div>

                <div class="col-xs-8">
                    <h4 class="setting-label">Filter: (<span id="valFilter">30</span>m)</h4>
                </div>
                <div class="col-xs-4">
                    <button id="btnFilter" class="btn btn-warning btn-block">Off</button>
                </div>
                <div id="sldrFilter" class="col-xs-12">
                    <div class="col-xs-1 div-no-padding">5m</div>
                    <div class="col-xs-10 div-no-padding">
                        <input id="numFilter" type="range" min="5" max="50" step="5" value="30">
                    </div>
                    <div class="col-xs-1 div-no-padding">50m</div>
                </div>
                <div class="col-xs-12">
                    <button id="btnLogout" class="btn btn-danger btn-block">Logout</button>
                </div>
           
            </div>
        </div>
        
    </body>
    <script>
        var user;
        $.ajax({
            url:'php/return_user.php',
            success:function(response){
                if (response.substring(0,5)=="ERROR") {
                    alert(response);
                } else {
                    user=JSON.parse(response);
                    $("#loginInfo").html("Corrent User: "+user.username);
                    refreshGPt();
                    refreshGLn();
                    refreshGPly();
                    refreshAO();
                }
            }
        })
        var mymap;
        var ctlScale;
        var ctlLayers;
        var ctlMeasure;
        var lyrSearch;
        var lyrBreadcrumbs;
        var lyrGPt;
        var jsnGPt;
        var lyrGLn;
        var jsnGLn;
        var lyrGPly;
        var jsnGPly;
        var lyrAO;
        var jsnAO;
        var objBasemaps;
        var objOverlays;
        var mrkCurrentLocation;
        var posCurrent;
        var posPrevious;
        var posLastTime;
        var dtAverageFinish;
        var arAverage;
        var intAverage;
        var intAutolocate;
        var intBreadcrumbs;
        var intInfo;
        
        $(document).ready(function(){

            //  ********* Map Initialization ****************

            mymap = L.map('divMap', {center:[19.42, -99.18], zoom:13});
            
            mymap.locate();
            
            var roadMutant = L.gridLayer.googleMutant({
                maxZoom: 24,
                type:'roadmap'
            }).addTo(mymap);

            var hybridMutant = L.gridLayer.googleMutant({
                maxZoom: 24,
                type:'hybrid'
            });
            
            mymap.on("zoomend", function(e){
                console.log("Zoom level: ", mymap.getZoom());
            if(mymap.getZoom() > 15){ 
                  mymap.removeLayer(roadMutant);
                  hybridMutant.addTo(mymap);
                }else{
                  mymap.removeLayer(hybridMutant);
                  roadMutant.addTo(mymap);
                }
            });
            
            /********* Layer Initialization  ***************/
            
            lyrBreadcrumbs=L.layerGroup([]).addTo(mymap);

            /********* Setup Layer Control  ***************/
            
            objBasemaps = {
                "Google Streets":roadMutant,
                "Google Satellite":hybridMutant
            };

            objOverlays = {
                "Breadcrumbs":lyrBreadcrumbs
            };

            ctlLayers = L.control.layers(objBasemaps, objOverlays).addTo(mymap);

            ctlMeasure = L.control.polylineMeasure({position:'topright'}).addTo(mymap);

            ctlScale = L.control.scale({position:'bottomright', imperial:false, maxWidth:200}).addTo(mymap);
            
            mymap.locate();

            
//              ******  Load Data  ******



            // ************ Location Events **************
            
            setInterval(function(){
                mymap.locate();
                var dt=new Date();
                var tsf=((dt-posLastTime)/1000).toFixed(0);
                if (posPrevious) {
                    tsf+="s, "+((dt-posPrevious.timestamp)/1000).toFixed(0);
                }
                $(".time_since_fix").html(tsf);
            }, 1000);
            
            intInfo = setInterval(function(){
                populateInfo();
            },$("#numBreadcrumbs").val()*1000)
            
            mymap.on('locationfound', function(e) {
                $(".info_cur_acc").html(e.accuracy.toFixed(0));
                if ($("#btnFilter").html()=="On") {
                    var flt=$("#numFilter").val();
                } else {
                    var flt=100000;
                }
                if (e.accuracy<flt){
                posCurrent=randomizePos(e);
                posLastTime=new Date();   
                } else {
                    if(posCurrent)
                    posCurrent.accuracy=e.accuracy;
                }
            });

            mymap.on('locationerror', function(e) {
                console.log(e);
            })
            
            mymap.on('contextmenu',function(e){
                if(confirm("are you sure you want to create a generic_point at this location?")){
                    populateCollect("generic_point");
                    $("#gen_lat").val(e.latlng.lat.toFixed(6));
                    $("#gen_lng").val(e.latlng.lng.toFixed(6));
                }
            })
            
            if (localStorage.jsnSettings){
                var jsnSettings=JSON.parse(localStorage.jsnSettings);
                $("#btnAutolocate").html(jsnSettings.autolocate);
                $("#numAutolocate").val(jsnSettings.numAutolocate);
                $("#valAutolocate").html(jsnSettings.numAutolocate);
                if (jsnSettings.autolocate=="On"){
                    startAutolocate();
                }
                $("#btnBreadcrumbs").html(jsnSettings.breadcrumbs);
                $("#numBreadcrumbs").val(jsnSettings.numBreadcrumbs);
                $("#valBreadcrumbs").html(jsnSettings.numBreadcrumbs);
                if (jsnSettings.breadcrumbs=="On"){
                    startBreadcrumbs();
                }  
                $("#btnFilter").html(jsnSettings.filter);
                $("#numFilter").val(jsnSettings.numFilter);
                $("#valFilter").html(jsnSettings.numFilter);
            }
        });
        
        $("#btnMap").click(function(){
            openSubScreen();
        })
        
        $("#btnInfo").click(function(){
            openSubScreen("divInfo");
        })
        
        $("#btnLayers").click(function(){
            openSubScreen("divLayers");
        })
        
        $("#btnPoints").click(function(){
            populatePoints();
            openSubScreen("divPoints");
        })
        
        $("#btnSettings").click(function(){
            openSubScreen("divSettings");
        })
        
        // ******* Settings events handlers  
        
        $("#btnAutolocate").click(function(){
            if ($("#btnAutolocate").html()=="On"){
                stopAutolocate();
            } else {
                startAutolocate();
            }
        });

        $("#numAutolocate").on("change", function(){
            $("#valAutolocate").html($("#numAutolocate").val());
            startAutolocate();
        });

        $("#btnBreadcrumbs").click(function(){
            if ($("#btnBreadcrumbs").html()=="On"){
                stopBreadcrumbs();
            } else {
                startBreadcrumbs();
            }
        });
        $("#numBreadcrumbs").on("change", function(){
            $("#valBreadcrumbs").html($("#numBreadcrumbs").val());
            startBreadcrumbs();
        });
        
        $("#btnFilter").click(function(){
            if ($("#btnFilter").html()=="On"){
                $("#btnFilter").html("Off");
                storeSettings();
            } else {
               $("#btnFilter").html("On");
                storeSettings();
            }
        });

        $("#numFilter").on("change", function(){
            $("#valFilter").html($("#numFilter").val());
            storeSettings();
        });
        
        $("#btnLogout").click(function(){
            window.location="logout.php";
        });
        
        // ******* Layers event handlers
        
        // ******** generic_ao event handlers
        
        $("#gen_ao_list").click(function(){
            populateFeaturesAO("ao_colonias_igg");
        });

        // ******** generic_point event handlers
        
        $("#gen_point_collect").click(function(){
            populateCollect("generic_point");
        });
        
        $("#gen_point_list").click(function(){
            populateFeatures("generic_point");
        });
        
        $("#gen_point_refresh").click(function(){
            alert("Refreshing point data for "+user.username);
            refreshGPt();
        });
        
        $("#gen_point_download").click(function(){
            alert("Point download not enabled");
        });
        
        $("#btnGPSPoint").click(function(){
            $("#gen_lat").val(posCurrent.latitude.toFixed(6));
            $("#gen_lng").val(posCurrent.longitude.toFixed(6));
        });
        
        $("#btnGPSAverage").click(function(){
            calculateAverage();
        });

        
        $("#btnScreenPoint").click(function(){
            stopAutolocate();
            openSubScreen();
            $(".stream-controls").show();
            $("#mode").html("");
            $("#btnLayers").attr("disabled",true);
        })
        
        // ******** generic_line event handlers
        
        $("#gen_line_collect").click(function(){
            populateCollect("generic_line");
        });
        
        $("#gen_line_list").click(function(){
            populateFeatures("generic_line");
        });
        
        $("#gen_line_refresh").click(function(){
            alert("Refreshing line data for "+user.username);
            refreshGLn();
        });
        
        $("#gen_line_download").click(function(){
            alert("Line download not enabled");
        });
        
        // ******** generic_poly event handlers
        
        $("#gen_poly_collect").click(function(){
            populateCollect("generic_poly");
        });
        
        $("#gen_poly_list").click(function(){
            populateFeatures("generic_poly");
        });
        
        $("#gen_poly_refresh").click(function(){
            alert("Refreshing poly data for "+user.username);
            refreshGPly();
        });
        
        $("#gen_poly_download").click(function(){
            alert("Poly download not enabled");
        });
        
        // *** Generic event handlers
        $("#btnGenFormInsert").click(function(){
            insertGenForm($("#hdrGenForm").html());
        });
        
        $("#btnStreamStop").click(function(){
            $("#mode").html("");
            $("#gen_lat").val(mymap.getCenter().lat.toFixed(6));
            $("#gen_lng").val(mymap.getCenter().lng.toFixed(6));
            openSubScreen("divGenForm");
            $(".stream-controls").hide();
            $("#btnLayers").attr("disabled", false);
        });
        
    </script>
</html>