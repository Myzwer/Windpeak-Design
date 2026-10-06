/**
 * Pause/play control for decorative looping header videos.
 *
 * The video source is not in the HTML `src` so first paint (especially mobile)
 * is the poster only. Playback starts after idle unless the user already paused
 * or Save-Data is on.
 *
 * The button lives in the bottom-right of the video frame (WCAG 2.2.2).
 * Reduced-motion users never see the video or the control (CSS).
 */

const STORAGE_KEY = "prelaunch-header-video-paused";
const PAUSE_LABEL = "Pause background video";
const PLAY_LABEL = "Play background video";
const PAUSE_ICON = "fa-pause";
const PLAY_ICON = "fa-play";

/**
 * Attach the mp4 when we actually intend to play.
 *
 * @param {HTMLVideoElement} video Header video element with a data-src source.
 */
function attachHeaderVideoSource(video: HTMLVideoElement): void {
	const source = video.querySelector("source");
	const pending = source?.getAttribute("data-src");

	if (!source || !pending || source.getAttribute("src")) {
		return;
	}

	source.setAttribute("src", pending);
	video.load();
}

/**
 * Update the toggle's accessible name and Font Awesome icon.
 *
 * @param {HTMLButtonElement} toggle Pause/play control button.
 * @param {boolean} paused Whether the video is currently paused.
 */
function syncToggleUi(toggle: HTMLButtonElement, paused: boolean): void {
	toggle.setAttribute("aria-label", paused ? PLAY_LABEL : PAUSE_LABEL);

	const icon = toggle.querySelector<HTMLElement>("[data-header-video-icon]");
	if (!icon) {
		return;
	}

	icon.classList.remove(PAUSE_ICON, PLAY_ICON);
	icon.classList.add(paused ? PLAY_ICON : PAUSE_ICON);
}

/**
 * Bind pause/play for decorative looping header videos.
 */
export function initHeaderVideoPause(): void {
	const toggle = document.querySelector<HTMLButtonElement>("[data-header-video-toggle]");
	const videos = Array.from(document.querySelectorAll<HTMLVideoElement>("video.header-video"));

	if (!toggle || videos.length === 0) {
		toggle?.remove();
		return;
	}

	if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
		videos.forEach((video) => video.pause());
		toggle.remove();
		return;
	}

	const connection = (navigator as Navigator & { connection?: { saveData?: boolean } }).connection;
	const saveData = Boolean(connection?.saveData);

	const setPaused = (paused: boolean): void => {
		videos.forEach((video) => {
			if (paused) {
				video.pause();
			} else {
				attachHeaderVideoSource(video);
				void video.play();
			}
		});

		syncToggleUi(toggle, paused);

		try {
			window.localStorage.setItem(STORAGE_KEY, paused ? "1" : "0");
		} catch {
			// Ignore private-mode / blocked storage.
		}
	};

	let stored = false;
	try {
		stored = window.localStorage.getItem(STORAGE_KEY) === "1";
	} catch {
		stored = false;
	}

	syncToggleUi(toggle, stored || saveData);

	const startWhenIdle = (): void => {
		if (stored || saveData) {
			return;
		}

		setPaused(false);
	};

	const idleWindow = window as Window & {
		requestIdleCallback?: (cb: () => void, opts?: { timeout: number }) => number;
	};

	if (typeof idleWindow.requestIdleCallback === "function") {
		idleWindow.requestIdleCallback(startWhenIdle, { timeout: 2500 });
	} else {
		window.setTimeout(startWhenIdle, 1);
	}

	toggle.addEventListener("click", () => {
		const isPaused = videos.every((video) => video.paused || !video.querySelector("source")?.getAttribute("src"));
		setPaused(!isPaused);
	});
}
