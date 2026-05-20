// Alternar sidebar
document.getElementById("sidebarToggle").addEventListener("click", () => {
	document.getElementById("sidebar").classList.toggle("open");
});

// Ayudante toast
function toast(msg, type = "success") {
	const c = document.getElementById("toastContainer");
	const el = document.createElement("div");
	el.className = "toast-item " + type;
	el.innerHTML = `<i class="bi bi-${type === "success" ? "check-circle-fill" : "exclamation-circle-fill"}"></i> ${msg}`;
	c.appendChild(el);
	setTimeout(() => {
		el.style.opacity = "0";
		el.style.transition = "opacity 0.4s";
		setTimeout(() => el.remove(), 400);
	}, 3200);
}

// Descarte automático
setTimeout(() => {
	document.querySelectorAll(".flash-msg").forEach((el) => {
		el.style.opacity = "0";
		el.style.transition = "opacity 0.4s";
		setTimeout(() => el.remove(), 400);
	});
}, 5000);
