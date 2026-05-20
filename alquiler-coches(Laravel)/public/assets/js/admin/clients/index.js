// Sidebar
document.getElementById("sidebarToggle").addEventListener("click", () => {
	document.getElementById("sidebar").classList.toggle("open");
});

// Modal
function openDeleteModal(action, name) {
	document.getElementById("deleteForm").action = action;
	document.getElementById("deleteModalMsg").textContent =
		`Are you sure you want to delete "${name}"? This action will permanently remove the user from WordPress.`;
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

// Búsqueda en vivo
const searchInput = document.getElementById("clientSearch");

function filterTable() {
	const q = searchInput.value.toLowerCase();
	document
		.querySelectorAll("#clientsTableBody tr[data-search]")
		.forEach((row) => {
			const match = row.dataset.search.includes(q);
			row.style.display = match ? "" : "none";
		});
}
searchInput.addEventListener("input", filterTable);

// Descarte automático
setTimeout(() => {
	document.querySelectorAll(".flash-msg").forEach((el) => {
		el.style.opacity = "0";
		el.style.transition = "opacity 0.4s";
		setTimeout(() => el.remove(), 400);
	});
}, 5000);
