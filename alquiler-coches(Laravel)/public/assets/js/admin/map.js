const map = L.map("map", { zoomControl: false }).setView(
	[40.4168, -3.7038],
	15,
);

L.tileLayer("https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}", {
	maxZoom: 20,
	subdomains: ["mt0", "mt1", "mt2", "mt3"],
}).addTo(map);

const carIcon = L.divIcon({
	className: "custom-car-icon",
	html: '<div class="velox-dot"></div>',
	iconSize: [18, 18],
	iconAnchor: [9, 9],
});

let carMarker = L.marker([0, 0], { icon: carIcon }).addTo(map);

function fetchLiveLocation() {
	const carId = window.NeuvoApp.carId;

	fetch(`/api/location/${carId}`)
		.then((response) => response.json())
		.then((data) => {
			if (data.status === "success") {
				const lat = parseFloat(data.lat);
				const lng = parseFloat(data.lng);

				document.getElementById("cLat").innerText = lat.toFixed(6);
				document.getElementById("cLng").innerText = lng.toFixed(6);
				document.getElementById("ipUpd").innerText = data.updated_at;

				const newLatLng = new L.LatLng(lat, lng);
				carMarker.setLatLng(newLatLng);
				map.panTo(newLatLng);
			}
		})
		.catch((error) => console.error("Aún no hay señal GPS:", error));
}

setInterval(fetchLiveLocation, 3000);
fetchLiveLocation();
