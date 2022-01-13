function updateall(map){
    console.log("carte update")
    /**Appel API */
    incidents = getIncidents()
    detecteurs = getDetecteurs()
    casernes = getCasernes()

    



    /**Parse tableau */
    tbody = document.getElementsByTagName("tbody")[0]
    tbody.innerHTML = ""
    incidents.forEach(incident => {
        //class Name = partie emergency.
        className = incident['type_status_incident']['nom_type_status_incident'].replace(" ","_")
        nomRue = incident['type_incident']['nom_type_incident']
        latitude = incident['latitude_incident']
        longitude = incident['longitude_incident']
        intensite = incident['intensite_incident']
        ligne = `
        <tr class='${className}'>
            <td>${nomRue}</td>
            <td>${latitude}, ${longitude}</td>
            <td class='intensite'>${intensite}</td>
        </tr>
        `
        tbody.innerHTML = tbody.innerHTML + ligne
    });
    
    /*A METTRE DANS UN FICHIER*/

    //RPZ Lyon 69 La Trick
    //45.754154744767455, 4.864503340336376


    /*GET MARKER AUTOMATICLY IN JS BY API AT WEB SERVICE.  */


    /**apres avoir recu les resultat des api ... in efface les markeurs  */
    map.eachLayer(function (layer) { 
        if (layer.options.name === 'detecteur') {
            map.removeLayer(layer) 
        }
    });

    /* –––––––––––– ADD MARKERS –––––––––––– */
    
    detecteurs.forEach(detecteur => {
        latitude = detecteur['latitude_detecteur']
        longitude = detecteur['longitude_detecteur']
        //Ajout du marker: avec une image (type_incident, et une intensité de 1 à 3.
        var marker = L.marker([latitude, longitude],{icon:iconDetecteur}).addTo(map);
        marker.options.name = "detecteur"
    });
    

     /**apres avoir recu les resultat des api ... in efface les markeurs  */
     map.eachLayer(function (layer) { 
        if (layer.options.name === 'incident') {
            map.removeLayer(layer) 
        }
    });
    

    incidents.forEach(incident => {
        var latitude =  incident['latitude_incident'] //coord lat
        var longitude = incident['longitude_incident'] //coord long
        var date_incident = Date(incident['date_incident']) //date apparition
        var intensite = incident['intensite_incident'] //1 à 100
        var type_incident = incident['type_incident']['nom_type_incident'] // prit en charge (incendie)
        var status = incident['type_status_incident']['nom_type_status_incident']
        var intensite_1_a_3 = Math.round(parseInt(intensite)/10*2)+1
        
        //Ajout du marker: avec une image (type_incident, et une intensité de 1 à 3.
        var marker = L.marker([latitude, longitude],{icon:iconNiveau(type_incident,intensite_1_a_3)}).addTo(map);
        marker.bindPopup(`
                        <h1 class="title">${type_incident} de Niveau ${intensite}</h1>
                        <h2 class="date_incident">${date_incident.toLocaleString('fr-FR', { timeZone: 'UTC' })}</h2>
                        <a href="https://www.google.fr/maps/@${latitude},${longitude},15z">
                        <b>Coordonnées</b> : lat:${latitude}, long:${longitude}
                        </a></br>
                        </br>
                        <h4 class="status incident">
                            Incident ${status}
                        </h4>`)
        
        marker.options.name = "incident"

    });

    map.eachLayer(function (layer) { 
        if (layer.options.name === 'vehicule') {
            map.removeLayer(layer) 
        }
    });

    map.eachLayer(function (layer) { 
        if (layer.options.name === 'caserne') {
            map.removeLayer(layer) 
        }
    });



    casernes.forEach(caserne => {
        caserne_id = caserne['id_caserne']
        pompierCaserne = getPompiersOfCaserne(caserne_id)

        pompierLibre = 0;
        pompierOccupe = 0;
        pompierTotal = 0;
        pompier_grade_array = [];
        pompier_grade_dispo_array = [];
        pompier_grade_occupe_array = [];   

        pompierCaserne.forEach(pompier => {
            if(pompier_grade_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')] !== undefined){
                pompier_grade_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')]++
            }else{
                pompier_grade_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')]=1
                pompier_grade_dispo_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')]=0
                pompier_grade_occuper_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')]=0
            }
            if(pompier["disponibilite_pompier"]){
                pompier_grade_dispo_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')]++
            }else{
                pompier_grade_occuper_array[pompier["type_pompier"]["nom_type_pompier"].replace(' ','_')]=0
            }

        })

        vehiculeLibre = 0;
        vehiculeOccupe = 0;
        vehiculeTotal = 0;
        vehicule_type_array = [];
        vehicule_type_dispo_array = []
        vehicule_type_occupe_array = []



        vehiculeCaserne = getVehiculesOfCaserne(caserne_id)

        vehiculeCaserne.forEach(vehicule => {
            if(vehicule_grade_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')] !== undefined){
                vehicule_grade_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')]++
            }else{
                vehicule_grade_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')]=1
                vehicule_grade_dispo_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')]=0
                vehicule_grade_occuper_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')]=0
            }
            if(vehicule["type_disponibilite_vehicule"]["nom_type_disponibilite_vehicule"] == "Disponible"){
                vehicule_grade_dispo_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')]++
            }else{
                vehicule_grade_occuper_array[vehicule["type_vehicule"]["nom_type_vehicule"].replace(' ','_')]=0
                
                //cas vehicule en intervention on l'affiche alors sur la carte.

                var latitude =  vehicule['latitude_vehicule'] //lat
                var longitude = vehicule['longitude_vehicule'] //long 
                //nom caserne
                var nom_type_vehicule = vehicule['type_vehicule']['nom_type_vehicule'] //nom
                var capacite_type_vehicule = vehicule['type_vehicule']['capacite_type_vehicule'] //capacite
                var puissance_intervention_type_vehicule = vehicule['type_vehicule']['puissance_intervention_type_vehicule'] //puissance
                var nom_caserne = vehicule['caserne']['nom_caserne'] //nom caserne
                var annee_vehicule = vehicule['annee_vehicule'] //annee 
                var nombre_intervention_maximum_vehicule = vehicule['nombre_intervention_maximum_vehicule']
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
                marker.options.name = "vehicules"
            }


        })


        var latitude = caserne['latitude_caserne']
        var longitude = caserne['longitude_caserne']
        var nom = caserne['nom_caserne']
        var data_vehicule = ``
        
        vehicule_type_array.forEach(type_vehicule => {

            nom_vehicule = type_vehicule.replace("_"," ")
            nombre_total = vehicule_type_array[type_vehicule]
            nombre_dispo = vehicule_type_dispo_array[type_vehicule]
            nombre_indispo = vehicule_type_occupe_array[type_vehicule]


            data_vehicule = data_vehicule + `
            <div class="type vehicule ${type_vehicule}">
                <div class="center_dispo">${nom_vehicule} — ${nombre_total}</div>

                <div class="disponnibilite_vehicule ${type_vehicule}">
                    <div class="dispo center_dispo ${type_vehicule}"><abbr title="${nom_vehicule} Disponible">🟢</abbr> ${nombre_dispo}</div>
                    <div class="non_dispo center_dispo ${type_vehicule}"><abbr title="'${nom_vehicule} En Intervention">🟡</abbr> ${nombre_indispo}</div>
                </div>
            </div>
            `
        });


        var data_pompier = ``
        pompier_grade_array.forEach(grade_pompier => {
            nom_pompier = grade_pompier.replace("_"," ")
            nombre_total = pompier_grade_array[type_pompier]
            nombre_dispo = pompier_grade_dispo_array[type_pompier]
            nombre_indispo = pompier_grade_occuper_array[type_pompier]


            data_pompier = data_pompier + `
            <div class="type pompier ${grade_pompier}">
                <div class="center_dispo">${nom_pompier} — ${nombre_total}</div>

                <div class="disponnibilite_pompier ${grade_pompier}">
                    <div class="dispo center_dispo ${grade_pompier}"><abbr title="${nom_pompier} Disponible">🟢</abbr> ${nombre_dispo}</div>
                    <div class="non_dispo center_dispo ${grade_pompier}"><abbr title="'${nom_pompier} En Intervention">🟡</abbr> ${nombre_indispo}</div>
                </div>
            </div>
            `
        })
        var marker = L.marker([latitude, longitude],{icon:iconCaserne}).addTo(map);
                    marker.bindPopup(`
                    <h1 class="title">Caserne de Pompier</h1>
                    <h2 class="NomCaserne">${nom}</h2></br>
                    <a href="https://www.google.fr/maps/@${latitude},${longitude},15z">
                    <b>Coordonnées</b> : ${latitude}, ${longitude}
                    </a></br>
                    <h4 class="vehicule center_dispo">🚒 Véhicules:</h4>
                    
                        ${data_vehicule}
                    
                    
                    <h4 class="pompier center_dispo">👩‍🚒 Pompiers:</h4>
                    
                        ${data_pompier}
                    `)
        markers_caserne.push(marker)//ajout du marker dans un tableau (utilisé pour cacher les markeurs de type :)
        marker.options.name = "caserne"
        
    });

}