<?php 
    require_once('API.php');
    
    /** –––––––––––– TOKEN API –––––––––––– */
    //pourrait être mis dans un fichier private qui lui meme serait dans le .gitignoe (plus de sécu mais flemme.)
    $TOKEN = 'CB814D37E278A63D3666B1A1604AD0F5C5FD7E177267F62B8D719F49182F410A';
    
    /**
     * RECUPERATION DES ELEMENTS DE LA DATABASE.
     * API-doc :
     *      html://127.0.0.1:8000/docs 
     */
    
    $API = new API(); //Classe API . (API.php)

    /*–––––––––––– Récupere la totalité des incidents –––––––––––– */
    $incidents = $API->getIncident($TOKEN);
    $json_incidents=json_decode($incidents,TRUE);
    /*–––––––––––– Récupere la totalité des casernes ––––––––––––*/
    //seront récuperé plus tard les : pompiers et véhicules
    $casernes = $API->getCasernes($TOKEN);
    $json_casernes=json_decode($casernes,TRUE);
    /*–––––––––––– Récupere la totalité des Capteurs(détécteurs) –––––––––––– */
    $detecteurs = $API->getDetecteur($TOKEN);
    $json_detecteurs=json_decode($detecteurs,TRUE);

?>
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
                            <?php 
                                foreach ($json_incidents as $incident) {
                                    //address : translate by API.
                                    /*try {
                                        #$address = $API->getAddressFromCoords($incident['latitude_incident'],$incident['longitude_incident']);
                                        #$addressJson=json_decode($address,TRUE);
                                        #$address = $addressJson['address'];
                                        // sous la forme de : Lyon - Rue Garibaldi, Part-Dieu
                                        #$AffichageRue = $address["city"].' - '.$address["road"].', '.$address["suburb"];
                                        #if ($AffichageRue == " - , "){
                                        #    $AffichageRue = strval($incident['latitude_incident'])." ".strval($incident['longitude_incident']);
                                        #}
                                        
                                    } catch (Exception $e) {
                                        print_r($e);//cas erreur (afficher = DEBUG)
                                        #$AffichageRue = $incident['latitude_incident'].", ".$incident['longitude_incident'];
                                    }*/
                                    $AffichageRue = $incident['latitude_incident'].", ".$incident['longitude_incident'];
                                    ?>
                                    
                                    <tr class='<?php echo(str_replace(" ","_",$incident['type_status_incident']['nom_type_status_incident'])); ?>'>
                                        <td><?php echo($incident['type_incident']['nom_type_incident']); ?></td>
                                        <td><?php echo($AffichageRue); ?></td>
                                        <td class='intensite'><?php echo($incident['intensite_incident']); ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>
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

            /* –––––––––––– ADD MARKERS –––––––––––– */
            //AUTOMATICLY GENERATED BY PHP
            <?php 
                foreach ($json_detecteurs as $detecteur) {
                    ?> 
                        var latitude =  <?php echo($detecteur['latitude_detecteur']); ?> //coord lat
                        var longitude = <?php echo($detecteur['longitude_detecteur']); ?> //coord long
                        
                        //Ajout du marker: avec une image (type_incident, et une intensité de 1 à 3.
                        var marker = L.marker([latitude, longitude],{icon:iconDetecteur}).addTo(map);
                        markers_detecteur.push(marker)//ajout du marker dans un tableau (utilisé pour cacher les markeurs de type :)
                    <?php
                }
            ?>
            
            //AUTOMATICLY GENERATED BY PHP
            <?php 
                foreach ($json_incidents as $incident) {
                    try {
                        $address = $API->getAddressFromCoords($incident['latitude_incident'],$incident['longitude_incident']);
                        $addressJson=json_decode($address,TRUE);
                        $address = $addressJson['address'];
                        // sous la forme de : Lyon - Rue Garibaldi, Part-Dieu
                        $AffichageRue = $address["city"].' - '.$address["road"].', '.$address["suburb"];
                        if ($AffichageRue == " - , "){
                            $AffichageRue = strval($incident['latitude_incident'])." ".strval($incident['longitude_incident']);
                        }

                    } catch (Exception $e) {
                        print_r($e);//cas erreur (afficher = DEBUG)
                        $AffichageRue = strval($incident['latitude_incident'])." ".strval($incident['longitude_incident']);
                    }
                    ?> 
                        var latitude =  <?php echo($incident['latitude_incident']); ?> //coord lat
                        var longitude = <?php echo($incident['longitude_incident']); ?> //coord long
                        var adresse = '<?php echo($AffichageRue); ?>' //adresse postal calculer par l'API. (pr faire jolie...)
                        var date_incident = Date('<?php echo($incident['date_incident']); ?>') //date apparition
                        var intensite = <?php echo($incident['intensite_incident']); ?> //1 à 100
                        var type_incident = '<?php echo($incident['type_incident']['nom_type_incident']); ?>' // prit en charge (incendie)
                        var status = '<?php echo($incident['type_status_incident']['nom_type_status_incident']); ?>'
                        var intensite_1_a_3 = Math.round(parseInt(intensite)/10*2)+1
                        
                        //Ajout du marker: avec une image (type_incident, et une intensité de 1 à 3.
                        var marker = L.marker([latitude, longitude],{icon:iconNiveau(type_incident,intensite_1_a_3)}).addTo(map);
                        marker.bindPopup(`
                        <h1 class="title">${type_incident} de Niveau ${intensite}</h1>
                        <h2 class="date_incident">${date_incident.toLocaleString('fr-FR', { timeZone: 'UTC' })}</h2>
                        <a href="https://www.google.fr/maps/@${latitude},${longitude},15z">
                        <h4 class="adresse">${adresse}</h4>
                        </a></br>
                        <b>Coordonnées</b> : lat:${latitude}, long:${longitude}</br></br>
                        <h4 class="status incident">
                            Incident ${status}
                        </h4>`)
                        markers_incident.push(marker)//ajout du marker dans un tableau (utilisé pour cacher les markeurs de type :)

                    <?php
                }
            ?>


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
                        if ($AffichageRue == " - , "){
                            $AffichageRue = strval($incident['latitude_incident'])." ".strval($incident['longitude_incident']);
                        }

                    } catch (Exception $e) {
                        print_r($e);//cas erreur (afficher = DEBUG)
                        $AffichageRue = strval($incident['latitude_incident'])." ".strval($incident['longitude_incident']);
                    }
                    
                    // TODO
                    $id_caserne = $caserne['id_caserne'];
                    
                    
                    $pompierCaserne = $API->getPompiersOfCaserne($TOKEN, $id_caserne);
                    //print_r($pompierCaserne);
                    
                    $pompiers = json_decode($pompierCaserne,TRUE);
                    //print_r($pompiers);
                    $pompierLibre = 0;
                    $pompierOccupe = 0;
                    $pompierTotal = 0;
                    $pompier_grade_array = [];
                    $pompier_grade_dispo_array = [];
                    $pompier_grade_occupe_array = [];

                    foreach ($pompiers as $pompier) {
                        

                        if (array_key_exists(str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"]),$pompier_grade_array)){
                            //grade + nombre total
                            $pompier_grade_array[str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"])]++;
                        }else{
                            //si c'est la premiere fois qu'on voit le grade alors on met le nombre de pompier a 1.
                            $pompier_grade_array[str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"])] = 1;

                            //on met a 0 les deux tableau dispo et occupé.
                            $pompier_grade_dispo_array[str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"])]=0;
                            $pompier_grade_occupe_array[str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"])]=0;
                        }

                        //on popule les 2 tableau précédament créé.
                        if ($pompier["disponibilite_pompier"]){
                            $pompier_grade_dispo_array[str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"])]++;
                        }else {
                            $pompier_grade_occupe_array[str_replace(" ","_",$pompier["type_pompier"]["nom_type_pompier"])]++;
                        }
                        


                    }
                    $vehiculeCaserne = $API->getVehiculesOfCaserne($TOKEN, $id_caserne);
                    //print_r($vehiculeCaserne);
                    $vehicules = json_decode($vehiculeCaserne,TRUE);
                    //print_r($vehicules);
                    
                    //foreach
                    // TODO

                    $vehiculeLibre = 0;
                    $vehiculeOccupe = 0;
                    $vehiculeTotal = 0;
                    $vehicule_type_array = [];


                    foreach ($vehicules as $vehicule) {
                        

                        if (array_key_exists(str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"]),$vehicule_type_array)){
                            $vehicule_type_array[str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"])]++;
                        }else{
                            $vehicule_type_array[str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"])] = 1;
                            //on met a 0 les deux tableau dispo et occupé.
                            $vehicule_type_dispo_array[str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"])]=0;
                            $vehicule_type_occupe_array[str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"])]=0;
                        }
                        
                        if ($vehicule["type_disponibilite_vehicule"]["nom_type_disponibilite_vehicule"] == "Disponible"){
                            $vehicule_type_dispo_array[str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"])]++;
                        }else {
                            $vehicule_type_occupe_array[str_replace(" ","_",$vehicule["type_vehicule"]["nom_type_vehicule"])]++;
                            //alors on affiche le camion.
                            ?>
                            var latitude =  <?php echo($vehicule['latitude_vehicule']); ?> //lat
                            var longitude = <?php echo($vehicule['longitude_vehicule']); ?> //long 
                            //nom caserne
                            var nom_type_vehicule = '<?php echo($vehicule['type_vehicule']['nom_type_vehicule']);?>' //nom
                            var capacite_type_vehicule = '<?php echo($vehicule['type_vehicule']['capacite_type_vehicule']);?>' //capacite
                            var puissance_intervention_type_vehicule = '<?php echo($vehicule['type_vehicule']['puissance_intervention_type_vehicule']);?>' //puissance
                            var nom_caserne = '<?php echo($vehicule['caserne']['nom_caserne']);?>' //nom caserne
                            var annee_vehicule = '<?php echo($vehicule['annee_vehicule']);?>' //annee 
                            var nombre_intervention_maximum_vehicule = '<?php echo($vehicule['nombre_intervention_maximum_vehicule']);?>'
                            //nom de Rue

                            var marker = L.marker([latitude, longitude],{icon:iconCamionPompier}).addTo(map);
                            marker.bindPopup(`
                            <h1 class="title">Camion de Pompier</h1>
                            <h2 class="NomVehicule">${nom_type_vehicule}</h2>
                            <h2 class="NomCaserne">Caserne ${nom_caserne}</h2></br>
                            <b>Coordonnées</b> :
                            <a href="https://www.google.fr/maps/@${latitude},${longitude},15z">
                                <h4 class="adresse">${latitude},${longitude}</h4>
                            </a></br>
                            <h4>Caractéristiques</h4>
                            <div>
                                <span>Capacités : <b>${capacite_type_vehicule}</b></span></br>
                                <span>Puissance du Véhicule : <b>${puissance_intervention_type_vehicule}</b></span></br>
                                <span>Nombre d'intervention maximum : <b>${nombre_intervention_maximum_vehicule}</b></span></br>
                                <span>Année construction : <b>${annee_vehicule}</b></span></br>

                            </div>
                            `)
                            <?php
                        }


                    }

                    ?>
                    //coords
                    var latitude =  <?php echo($caserne['latitude_caserne']); ?> //lat
                    var longitude = <?php echo($caserne['longitude_caserne']); ?> //long 
                    //nom caserne
                    var nom = '<?php echo($caserne['nom_caserne']);?>' //nom
                    //nom de Rue
                    var adresse = '<?php echo($AffichageRue);?>' //addre

                    var marker = L.marker([latitude, longitude],{icon:iconCaserne}).addTo(map);
                    marker.bindPopup(`
                    <h1 class="title">Caserne de Pompier</h1>
                    <h2 class="NomCaserne">${nom}</h2></br>
                    <a href="https://www.google.fr/maps/@${latitude},${longitude},15z">
                        <h4 class="adresse">${adresse}</h4>
                    </a></br>
                    <b>Coordonnées</b> : ${latitude}, ${longitude}</br></br>
                    <h4 class="vehicule center_dispo">🚒 Véhicules:</h4>
                    
                        <?php 
                        foreach ($vehicule_type_array as $type_vehicule => $nombre_vehicule_de_type) {
                            $nom_vehicule = str_replace("_"," ",$type_vehicule);
                            echo('
                            <div class="type vehicule '.$type_vehicule.'">
                                <div class="center_dispo">'.$nom_vehicule.' — '.$nombre_vehicule_de_type.'</div>

                                <div class="disponnibilite_vehicule '.$type_vehicule.'">
                                    <div class="dispo center_dispo '.$type_vehicule.'"><abbr title="'.$nom_vehicule.' Disponible">🟢</abbr> '.$vehicule_type_dispo_array[$type_vehicule].'</div>
                                    <div class="non_dispo center_dispo '.$type_vehicule.'"><abbr title="'.$nom_vehicule.' En Intervention">🟡</abbr> '.$vehicule_type_occupe_array[$type_vehicule].'</div>
                                </div>
                            </div>
                            ');
                        }
                        ?>
                    
                    
                    <h4 class="pompier center_dispo">👩‍🚒 Pompiers:</h4>
                    
                        <?php 
                        foreach ($pompier_grade_array as $grade => $nombre_pompier_avec_grade) {
                            $nom_grade = str_replace("_"," ",$grade);
                            echo('
                            <div class="grade pompier '.$grade.'">
                                <div class="center_dispo">'.$nom_grade.' — '.$nombre_pompier_avec_grade.'</div>

                                <div class="disponnibilite_vehicule '.$grade.'">
                                    <div class="dispo center_dispo '.$grade.'"><abbr title="'.$nom_grade.' Disponible">🟢</abbr> '.$pompier_grade_dispo_array[$grade].'</div>
                                    <div class="non_dispo center_dispo '.$grade.'"><abbr title="'.$nom_grade.' Occupé">🟡</abbr> '.$pompier_grade_occupe_array[$grade].'</div>
                                </div>
                            </div>
                            ');//afficher les grades des pompiers et leur effectif.
                        }
                        ?>
                    `)
                    markers_caserne.push(marker)//ajout du marker dans un tableau (utilisé pour cacher les markeurs de type :)

                    <?php
                }
            ?>
            
            
            


        </script>
        <script type="text/javascript">
            /**
             * ––––––––––––ADDEventListener Params menu––––––––––––
             */
             var switchs = document.getElementsByClassName("switch")
            for (let index = 0; index < switchs.length; index++) {
                // get input.
                element = switchs[index].getElementsByTagName("input")[0]
                console.log(index)
                //add listener
                element.addEventListener('change',function(){
                    afficherOuCacherLesMarker(map,markers_all[index])
                })

                
            }

        </script>
        <script type="text/javascript">
            //a enlever.
            setTimeout(function(){
            window.location.reload(1);
            }, 5000);
        </script>
    </body>
    <footer>

    </footer>
</html>
