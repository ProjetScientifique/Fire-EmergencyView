<?php 
    require_once('API.php');
    
    $TOKEN = 'CB814D37E278A63D3666B1A1604AD0F5C5FD7E177267F62B8D719F49182F410A';
    /**
     * RECUPERATION DES EVENEMENTS
     * API-doc :
     *      html://127.0.0.1:8000/docs 
     */
    
    $API = new API();
    $incidents = $API->getIncident($TOKEN);
    $json_incidents=json_decode($incidents,TRUE);

    $casernes = $API->getCasernes($TOKEN);
    $json_casernes=json_decode($casernes,TRUE);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Service d'Urgence 🚒</title>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
        <link rel="stylesheet" href="css/style.css"/>
    </head>
    <body>
        <div id="main">
            <div id="tableau-arrive">
                <div class="titre"><h1>Liste des Evenements</h1></div>
                
                <div id ="tableau" >
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Incident</th>
                                <th>Rue</th>
                                <th>Intensité</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php 
                                /*
                                <div></br>
                                    🟢 = Traité</br>
                                    🟡 = Pris en charge</br>
                                    🔴 = Non traité</br></br>
                                </div>
                                */
                                foreach ($json_incidents as $incident) {
                                    
                                    //address : translate by API.
                                    try {
                                        $address = $API->getAddressFromCoords($incident['latitude_incident'],$incident['longitude_incident']);
                                        $addressJson=json_decode($address,TRUE);
                                        #print_r($addressJson);
                                        $address = $addressJson['address'];
                                        #print_r($address);
                                        #print_r($address["city"]);
                                        #print_r($address["road"]);
                                        #print_r($address["suburb"]);
                                        $AffichageRue = $address["city"].' - '.$address["road"].','.$address["suburb"];

                                    } catch (Exception $e) {
                                        print_r($e);
                                        $AffichageRue = strval($incident['latitude_incident'])+" "+strval($incident['longitude_incident']);
                                    }


                                    //formate le tableau.
                                    echo("<tr>
                                            <td>".$incident['type_status_incident']['nom_type_status_incident']."</td>
                                            <td>".$incident['type_incident']['nom_type_incident']."</td>
                                            <td>".$AffichageRue."</td>
                                            <td class='intensite'>".$incident['intensite_incident']."</td>
                                        </tr>\n");
                                }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="map">
                <div class="titre"><h1>Carte</h1></div>
                <div id="mapbox"></div>
            </div>
            
        </div>
        <script type="text/javascript">
            /*A METTRE DANs UN FICHIER*/
            //45.754154744767455, 4.864503340336376
            const mapbox_token = "pk.eyJ1IjoidGVsbGVibWEiLCJhIjoiY2tuaXdleTY3MHM2dzJucGdpbGxsOXA3aCJ9.Lv06-rCdI3y9m0nC_0bWsg";
            var map = L.map('mapbox').setView([45.75415, 4.8645033], 12.5);

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: '',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: mapbox_token
            }).addTo(map);

            //Si possible voir pour mettre en gris les villes non prise en charge
            //https://github.com/mmaciejkowalski/L.Highlight

            /*DEFINE IMAGES*/
            function iconNiveau(type,niveau) {
                return L.icon({
                    iconUrl: `img/${type}-${niveau}.png`,   
                    iconSize:     [35,60],//[35, 60], // size of the icon
                    iconAnchor:   [22, 59], // point of the icon which will correspond to marker's location
                    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
                });
            }

            //camion de pompier, sera utilisé dans la partie réel. 
            var iconCamionPompier = L.icon({
                iconUrl: 'img/camion.png',
                iconSize:     [35,35],//[35, 60], // size of the icon
                iconAnchor:   [22, 49], // point of the icon which will correspond to marker's location
                popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
            });

            //point d'eau (peu être utile plus tard.)
            var iconCaserne = L.icon({
                iconUrl: 'img/caserne.png',
                iconSize:     [50,50],//[35, 60], // size of the icon
                iconAnchor:   [22, 29], // point of the icon which will correspond to marker's location
                popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
            });


            /*GET MARKER AUTOMATICLY IN JS BY API AT WEB SERVICE.  */

            /*ADD A MARKER*/
            /*
            var latitude =  45.7596
            var longitude = 4.8523
            var adresse = "Lyon"
            var zoom = 15
            var niveau = 4
            var marker = L.marker([latitude, longitude],{icon:iconNiveau(niveau)}).addTo(map);
            marker.bindPopup(`Incendie de Niveau ${niveau} </br><a href="https://www.google.fr/maps/@${latitude},${longitude},${zoom}z">Coordonnées : ${latitude}, ${longitude}</a></br>${adresse}`)
            */
            /*
            var latitude =  45.77792
            var longitude = 4.88204646
            var adresse = "Villeurbanne"
            var zoom = 15
            var nombre_de_pompier = 25
            var marker = L.marker([latitude, longitude],{icon:iconCamionPompier}).addTo(map);
            marker.bindPopup(`Camion de pompier ${nombre_de_pompier} </br>Se dirige sur le lieu de l'incendie.`)
            */

            /*
            var latitude =  45.74792
            var longitude = 4.83204646
            var adresse = "Lyon"
            var zoom = 15
            var niveau = 2
            var marker = L.marker([latitude, longitude],{icon:iconPointEau}).addTo(map);
            marker.bindPopup(`Point d'eau </br><a href="https://www.google.fr/maps/@${latitude},${longitude},${zoom}z">Coordonnées : ${latitude}, ${longitude}</a></br>${adresse}`)
            */


        

            
            //PHP ADD INCIDENTS MARKERS.
            var latitude_incident_array = []
            var longitude_incident_array = []

            var affichageRue_incident_array = []
            //var adresse_array = []
            //var zoom_array = []
            var intensite_incident_array = []
            var date_incident_incident_array = []
            
            //Seulement ID ... Voir ajouter nom. (incendie etc...)
            var type_incident_incident_array = []

            //AUTOMATICLY GENERATED BY PHP
            <?php 
                foreach ($json_incidents as $incident) {
                    try {
                        $address = $API->getAddressFromCoords($incident['latitude_incident'],$incident['longitude_incident']);
                        $addressJson=json_decode($address,TRUE);
                        #print_r($addressJson);
                        $address = $addressJson['address'];
                        #print_r($address);
                        #print_r($address["city"]);
                        #print_r($address["road"]);
                        #print_r($address["suburb"]);
                        $AffichageRue = $address["city"].' - '.$address["road"].','.$address["suburb"];

                    } catch (Exception $e) {
                        print_r($e);
                        $AffichageRue = strval($incident['latitude_incident'])+" "+strval($incident['longitude_incident']);
                    }

                    echo("affichageRue_incident_array.push('".$AffichageRue."');\n");
                    echo("latitude_incident_array.push('".$incident['latitude_incident']."');\n");
                    echo("longitude_incident_array.push('".$incident['longitude_incident']."');\n");
                    echo("intensite_incident_array.push('".$incident['intensite_incident']."');\n");
                    echo("date_incident_incident_array.push('".$incident['date_incident']."');\n");
                    echo("type_incident_incident_array.push('".$incident['type_incident']['nom_type_incident']."');\n");
                }
            ?>
            for (let index = 0; index < latitude_incident_array.length; index++) {
                var latitude =  latitude_incident_array[index]
                var longitude = longitude_incident_array[index]
                var adresse = affichageRue_incident_array[index]
                var date_incident = date_incident_incident_array[index]
                var zoom = 15
                var intensite = intensite_incident_array[index]
                var type_incident = type_incident_incident_array[index]
                var intensite_1_a_3 = Math.round(parseInt(intensite)/100*2)+1
                var marker = L.marker([latitude, longitude],{icon:iconNiveau(type_incident,intensite_1_a_3)}).addTo(map);
                marker.bindPopup(`${type_incident} de Niveau ${intensite} </br><a href="https://www.google.fr/maps/@${latitude},${longitude},${zoom}z">Coordonnées : ${latitude}, ${longitude}</a></br>${adresse}</br>${date_incident}`)
            }


            //PHP ADD CASERNES MARKERS.
            var latitude_caserne_array = []
            var longitude_caserne_array = []

            var affichageRue_caserne_array = []
            //var adresse_array = []
            //var zoom_array = []
            var nom_caserne_array = []

            //AUTOMATICLY GENERATED BY PHP
            <?php 
                foreach ($json_casernes as $caserne) {
                    //boucle for avec les casernes
                    try {
                        //récupère l'Adresse via les coordonnées.
                        $address = $API->getAddressFromCoords($caserne['latitude_caserne'],$caserne['longitude_caserne']);
                        $addressJson=json_decode($address,TRUE);
                        $address = $addressJson['address'];
                        // City - Rue, Quartier
                        $AffichageRue = $address["city"].' - '.$address["road"].', '.$address["suburb"];

                    } catch (Exception $e) {
                        print_r($e);// gestion erreur, (cas erreur: si les coordonnées n'ont pas de nom de rue.)
                        $AffichageRue = strval($caserne['latitude_caserne'])+" "+strval($caserne['longitude_caserne']);
                    }
                    
                    //
                    $id_caserne = $caserne['id_caserne'];
                    $pompierCaserne = $API->getPompiersOfCaserne($TOKEN);
                    $pompiers = json_decode($pompierCaserne,TRUE);
                    //foreach
                    // TODO
                    
                    $vehiculeCaserne = $API->getVehiculesOfCaserne($TOKEN);
                    $vehicules = json_decode($vehiculeCaserne,TRUE);
                    //foreach
                    // TODO


                    
                    
                    //ajout en dans un array Js 
                    echo("affichageRue_caserne_array.push('".$AffichageRue."');\n");
                    echo("latitude_caserne_array.push('".$caserne['latitude_caserne']."');\n");
                    echo("longitude_caserne_array.push('".$caserne['longitude_caserne']."');\n");
                    echo("nom_caserne_array.push('".$caserne['nom_caserne']."');\n");

                    // recuperer le nombre de véhicules dans une caserne
                    // nombreVehiculeDisponible=  //

                }
            ?>
            for (let index = 0; index < latitude_caserne_array.length; index++) {
                //Ajout du marker.

                //coords
                var latitude =  latitude_caserne_array[index]
                var longitude = longitude_caserne_array[index]
                //nom caserne
                var nom = nom_caserne_array[index]
                //nom de Rue
                var adresse = affichageRue_caserne_array[index]
                //zoom sur google map ?
                var zoom = 15
                
                // recuperer le nombre de véhicules dans une caserne
                var nombreVehiculeDisponible= 10 //
                
                var nombreVehiculeNonDisponible = 3
                var nombrePompierDisponible = 21
                var nombrePompierEnInterventionDisponible = 10
                var nombrePompierNonDisponible = 5
                
                //modifier ca ...........;
                var marker = L.marker([latitude, longitude],{icon:iconCaserne}).addTo(map);
                marker.bindPopup(`
                <h1 class="title">Caserne de Pompier</h1>
                <h2 class="NomCaserne">${nom}</h2></br>
                <a href="https://www.google.fr/maps/@${latitude},${longitude},${zoom}z">
                    <h4 class="adresse">${adresse}</h4>
                </a></br>
                <b>Coordonnées</b> : ${latitude}, ${longitude}</br></br>
                <h4 class="vehicule center_dispo">🚒 Véhicules:</h4>
                <div class="disponnibilite_vehicule">
                    <div class="dispo center_dispo"><abbr title="Disponible">🟢</abbr> ${nombreVehiculeDisponible}</div>
                    <div class="non_dispo center_dispo"><abbr title="En Intervention">🔴</abbr> ${nombreVehiculeNonDisponible}</div>
                </div>
                <h4 class="pompier center_dispo">👩‍🚒 Pompiers:</h4>
                <div class="disponnibilite_pompier">
                    <div class="dispo center_dispo"><abbr title="Disponible">🟢</abbr> ${nombrePompierDisponible}</div>
                    <div class="temp_non_dispo center_dispo"><abbr title="En Intervention">🟡</abbr> ${nombrePompierEnInterventionDisponible}</div>
                    <div class="non_dispo center_dispo"><abbr title="Non Disponnible pour le moment">🔴</abbr> ${nombrePompierNonDisponible}</div>
                </div>
                `)
                //JE METTRAIS BIEN SOUS CETTE FORME :
                /*
                        Véhicules:
                      🟢 10    🔴 5
                        Pompiers:
                    🟢 23  🟡 5   🔴 2

                */

            }
            
            


        </script>
        
   
    </body>
    <footer>

    </footer>
</html>