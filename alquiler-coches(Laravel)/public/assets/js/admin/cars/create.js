// Sidebar toggle
document.getElementById("sidebarToggle").addEventListener("click", () => {
	document.getElementById("sidebar").classList.toggle("open");
});

// SERVICE TAGS 
let serviceTags = window.__oldFeatures
	? Object.values(window.__oldFeatures)
	: [];

function renderServiceTags() {
	const list = document.getElementById("serviceTagList");
	const hiddenDiv = document.getElementById("featuresHiddenInputs");
	list.innerHTML = "";
	hiddenDiv.innerHTML = "";
	serviceTags.forEach((tag, i) => {
		const el = document.createElement("div");
		el.className = "tag-item";
		el.innerHTML = `<span>${tag}</span><button class="tag-remove" type="button" data-idx="${i}"><i class="bi bi-x"></i></button>`;
		list.appendChild(el);
		const inp = document.createElement("input");
		inp.type = "hidden";
		inp.name = "features[]";
		inp.value = tag;
		hiddenDiv.appendChild(inp);
	});
	list.querySelectorAll(".tag-remove").forEach((btn) => {
		btn.addEventListener("click", () => {
			serviceTags.splice(parseInt(btn.dataset.idx), 1);
			renderServiceTags();
		});
	});
}

function addServiceTag() {
	const input = document.getElementById("serviceTagInput");
	const val = input.value.trim();
	if (!val) return;
	serviceTags.push(val);
	input.value = "";
	renderServiceTags();
}

document
	.getElementById("btnAddService")
	.addEventListener("click", addServiceTag);
document.getElementById("serviceTagInput").addEventListener("keydown", (e) => {
	if (e.key === "Enter") {
		e.preventDefault();
		addServiceTag();
	}
});
renderServiceTags();

// IMAGE PREVIEW 
document
	.getElementById("vFeaturedImage")
	.addEventListener("change", function (e) {
		const file = e.target.files[0];
		if (!file) return;
		const reader = new FileReader();
		reader.onload = (ev) => {
			const preview = document.getElementById("featuredPreview");
			preview.src = ev.target.result;
			preview.classList.remove("hidden");
			document.getElementById("featuredPlaceholder").style.display = "none";
			document.getElementById("featuredClear").classList.remove("hidden");
		};
		reader.readAsDataURL(file);
	});

document.getElementById("featuredClear").addEventListener("click", (e) => {
	e.stopPropagation();
	document.getElementById("featuredPreview").classList.add("hidden");
	document.getElementById("featuredPlaceholder").style.display = "";
	document.getElementById("featuredClear").classList.add("hidden");
	document.getElementById("vFeaturedImage").value = "";
});

// GALLERY PREVIEW 
document
	.getElementById("vGalleryImages")
	.addEventListener("change", function (e) {
		const files = Array.from(e.target.files);
		const grid = document.getElementById("galleryPreviewGrid");
		const placeholder = document.getElementById("galleryPlaceholder");
		grid.innerHTML = "";
		if (!files.length) {
			placeholder.style.display = "";
			return;
		}
		placeholder.style.display = "none";
		files.forEach((file) => {
			const reader = new FileReader();
			reader.onload = (ev) => {
				const img = document.createElement("img");
				img.src = ev.target.result;
				img.className = "gallery-thumb";
				grid.appendChild(img);
			};
			reader.readAsDataURL(file);
		});
	});
