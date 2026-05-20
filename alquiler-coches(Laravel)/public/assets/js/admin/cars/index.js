// Sidebar
document.getElementById("sidebarToggle").addEventListener("click", () => {
	document.getElementById("sidebar").classList.toggle("open");
});

// Modal
function openDeleteModal(action, name) {
	document.getElementById("deleteForm").action = action;
	document.getElementById("deleteModalMsg").textContent =
		`Are you sure you want to delete "${name}"? This action will also remove it from WordPress.`;
	document.getElementById("deleteModal").classList.add("open");
}

function closeModal(id) {
	document.getElementById(id).classList.remove("open");
}

document.addEventListener("keydown", (e) => {
	if (e.key === "Escape")
		document
			.querySelectorAll(".modal-overlay.open")
			.forEach((m) => m.classList.remove("open"));
});

// Búsqueda en vivo + filtro
const searchInput = document.getElementById("vehicleSearch");
const fuelFilter = document.getElementById("vehicleFilterFuel");
const statusFilter = document.getElementById("vehicleFilterStatus");

function filterTable() {
	const q = searchInput.value.toLowerCase();
	const fuel = fuelFilter.value.toLowerCase();
	const status = statusFilter.value;
	document
		.querySelectorAll("#vehiclesTableBody tr[data-search]")
		.forEach((row) => {
			const nameMatch = row.dataset.search.includes(q);
			const fuelMatch =
				!fuel || (row.dataset.fuel || "").toLowerCase() === fuel;
			const statusMatch = !status || row.dataset.status === status;
			row.style.display = nameMatch && fuelMatch && statusMatch ? "" : "none";
		});
}
searchInput.addEventListener("input", filterTable);
fuelFilter.addEventListener("change", filterTable);
statusFilter.addEventListener("change", filterTable);

// Descarte automático
setTimeout(() => {
	document.querySelectorAll(".flash-msg").forEach((el) => {
		el.style.opacity = "0";
		el.style.transition = "opacity 0.4s";
		setTimeout(() => el.remove(), 400);
	});
}, 5000);
