mapboxgl.accessToken =
  "pk.eyJ1IjoiaGFycnliYXJjaWEiLCJhIjoiY2s3dzRvdTJnMDBqODNlbzhpcjdmaGxldiJ9.vg2wE4S7o_nryVx8IFIOuQ";
//DISPLAY MY MAP Configuration de la carte, style, url, zoom, center, nom du container
var map = new mapboxgl.Map({
  container: "map", // container id
  style: "mapbox://styles/harrybarcia/cksn9vv4o27jm18pgbw3vrt2l",

  center: [2.503, 48.842],
  zoom: 15.2,
  pitch: 40,
  bearing: 10,
  hash: true,
  minZoom: 6,
  maxZoom: 19,
});

map.on("load", function () {
  //   ajout de la carte satellite


  // ajout source carte réglementaire
  map.addSource("Zonage", {
    type: "vector",
    url: "mapbox://harrybarcia.1lyy8d8e", //tilesetID
  });

  // ajout carte réglementaire en dessous de ma couche watershadow
  map.addLayer({
    id: "Zonage", // nom donné à ma couche sur Atom
    type: "fill",
    source: "Zonage",
    "source-layer": "zonage_perreux-3t2g2s", // nom de la couche sur Mapbox
    layout: { visibility: "none" },
    paint: {
      "fill-color": {
        property: "LIBELLE",
        type: "categorical",
        stops: [
          ["UEa", "#96ceb4"],
          ["UAa", "#d6d4e0"],
          ["UEb", "#b8a9c9"],
          ["UL", "#622569"],
          ["N", "#588c7e"],
          ["UAb", "#f2e394"],
          ["UH", "#f2ae72"],
          ["URa", "#a2836e"],
          ["UM", "#85C1E9"],
          ["URb", "#034f84"],
          ["UB", "#eea29a"],
        ],
      },
      "fill-opacity": 1,
    },

    layout: { visibility: "visible" },
  },
  "waterway-shadow" 
);

  // ajout source
  map.addSource("bati", {
    type: "vector",
    url: "mapbox://harrybarcia.5p71e2pg", //tilesetID
  });

  // ajout carte bati avec couleur correspondant à la zone
  map.addLayer({
    id: "bati", // nom donné à ma couche sur Atom

    type: "fill-extrusion",

    source: "bati",

    "source-layer": "zonage_perreux_nett-5jtjei", // nom de la couche sur Mapbox
    layout: { visibility: "visible" },

    paint: {     
      "fill-extrusion-color": {
      property: "LIBELLE",
      stops: [
        ["UEa", "#588c7e"],
        ["UAa", "#a2836e"],
        ["UEb", "#622569"],
        ["UL", "#b8a9c9"],
        ["N", "#588c7e"],
        ["UAb", "#ffcc5c"],
        ["UH", "#c83349"],
        ["URa", "#d6d4e0"],
        ["UM", "#5DADE2"],
        ["URb", "#034f84"],
        ["UB", "#D84315"],
      ],
      type:"categorical",
    },
      "fill-extrusion-height": ["interpolate",
        ["exponential",10],
        ["zoom"],8,1,8.05,["get", "HAUTEUR"],
      ],
    "fill-extrusion-base": 0, // mettre 'surface_re' fait commencer la hauteur a 20 m pour 20 m² de surface par ex
    "fill-extrusion-opacity": 0.8,
    },

  });

  // ajout satellite
  map.addLayer({
    id: "satellite",
    source: { type: "raster", url: "mapbox://mapbox.satellite", tileSize: 256 },
    type: "raster",
    layout: { visibility: "none" },
 
  
  });
  // initialisation du geocoder et ajout sur la map

  let h = $(window).height();
  let w = $(window).width();

// Add the control to the map.
map.addControl(
  new MapboxGeocoder({
  accessToken: mapboxgl.accessToken,
  mapboxgl: mapboxgl
  })
  );

  var switchy = document.getElementById("preview");
  switchy.src = "sat.jpg";

  // Construct a static map url
  // https://www.mapbox.com/developers/api/static/

  // fonction qui permet l'affichage de la map au click sur l'image preview
  console.log(switchy);
  switchy.addEventListener("click", function () {
    switchy = document.getElementById("preview");
    if (switchy.className === "on") {
      switchy.setAttribute("class", "off");
      map.setLayoutProperty("satellite", "visibility", "none");
      console.log(switchy);
      document.getElementById("preview").src = "sat.jpg";
    } else {
      switchy.setAttribute("class", "on");
      map.setLayoutProperty("satellite", "visibility", "visible");
      document.getElementById("preview").src = "street.jpg";
    }
  });
});
