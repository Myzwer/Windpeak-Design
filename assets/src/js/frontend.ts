import "../css/tailwind.css";
import "../css/frontend.css";
import "./accordion";

import { initPrimaryNav } from "./navbar";
import { initHeaderVideoPause } from "./header-video";
import { initPaletteFab } from "./palette-fab";
import { initSelectionBlocks } from "./selection";
import { initPricingCalculators } from "./pricing-calculator";

const boot = (): void => {
	initPrimaryNav();
	initHeaderVideoPause();
	initPaletteFab();
	initSelectionBlocks();
	initPricingCalculators();
};

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", boot);
} else {
	boot();
}


