<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Service d'Urgence 🚒</title>
        <meta charset="utf-8"/>
        <!-- Lib Leaflet https://leafletjs.com/ -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
            integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
            crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
        <script src="js/script.js"></script>
        <script src="js/updater.js"></script>

        <!-- Style de la page WEB.-->
        <link rel="stylesheet" href="css/style.css"/>
    </head>
    <body>
        <div id="main">
            <div id="tableau-arrive">
                <div class="titre"><h1>Liste des Evenements</h1></div>
                <!-- –––––––––––– TAB AVEC LES INFORMATIONS SUR LES INCIDENTS –––––––––––– -->
                <div id ="tableau" >
                    <table>
                        <thead>
                            <tr>
                                <th>Incident</th>
                                <th>Rue</th>
                                <th>Intensité</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- –––––––––––– MAIN DIV MAP –––––––––––– -->
            <div id="map">
                <div class="titre"><h1>Carte</h1></div>
                <!-- –––––––––––– HTML DIV MAPBOX –––––––––––– -->
                <div id="mapbox"></div>
                <div id="params">
                    <!-- –––––––––––– HTML MENU PARAMETRES –––––––––––– -->
                    <h4>Éléments à afficher :</h4>
                    <div class="btn-afficher incidents">
                        <img class="iconParams" src="img/Incendie-3.png" alt="Incendies" />
                        <label class="switch">
                            <span class="slider round"> - Incendies</span>
                            <input type="checkbox" checked>
                        </label>
                    </div>
                    <div class="btn-afficher capteurs">
                        <img class="iconParams" src="img/detecteur.png" alt="Capteurs" />
                        <label class="switch">
                            <span class="slider round"> - Capteurs</span>
                            <input type="checkbox" checked>

                        </label>
                    </div>
                    <div class="btn-afficher casernes">
                        <img class="iconParams" src="img/caserne.png" alt="Casernes" />
                        <label class="switch">
                            <span class="slider round"> - Casernes</span>
                            <input type="checkbox" checked>

                        </label>
                    </div>
                    <div class="btn-afficher camions">
                        <img class="iconParams" src="img/camion.png" alt="Camions" />
                        <label class="switch">
                            <span class="slider round"> - Camions</span>
                            <input type="checkbox" checked>

                        </label>
                    </div>
                </div>
            </div>
            
        </div>
        <script type="text/javascript">
            
            
            /*A METTRE DANS UN FICHIER*/

            //RPZ Lyon 69 La Trick
            //45.754154744767455, 4.864503340336376

            /* –––––––––––– Génére la map –––––––––––– */
            const mapbox_token = "pk.eyJ1IjoidGVsbGVibWEiLCJhIjoiY2tuaXdleTY3MHM2dzJucGdpbGxsOXA3aCJ9.Lv06-rCdI3y9m0nC_0bWsg";
            var map = L.map('mapbox').setView([45.75415, 4.8645033], 12.5);

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: '',
                maxZoom: 18,
                id: 'mapbox/streets-v11', //fuck it. 
                tileSize: 512,
                zoomOffset: -1,
                accessToken: mapbox_token
            }).addTo(map);

            //Si possible voir pour mettre en gris les villes non prise en charge
            //https://github.com/mmaciejkowalski/L.Highlight

            //Containeurs avec tous les markeurs:
            var markers_incident = [] // 0 
            var markers_detecteur = [] // 1
            var markers_caserne = [] // 2 
            var markers_vehicule = [] // 3
            var markers_all = [markers_incident,markers_detecteur,markers_caserne,markers_vehicule]
            
            /* ––––––––––––DEFINE IMAGES––––––––––––*/
            function iconNiveau(type,niveau) {
                return L.icon({
                    iconUrl: `img/${type}-${niveau}.png`,   
                    iconSize:     [35,60],//[35, 60], // size of the icon
                    iconAnchor:   [22, 59], // point of the icon which will correspond to marker's location
                    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
                });
            }

            //todo mettre sous forme de fonction comme iconNiveau??
            //camion de pompier, sera utilisé dans la partie réel. 
            var iconCamionPompier = L.icon({
                iconUrl: 'img/camion.png',
                iconSize:     [35,35],//[35, 60], // size of the icon
                iconAnchor:   [22, 49], // point of the icon which will correspond to marker's location
                popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
            });

            //Caserne
            var iconCaserne = L.icon({
                iconUrl: 'img/caserne.png',
                iconSize:     [50,50],//[35, 60], // size of the icon
                iconAnchor:   [22, 29], // point of the icon which will correspond to marker's location
                popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
            });

            //Detecteur
            var iconDetecteur = L.icon({
                iconUrl: 'img/detecteur.png',
                iconSize:     [25,25],//[35, 60], // size of the icon
                iconAnchor:   [22, 29], // point of the icon which will correspond to marker's location
                popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
            });

            /*GET MARKER AUTOMATICLY IN JS BY API AT WEB SERVICE.  */

            updateall(map)
            setTimeout(function(){
            updateall(map)

            }, 5000);


        </script>
        <script type="text/javascript">
            /**
             * ––––––––––––ADDEventListener Params menu––––––––––––
             */
             var switchs = document.getElementsByClassName("switch")
             var listElement = ['incident','detecteur','caserne','vehicule']
            for (let index = 0; index < switchs.length; index++) {
                // get input.
                element = switchs[index].getElementsByTagName("input")[0]
                console.log(index)
                //add listener
                element.addEventListener('change',function(){
                    afficherOuCacherLesMarker(map,listElement[index])
                })
            }
        </script>
    </body>
    <footer>

    </footer>
</html>
