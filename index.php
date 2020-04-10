
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
        <script src="js/general_functions.js"></script>
        <script src="js/general_editing.js"></script>
        <script src="generic_mobile_resources/js_generic_mobile.js"></script>
        <script src="https://maps.google.com/maps/api/js?key=AIzaSyCQQSs0dPp_21EUf6eATD97BM6e432mi_E"></script>
        <script src="src/plugins/Leaflet.GoogleMutant.js"></script>
    </head>
    <body>
       
       <!-- Map screen -->
       
        <div id="divHeader" class="col-xs-12">
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
                Info Content
            </div>
        </div>
        <div id="divLayers" class="modal">
            <div class="sub-header"><h3 class="text-center">Layers</h3></div>
            <div id="info">
                Layer Content
            </div>
        </div>
        <div id="divPoints" class="modal">
            <div class="sub-header"><h3 class="text-center">Points</h3></div>
            <div id="info">
            <div id="info">
                Point
            </div>
            </div>
        </div>
        <div id="divSettings" class="modal">
            <div class="sub-header"><h3 class="text-center">Settings</h3></div>
            <div id="info">
                <div class="col-xs-8">
                    <h4 class="setting-label">Autolocate: (<span id="valAutolocate">9</span>s)</h4>
                </div>
                <div class="col-xs-4">
                    <button id="btnAutolocate" class="btn btn-warning btn-block">Off</button>
                </div>
                <div id="sldrAutolocate" class="col-xs-12">
                    <div class="col-xs-1">3s</div>
                    <div class="col-xs-8">
                        <input id="numAutolocate" type="range" min="3" max="30" step="3" value="9">
                    </div>
                    <div class="col-xs-2">30s</div>
                </div>
                <div class="col-xs-8">
                    <h4 class="setting-label">Breadcrumbs: (<span id="valBreadcrumbs">10</span>s)</h4>
                </div>
                <div class="col-xs-4">
                    <button id="btnBreadcrumbs" class="btn btn-warning btn-block">Off</button>
                </div>
                <div id="sldrBreadcrumbs" class="col-xs-12">
                    <div class="col-xs-1">5s</div>
                    <div class="col-xs-8">
                        <input id="numBreadcrumbs" type="range" min="5" max="60" step="5" value="10">
                    </div>
                    <div class="col-xs-2">60s</div>
                </div>
            </div>
        </div>
        
    </body>
    <script>
        var mymap;
        var ctlScale;
        var ctlLayers;
        var ctlMeasure;
        var lyrBreadcrumbs;
        var objBasemaps;
        var objOverlays;
        var mrkCurrentLocation;
        var posCurrent;
        var posLastTime;
        var intAutolocate;
        var intBreadcrumbs;
        
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
            };

            ctlLayers = L.control.layers(objBasemaps, objOverlays).addTo(mymap);

            ctlMeasure = L.control.polylineMeasure({position:'topright'}).addTo(mymap);

            ctlScale = L.control.scale({position:'bottomright', imperial:false, maxWidth:200}).addTo(mymap);
            
            mymap.locate();

            
//              ******  Load Data  ******



            // ************ Location Events **************
            
            setInterval(function(){
                mymap.locate();
            }, 1000);
            
            mymap.on('locationfound', function(e) {
                posCurrent=randomizePos(e);
                posLastTime=new Date();
            });

            mymap.on('locationerror', function(e) {
                console.log(e);
            })
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
            openSubScreen("divPoints");
        })
        
        $("#btnSettings").click(function(){
            openSubScreen("divSettings");
        })
        
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
        
    </script>
</html>
