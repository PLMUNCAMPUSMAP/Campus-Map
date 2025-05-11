<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Campus Navigation System</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        html, body, #map {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            overflow: hidden;
        }
        .container {
            position: relative;
            height: 100vh;
            width: 100vw;
        }
        #loginPopover {
    display: none;
    position: fixed;
    top: 60px;
    right: 20px;
    background: #ffffff;
    border: 1px solid #ccc;
    padding: 16px;
    z-index: 3000;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    border-radius: 8px;
    width: 240px;
    box-sizing: border-box;
}

#loginPopover form {
    display: flex;
    flex-direction: column;
}
#loginPopover p {
            margin: 8px 0;
            font-size: 14px;
            color: #333;
        }
#loginPopover input {
    margin-bottom: 10px;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-family: monospace;
    background: #f9f9f9;
}

#loginPopover button {
    padding: 10px;
    font-size: 14px;
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-family: monospace;
}

#loginPopover button:hover {
    background-color: #555;
}

#loginPopover a {
    display: block;
    text-align: center;
    margin-top: 10px;
    text-decoration: none;
    color: black;
    font-size: 14px;
}

#loginPopover a:hover {
    text-decoration: underline;
}

        .popover-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            z-index: 3001;
        }
        .popover-btn img {
            width: 40px;
            height: 40px;
            
        }

  

    .label-icon {
      font-size: 16px;
      font-weight: 800;
      color: black;
      text-shadow: 1px 1px 4px #fff;
      white-space: nowrap;
      background: none;
      padding: 2px 6px;
      border-radius: 4px;
      pointer-events: auto;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .search-panel {
      position: absolute;
      top: 15px;
      left: 10px;
      background: white;
      width: 300px;
      padding: 15px;
      z-index: 1000;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .search-panel h3 {
      margin: 0 0 5px 0;
      text-align: center;
      font-family: monospace;
      font-size: 18px;
    }

.form-row-inline {
  display: flex;
  align-items: center;
}

.search-panel .form-row-inline label {
  font-weight: 500;
  width: 60px;
  font-family: monospace;
  font-size: 16px;
}

.search-panel .form-row-inline select {
  flex: 1;
  padding: 6px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-family: monospace;
}

/* Toggle button — hidden by default */
.toggle-search-panel {
  display: none;
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 1100;
  padding: 8px 12px;
  font-size: 18px;
  background: #333;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-family: monospace;
}

/* Mobile adjustments */
@media (max-width: 768px) {
  .search-panel {
    display: none; /* Hide by default on phones */
    width: 80%;
    top: 50px;
    left: 10%;
    right: 10%;
  }

  .search-panel.open {
    display: flex; /* Show when toggled */
  }

  .toggle-search-panel {
    display: block; /* Show toggle button on mobile */
  }
}
    /* Mobile styles */


    .popup-container {
      position: absolute;
      background-color: white;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      display: none;
      z-index: 1000;
    }
    .popup-container img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      margin-bottom: 10px;
    }
    .popup-container button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 5px;
      cursor: pointer;
    }
    .popup-container button:hover {
      background-color: #45a049;
    }

    #buildingImage {
  max-width: 90%;
  max-height: 60vh;
  border-radius: 50px;
  object-fit: contain;
  margin-bottom: 20px;
  height: 300px;
  width: 500px;
  font-family: 'Courier New', Courier, monospace;
}

.leaflet-control-zoom {
  position: absolute;
  bottom: 20px;
  right: 20px;
  display: flex;
  flex-direction: column;
  z-index: 1000;
}

.leaflet-control-zoom a {
  width: 50px;
  height: 50px;
  line-height: 40px;
  font-size: 30px;
  background-color: #ffffff;
  border: 1px solid #ccc;
  color: #333;
  text-align: center;
  text-decoration: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: background-color 0.2s;
}

.leaflet-control-zoom a:hover {
  background-color: #f0f0f0;
}

/* Remove the default text inside the button */
.leaflet-control-zoom-in,
.leaflet-control-zoom-out {
  font-size: 0;
}



    </style>
</head>
<body>



<div class="container">
<div id="map"></div>
    <!-- Toggle Popover button (using image) -->
    <button type="button" class="popover-btn" id="toggleBtn">
        <img src="profile.png" alt="Menu" style="color: #333;">
    </button>

    <!-- Popover content -->
    <div id="loginPopover">
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Register</a>
    </div>
</div>

<button class="toggle-search-panel" onclick="toggleSearchPanel()" style=" color: #333;
  text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.2); background-color: #ffffff;">☰</button>
  <div class="search-panel" id="searchPanel">
    <h3>Find your route</h3>
  
    <div class="form-row-inline">
      <label for="from">From:</label>
      <select id="from">
        <option value="entrance1">Entrance 1</option>
        <option value="entrance2">Entrance 2</option>
        <option value="gym">Gym</option>
        <option value="mabini">Mabini</option>
        <option value="bonifacio">Bonifacio</option>
        <option value="delpilar">Del Pilar</option>
        <option value="rizal">Rizal</option>
        <option value="garden">Centennial Garden</option>
        <option value="parking1">Parking 1</option>
        <option value="parking2">Parking 2</option>
      </select>
    </div>
  
    <div class="form-row-inline">
      <label for="to">To:</label>
      <select id="to">
        <option value="entrance1">Entrance 1</option>
        <option value="entrance2">Entrance 2</option>
        <option value="gym">Gym</option>
        <option value="mabini">Mabini</option>
        <option value="bonifacio">Bonifacio</option>
        <option value="delpilar">Del Pilar</option>
        <option value="rizal">Rizal</option>
        <option value="garden">Centennial Garden</option>
        <option value="parking1">Parking 1</option>
        <option value="parking2">Parking 2</option>
      </select>
    </div>
  
    <button onclick="drawRoute()" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc; font-family: monospace;">Search</button>

    <div id="directions" style="margin-top: 10px; background: #f9f9f9; padding: 8px; border-radius: 3px; border: 1px solid #ccc; font-family: monospace;">
    Directions: 
  </div>
  </div>

  <div id="buildingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center; text-align: center;">
  <div style="background: white; color: black; padding: 20px; border-radius: 15px; max-width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
    <h2 id="buildingTitle" style="font-family: monospace; font-weight: 900;"></h2>
    <img id="buildingImage" src="" alt="Image" style="max-width: 100%; border-radius: 10px; margin-bottom: 10px; ">
    <br>
    <button id="viewInsideBtn" style="padding: 10px 20px; font-weight: bold; background: #333; color: white; border: none; font-family: monospace; border-radius: 5px; cursor: pointer; ">Tap to see inside</button>
    <br><br>
    <button onclick="closeModal()" style="padding: 5px 15px; background: red; color: white; border: none; font-family: monospace; border-radius: 5px; cursor: pointer;">Close</button>
  </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>

document.addEventListener("DOMContentLoaded", function() {
    var toggleBtn = document.getElementById("toggleBtn");
    var loginPopover = document.getElementById("loginPopover");

    // Toggle popover visibility when button clicked
    toggleBtn.addEventListener("click", function(event) {
        event.stopPropagation();
        if (loginPopover.style.display === "block") {
            loginPopover.style.display = "none";
        } else {
            loginPopover.style.display = "block";
        }
    });

    // Hide popover when clicking outside
    document.addEventListener("click", function(event) {
        if (!loginPopover.contains(event.target) && event.target !== toggleBtn && !toggleBtn.contains(event.target)) {
            loginPopover.style.display = "none";
        }
    });
});


const bounds = [[-22, -160], [842, 1375]];

const map = L.map('map', {
  crs: L.CRS.Simple,
  minZoom: 0,        // set minimum zoom
  maxZoom: 3,        // set maximum zoom
  maxBounds: bounds, // restrict panning outside this area
  maxBoundsViscosity: 1.0, // prevents dragging outside
  zoomControl: false // disables the top-left default zoom control
});

L.imageOverlay('campusmap.jpg', bounds).addTo(map);
map.setView([400, 500], 0);

function addLabel(latlng, text, key) {
  const marker = L.marker(latlng, {
    icon: L.divIcon({
      className: 'label-icon',
      html: text,
      iconSize: [0, 0]
    })
  }).addTo(map);

  marker.on('click', function() {
    document.getElementById('from').value = key;
    showBuildingPopup(latlng, key);
  });
}

function showBuildingPopup(latlng, buildingName) {
  const popup = document.createElement('div');
  popup.className = 'popup-container';
  
  // Add building image
  const img = document.createElement('img');
  img.src = '${buildingName.toLowerCase()}.jpg'; // Assuming the image is named after the building
  popup.appendChild(img);

  // Add building name
  const title = document.createElement('h3');
  title.textContent = buildingName;
  popup.appendChild(title);

  // Add the button to navigate
  const button = document.createElement('button');
  button.textContent = 'Tap Here';
  button.onclick = function() {
    window.location.href = '${buildingName.toLowerCase()}_building.html';  // Navigate to the building's rooms page
  };
  popup.appendChild(button);

  // Position the popup above the label and show it
  const container = document.querySelector('#map');
  container.appendChild(popup);
  const markerPos = map.latLngToContainerPoint(latlng);
  popup.style.left = '${markerPos.x - 50}px';  // Adjust based on label position
  popup.style.top = '${markerPos.y - 150}px'; // Adjust to display above the label
  popup.style.display = 'block';

  // Close popup on clicking outside
  window.addEventListener('click', function(e) {
    if (!popup.contains(e.target)) {
      popup.style.display = 'none';
    }
  });
}

addLabel([660, 705], "GYM", "Gym", false);
addLabel([700, 1010], "MABINI", "Mabini Building", false);
addLabel([390, 990], "BONIFACIO", "Bonifacio Building", false);
addLabel([155, 975], "DEL PILAR", "Del Pilar Building", false);
addLabel([270, 245], "RIZAL", "Rizal Building", false);
addLabel([385, -100], "CENTENNIAL GARDEN", "Centennial Garden", false);
addLabel([345, 497], "PARKING 1", "Parking 1", false);
addLabel([295, 1175], "PARKING 2", "Parking 2", false);
addLabel([0, 675], "ENTRANCE 1", "Entrance 1", false);
addLabel([825, 800], "ENTRANCE 2", "Entrance 2", false);

let routeLines = [];
let animatingMarkers = [];

const actualPaths = {
  gym: {
    mabini: [ [ [660, 773], [680, 940] ] ],
    entrance2: [ [ [660, 773], [670, 845], [740, 860], [780, 855], [830, 805] ] ],
    bonifacio: [ [ [660, 773], [650, 850], [540, 867], [355, 850], [350, 915] ] ],
    delpilar: [ [ [660, 773], [650, 850], [540, 867], [355, 855], [160, 837], [150, 900] ] ],
    entrance1: [ [ [660, 773], [650, 850], [540, 867], [355, 855], [15, 828], [14, 690], [-13, 685] ] ], 
    parking2: [ [ [660, 773], [650, 850], [580, 867], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
    rizal: [ [ [603, 686], [513, 680], [525, 370], [265, 353], [260, 287] ] ],
    garden: [ [ [603, 686], [513, 680], [540, 120], [390, 112], [385, 65] ] ], 
    parking1: [ [ [603, 686], [500, 680], [440, 660], [345, 495] ] ]
  },

  mabini: {
    gym: [ [ [680, 940], [660, 773] ] ],
    entrance2: [ [ [680, 940], [685, 875], [780, 855], [830, 805] ] ],
    bonifacio: [ [ [680, 940], [678, 885], [540, 870], [355, 855], [350, 915] ] ],
    delpilar: [ [ [680, 940], [678, 880], [540, 868], [355, 855], [160, 840], [150, 900] ] ],
    entrance1: [ [ [680, 940], [678, 880], [540, 868], [355, 855], [15, 828], [14, 690], [-13, 685] ] ],
    parking2: [ [ [680, 940], [670, 885], [585, 880], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
    parking1: [ [ [680, 940], [670, 865], [585, 850], [550, 815], [520, 750], [500, 700], [440, 660], [345, 495] ] ],
    rizal: [ [ [680, 940], [670, 865], [585, 850], [550, 815], [520, 750], [513, 680], [525, 370], [265, 353], [260, 287] ] ],
    garden: [ [ [680, 940], [670, 865], [585, 850], [550, 815], [520, 750], [513, 680], [540, 120], [390, 112], [385, 65] ] ]
  },

  entrance1: {
    entrance2: [ [ [-13, 685], [14, 690], [15, 828], [355, 855], [540, 867], [650, 855], [780, 855], [830, 805], ] ], 
    gym: [ [ [-13, 685], [14, 690], [15, 828], [355, 855], [540, 867], [650, 855], [660, 773] ] ], 
    mabini: [ [ [-13, 685], [14, 690], [15, 828], [355, 855], [540, 867], [678, 880], [680, 940] ] ],
    delpilar: [ [ [-13, 685], [14, 690], [15, 828], [144, 840], [150, 900] ] ],
    bonifacio: [ [ [-13, 685], [14, 690], [15, 828], [348, 855], [350, 915] ] ],
    parking2: [ [ [-13, 685], [14, 690], [12, 1080], [25, 1130], [55, 1155], [290, 1175] ] ], 
    rizal: [ [ [-13, 685], [14, 685], [35, 338], [253, 353], [260, 287] ] ],   
    parking1: [ [ [-13, 685], [14, 685], [35, 338], [280, 353], [345, 505] ] ],   
    garden: [ [ [-13, 685], [14, 685], [33, 338], [36, 165], [55, 115], [75, 100], [380, 112], [385, 65] ] ]
  },

  entrance2: {
    entrance1: [ [ [830, 805], [780, 855], [650, 855], [540, 867], [355, 855], [15, 828], [14, 690], [-13, 683] ] ], 
    bonifacio: [ [ [830, 805], [780, 855], [650, 855], [540, 867], [355, 855], [350, 915] ] ], 
    delpilar: [ [ [830, 805], [780, 855], [650, 855], [540, 867], [355, 855], [160, 840], [150, 900] ] ], 
    mabini: [ [ [830, 805], [780, 855], [685, 875], [680, 940] ] ],
    parking1: [ [ [830, 805], [780, 855], [585, 850], [550, 815], [520, 750], [500, 700], [440, 660], [345, 495] ] ],
    parking2: [ [ [830, 805], [780, 855], [650, 850], [580, 867], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
    gym: [ [ [830, 805], [780, 855], [740, 860], [670, 845], [660, 773] ] ],
    rizal: [ [ [830, 805], [780, 855], [585, 850], [550, 815], [520, 750], [513, 680], [525, 370], [265, 353], [260, 287] ] ],
    garden: [[ [830, 805], [780, 855], [585, 850], [550, 815], [520, 750], [513, 680], [540, 120], [390, 112], [385, 65] ] ] 
  },
  
   bonifacio: {
    entrance2: [ [ [350, 915],  [355, 855], [540, 867], [650, 855], [780, 855], [830, 805] ] ], 
    gym: [ [ [350, 915], [355, 850], [540, 867], [650, 850], [660, 773] ] ],
    mabini: [ [ [350, 915], [355, 855], [540, 870], [678, 885], [680, 940], ] ],
    delpilar: [ [ [350, 915], [350, 855], [160, 840], [150, 900] ] ],
    entrance1: [ [ [350, 915], [350, 915], [348, 855], [15, 828], [14, 690], [-13, 685] ] ],
    parking2: [ [ [350, 915], [355, 855], [540, 870], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
    parking1: [ [ [350, 915], [355, 855], [340, 510] ] ],  
    garden: [ [ [350, 915], [355, 855], [340, 510], [350, 355], [525, 365], [540, 120], [390, 112], [385, 65] ] ], 
    rizal: [ [ [350, 915], [355, 855], [340, 510], [340, 355], [265, 353], [260, 287] ] ]
  },

  parking1: {
    mabini: [ [ [345, 495], [440, 660], [500, 700], [520, 750], [550, 815], [585, 850], [670, 865], [680, 940] ] ],
    gym: [ [ [345, 495], [440, 660], [500, 680], [603, 686] ] ],
    entrance2: [ [ [345, 495], [440, 660], [520, 750], [550, 815], [585, 850], [780, 855], [830, 805] ] ],
    bonifacio: [ [ [340, 510], [355, 855], [350, 915] ] ],  
    garden: [ [ [340, 510], [350, 355], [525, 365], [540, 120], [390, 112], [385, 65] ] ], 
    rizal: [ [ [340, 510], [340, 355], [265, 353], [260, 287] ] ],
    entrance1: [ [ [345, 505], [280, 353], [35, 338], [14, 685], [-13, 685]] ],   
    delpilar: [ [ [340, 510], [355, 855], [160, 840], [150, 900] ] ],
    parking2: [ [ [345, 495], [440, 660], [500, 700], [520, 750], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
  },

  delpilar: {
    entrance2: [ [ [150, 900], [160, 840], [540, 867], [650, 855], [780, 855], [830, 805] ] ], 
    gym: [ [ [150, 900], [160, 840], [540, 867], [650, 850], [660, 773] ] ],
    mabini: [ [ [150, 900], [160, 840], [540, 870], [678, 885], [680, 940], ] ],
    bonifacio: [ [ [150, 900], [160, 840], [350, 855], [350, 915] ] ],
    parking2: [ [  [150, 900], [144, 840], [15, 828], [12, 1080], [25, 1130], [55, 1155], [290, 1175] ] ],
    parking1: [ [ [150, 900], [160, 840], [350, 855], [355, 855], [340, 510] ] ],
    entrance1: [ [ [150, 900], [144, 840], [15, 828], [14, 690], [-13, 685] ] ],
    rizal: [ [ [150, 900], [160, 840], [200, 840], [225, 350], [253, 353], [260, 287] ] ],
    garden: [ [ [150, 900], [160, 840], [200, 840], [225, 350], [525, 365], [540, 120], [390, 112], [385, 65] ] ],
  },

  rizal: {
    gym: [ [ [260, 287], [265, 353], [525, 370], [513, 680], [603, 686] ] ],
    garden: [ [ [260, 287], [265, 353], [525, 370], [525, 365], [540, 120], [390, 112], [385, 65] ] ],
    parking1: [ [ [260, 287], [265, 353], [340, 355], [340, 510] ] ],
    entrance1: [ [ [260, 287], [253, 353], [35, 338], [14, 685], [-13, 685] ] ],   
    mabini: [ [ [260, 287], [265, 353], [525, 370], [513, 680], [520, 750], [550, 815], [585, 850], [670, 865], [680, 940] ] ],
    entrance2: [ [ [260, 287], [265, 353], [525, 370], [513, 680], [520, 750], [550, 815], [585, 850], [670, 865], [780, 855], [830, 805] ] ],
    bonifacio: [ [ [260, 287], [265, 353], [340, 355], [340, 510], [355, 855], [350, 915] ] ],
    delpilar: [ [ [260, 287], [253, 353], [225, 350], [200, 840], [160, 840], [150, 900] ] ],
    parking2: [ [ [260, 287], [265, 353], [525, 370], [513, 680], [520, 750], [550, 815], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
  },

  garden: {
    entrance1: [ [ [385, 65], [380, 112], [75, 100], [55, 115], [36, 165], [33, 338], [14, 685], [-13, 685] ] ],
    rizal: [ [ [385, 65], [390, 112], [540, 120], [525, 365], [525, 370], [265, 353], [260, 287] ] ],
    parking1: [ [ [385, 65], [390, 112], [540, 120], [525, 365], [350, 355], [340, 510] ] ], 
    bonifacio: [ [ [385, 65], [390, 112], [540, 120], [525, 365], [350, 355], [340, 510], [355, 855], [350, 915] ] ], 
    delpilar: [ [ [385, 65], [390, 112], [540, 120], [525, 365], [225, 350], [200, 840], [160, 840], [150, 900] ] ],
    entrance2: [ [ [385, 65], [390, 112], [540, 120], [513, 680], [520, 750], [520, 750], [550, 815], [585, 850], [780, 855], [830, 805] ] ],
    mabini: [ [ [385, 65], [390, 112], [540, 120], [513, 680], [520, 750], [550, 815], [585, 850], [670, 865], [680, 940] ] ],
    parking2: [ [ [385, 65], [390, 112], [540, 120], [513, 680], [520, 750], [550, 815], [563, 910], [560, 1140], [540, 1175], [505, 1195], [290, 1175] ] ],
    gym: [ [ [385, 65], [390, 112], [540, 120], [513, 680], [603, 686] ] ], 
  },

  parking2: {
    rizal: [ [ [290, 1175], [505, 1195], [540, 1175],[560, 1140], [563, 910], [550, 815], [520, 750], [513, 680], [525, 370], [265, 353], [260, 287] ] ],
    delpilar: [ [ [290, 1175], [55, 1155], [25, 1130], [12, 1080], [15, 828], [144, 840], [150, 900] ] ],
    parking1: [ [ [290, 1175], [505, 1195], [540, 1175],[560, 1140], [563, 910], [520, 750], [500, 700], [440, 660], [345, 495] ] ],
    bonifacio: [ [ [290, 1175], [505, 1195], [540, 1175],[560, 1140], [563, 910], [540, 870], [355, 855], [350, 915] ] ],
    entrance1: [ [ [290, 1175], [55, 1155], [25, 1130],  [12, 1080], [14, 690], [-13, 685] ] ], 
    mabini: [ [ [290, 1175], [505, 1195], [540, 1175], [560, 1140], [563, 910], [585, 880], [670, 885], [680, 940] ] ],
    gym: [ [ [290, 1175], [505, 1195], [540, 1175], [560, 1140], [563, 910], [580, 867], [650, 850], [660, 773] ] ],
    entrance2: [ [ [290, 1175], [505, 1195], [540, 1175], [560, 1140], [563, 910], [580, 867], [650, 850], [780, 855], [830, 805] ] ],
    garden: [ [ [290, 1175], [505, 1195], [540, 1175],[560, 1140], [563, 910], [550, 815], [520, 750], [513, 680], [540, 120], [390, 112], [385, 65] ] ],
  },

};

function drawRoute() {
  const from = document.getElementById('from').value;
  const to = document.getElementById('to').value;

  if (from === to) {
    alert('Please select two different locations.');
    return;
  }

  // Remove old lines and markers
  routeLines.forEach(line => map.removeLayer(line));
  animatingMarkers.forEach(marker => map.removeLayer(marker));
  routeLines = [];
  animatingMarkers = [];

  const paths = actualPaths[from]?.[to];
  if (!paths) {
    alert('No route data available.');
    return;
  }

  const colors = ['blue', 'red', 'green', 'purple'];
  let totalDistance = 0;
  let stepByStep = "";

  paths.forEach((path, index) => {
    const color = colors[index % colors.length];

    const line = L.polyline(path, {
      color: color,
      weight: 4
    }).addTo(map);

    routeLines.push(line);

    // Add moving marker
    const marker = L.circleMarker(path[0], {
      radius: 7,
      color: color,
      fillColor: color,
      fillOpacity: 1
    }).addTo(map);

    animatingMarkers.push(marker);
    animateMarker(marker, path, 1000 + (index * 300));

    // Calculate total distance for this path
    for (let i = 0; i < path.length - 1; i++) {
      const start = path[i];
      const end = path[i + 1];
      totalDistance += Math.hypot(end[0] - start[0], end[1] - start[1]);

      // Collect step-by-step directions (as coordinate points for now)
      stepByStep += `→ From [${start[0]}, ${start[1]}] to [${end[0]}, ${end[1]}]<br>`;
    }
  });
}

function animateMarker(marker, path, delay = 0) {
  let i = 0;
  const speed = 12;
  const walkingSpeed = 0.90;

  function moveToNextPoint() {
    if (i >= path.length - 1) {
      document.getElementById('directions').innerText = 'You’ve reached your destination.';
      return;
    }

    const start = path[i];
    const end = path[i + 1];
    const distance = Math.hypot(end[0] - start[0], end[1] - start[1]);
    const steps = Math.max(Math.round(distance / walkingSpeed), 1);
    let step = 0;

    if (i > 0) {
      const prev = path[i - 1];
      const direction = getDirection(prev, start, end);
      document.getElementById('directions').innerText = `Directions: ${direction}`;
    }

    const interval = setInterval(() => {
      if (step >= steps) {
        clearInterval(interval);
        i++;
        moveToNextPoint();
      } else {
        const lat = start[0] + (end[0] - start[0]) * (step / steps);
        const lng = start[1] + (end[1] - start[1]) * (step / steps);
        marker.setLatLng([lat, lng]);
        step++;
      }
    }, speed);
  }

  setTimeout(() => {
    moveToNextPoint();
  }, delay);
}

function getDirection(p1, p2, p3) {
  const angle1 = Math.atan2(p2[1] - p1[1], p2[0] - p1[0]);
  const angle2 = Math.atan2(p3[1] - p2[1], p3[0] - p2[0]);
  const diff = ((angle2 - angle1) * 180 / Math.PI + 360) % 360;

  if (diff < 30 || diff > 330) {
    return 'Go straight';
  } else if (diff >= 30 && diff <= 150) {
    return 'Turn right';
  } else if (diff >= 210 && diff <= 330) {
    return 'Turn left';
  } else {
    return 'Slight turn';
  }
}

function toggleSearchPanel() {
  if (window.innerWidth <= 768) {
    const panel = document.getElementById("searchPanel");
    panel.classList.toggle("open");
  }
}

function addLabel(latlng, text, key, hasInsideLink = true) {
  const marker = L.marker(latlng, {
    icon: L.divIcon({
      className: 'label-icon',
      html: text,
      iconSize: [250, 40],
      iconAnchor: [50, 20]
    })
  }).addTo(map);

  marker.on('click', function() {
    document.getElementById('from').value = key;
    showBuildingPopup(latlng, key, hasInsideLink);
  });
}

function showBuildingPopup(latlng, buildingName, hasInsideLink) {
  const modal = document.getElementById("buildingModal");
  document.getElementById("buildingTitle").textContent = buildingName;
  document.getElementById("buildingImage").src = `${buildingName.toLowerCase()}.jpg`;

  const viewBtn = document.getElementById("viewInsideBtn");
  if (hasInsideLink) {
    viewBtn.style.display = "inline-block";
    viewBtn.onclick = function () {
      window.location.href = `${buildingName.toLowerCase()}.html`;
    };
  } else {
    viewBtn.style.display = "none";
  }

  modal.style.display = "flex";
}

function closeModal() {
  document.getElementById("buildingModal").style.display = "none";
}

L.control.zoom({
  position: 'bottomright' // moves it to bottom-right
}).addTo(map);


</script>

</body>
</html>
