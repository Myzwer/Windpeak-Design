const STORAGE_KEY = "prelaunch-palette";
const SITE_PALETTES = new Set(["spring", "summer", "autumn", "winter"]);

/**
 * Read the palette chosen earlier in this tab.
 * sessionStorage lasts for the tab, including reloads and page changes.
 *
 * @return {string|null} Saved slug, or null when nothing valid is stored.
 */
function readStoredPalette(): string | null {
	try {
		return sessionStorage.getItem(STORAGE_KEY);
	} catch (error) {
		return null;
	}
}

/**
 * Remember the palette for the rest of this tab.
 *
 * @param {string} palette Palette slug to store.
 */
function writeStoredPalette(palette: string): void {
	try {
		sessionStorage.setItem(STORAGE_KEY, palette);
	} catch (error) {
		// Storage can be blocked. The choice still applies on this page.
	}
}

/**
 * Point the document at a seasonal palette, or clear it for Base Windpeak.
 * Seasonal tokens live on :root[data-palette="…"] in palettes.css.
 *
 * @param {string} palette Palette slug from the FAB.
 */
function applySitePalette(palette: string): void {
	const root = document.documentElement;

	if (SITE_PALETTES.has(palette)) {
		root.dataset.palette = palette;
	} else {
		delete root.dataset.palette;
	}

	swapSeasonalMedia(palette);
	persistPaletteCookie(palette);
}

/**
 * Keep PHP in step with the tab choice so the next page paints the right photo.
 *
 * @param {string} palette Palette slug from the FAB.
 */
function persistPaletteCookie(palette: string): void {
	document.cookie = `${STORAGE_KEY}=${encodeURIComponent(palette)}; Path=/; SameSite=Lax`;
}

/**
 * Point seasonal photos at the active palette without fetching the others.
 * Missing season URLs fall back to the Windpeak photo.
 *
 * @param {string} palette Palette slug from the FAB.
 */
function swapSeasonalMedia(palette: string): void {
	const nodes = document.querySelectorAll<HTMLElement>("[data-seasonal-media]");

	nodes.forEach((node) => {
		const url = node.getAttribute(`data-palette-src-${palette}`)
			|| node.getAttribute("data-palette-src-windpeak");

		if (!url) {
			return;
		}

		if (node instanceof HTMLImageElement) {
			node.removeAttribute("srcset");
			node.removeAttribute("sizes");

			if (node.getAttribute("src") !== url) {
				node.src = url;
			}

			return;
		}

		node.style.setProperty("--video-poster-image", `url("${url.replace(/"/g, "%22")}")`);
	});
}

const SLIDE_MS = 360;

/**
 * Bind the corner button and the choices above it.
 * Choosing one collapses the menu and makes that icon the corner button.
 * The choice is kept for this tab, then forgotten when the session ends.
 *
 * @param {ParentNode} root Scope for the palette control. Defaults to the document.
 */
export function initPaletteFab(root: ParentNode = document): void {
	const stack = root.querySelector<HTMLElement>("[data-palette-fab]");

	if (!stack) {
		return;
	}

	const toggle = stack.querySelector<HTMLButtonElement>("[data-palette-fab-toggle]");
	const seasons = stack.querySelector<HTMLElement>("[data-palette-fab-seasons]");

	if (!toggle || !seasons) {
		return;
	}

	let closeTimer = 0;

	const motionOk = (): boolean => !window.matchMedia("(prefers-reduced-motion: reduce)").matches;

	const applyChoice = (palette: string, syncMenu: boolean): void => {
		const option = seasons.querySelector<HTMLButtonElement>(`[data-palette="${palette}"]`);

		if (!option) {
			return;
		}

		stack.dataset.active = palette;
		applySitePalette(palette);

		if (syncMenu) {
			stack.dataset.menu = palette;
		}

		toggle.setAttribute("aria-label", option.getAttribute("aria-label") || palette);
	};

	const pop = (): void => {
		if (!motionOk()) {
			return;
		}

		toggle.classList.remove("is-pop");
		void toggle.offsetWidth;
		toggle.classList.add("is-pop");
		toggle.addEventListener(
			"animationend",
			() => {
				toggle.classList.remove("is-pop");
			},
			{once: true}
		);
	};

	const finishClose = (): void => {
		if (stack.classList.contains("is-open")) {
			return;
		}

		seasons.hidden = true;

		if (stack.dataset.active) {
			stack.dataset.menu = stack.dataset.active;
		}
	};

	const openMenu = (): void => {
		window.clearTimeout(closeTimer);

		if (stack.dataset.active) {
			stack.dataset.menu = stack.dataset.active;
		}

		seasons.hidden = false;
		toggle.setAttribute("aria-expanded", "true");

		if (!motionOk()) {
			stack.classList.add("is-open");
			return;
		}

		stack.classList.remove("is-open");
		seasons.classList.add("is-prep");
		void seasons.offsetWidth;
		seasons.classList.remove("is-prep");
		stack.classList.add("is-open");
	};

	const closeMenu = (): void => {
		toggle.setAttribute("aria-expanded", "false");
		if (!motionOk()) {
			stack.classList.remove("is-open");
			finishClose();
			return;
		}

		window.clearTimeout(closeTimer);
		stack.classList.remove("is-open");
		closeTimer = window.setTimeout(finishClose, SLIDE_MS);
	};

	const notice = root.querySelector<HTMLElement>("[data-palette-notice]");
	const noticeIcons: Record<string, string> = {
		spring: "fa-flower",
		summer: "fa-sun",
		autumn: "fa-leaf",
		winter: "fa-snowflake",
	};
	let noticeTimer = 0;
	let detachNotice: (() => void) | null = null;

	const clearNoticeListeners = (): void => {
		window.clearTimeout(noticeTimer);

		if (detachNotice) {
			detachNotice();
			detachNotice = null;
		}
	};

	const hideNotice = (): void => {
		if (!notice || notice.hidden) {
			clearNoticeListeners();
			return;
		}

		clearNoticeListeners();
		notice.classList.remove("is-on");

		const finish = (): void => {
			if (notice.classList.contains("is-on")) {
				return;
			}

			notice.hidden = true;
		};

		if (!motionOk()) {
			finish();
			return;
		}

		notice.addEventListener("transitionend", finish, {once: true});
	};

	const showNotice = (palette: string): void => {
		if (!notice || !SITE_PALETTES.has(palette)) {
			return;
		}

		const text = notice.getAttribute(`data-notice-${palette}`);
		const icon = notice.querySelector<HTMLElement>("[data-palette-notice-icon]");
		const label = notice.querySelector<HTMLElement>("[data-palette-notice-text]");

		if (!text || !icon || !label) {
			return;
		}

		clearNoticeListeners();
		icon.className = `fa-solid ${noticeIcons[palette] || "fa-flower"}`;
		label.textContent = text;
		notice.hidden = false;
		void notice.offsetWidth;
		notice.classList.add("is-on");

		const onInteract = (): void => {
			hideNotice();
		};

		const attachTimer = window.setTimeout(() => {
			window.addEventListener("pointerdown", onInteract);
			window.addEventListener("keydown", onInteract);
			window.addEventListener("scroll", onInteract, true);
			window.addEventListener("wheel", onInteract, {passive: true});
		}, 50);

		detachNotice = (): void => {
			window.clearTimeout(attachTimer);
			window.removeEventListener("pointerdown", onInteract);
			window.removeEventListener("keydown", onInteract);
			window.removeEventListener("scroll", onInteract, true);
			window.removeEventListener("wheel", onInteract);
		};

		noticeTimer = window.setTimeout(hideNotice, 2000);
	};

	const stored = readStoredPalette();

	if (stored) {
		applyChoice(stored, true);
	}

	toggle.addEventListener("click", () => {
		const open = toggle.getAttribute("aria-expanded") === "true";

		if (open) {
			closeMenu();
			return;
		}

		openMenu();
	});

	seasons.addEventListener("click", (event) => {
		const option = (event.target as Element | null)?.closest<HTMLButtonElement>("[data-palette]");
		const palette = option?.dataset.palette;

		if (!option || !palette) {
			return;
		}

		applyChoice(palette, false);
		writeStoredPalette(palette);
		pop();
		closeMenu();
		showNotice(palette);
		toggle.focus();
	});
}
