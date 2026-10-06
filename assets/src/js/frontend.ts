import "../css/tailwind.css";
import "../css/frontend.css";
import "./accordion";

import { initPrimaryNav } from "./navbar";
import { initHeaderVideoPause } from "./header-video";
import { initPaletteFab } from "./palette-fab";

const boot = (): void => {
	initPrimaryNav();
	initHeaderVideoPause();
	initPaletteFab();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", boot);
} else {
	boot();
}


