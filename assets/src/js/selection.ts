/**
 * Interactive selection tiles for the Selection flex block.
 */

const initSelectionBlocks = (): void => {
	const blocks = document.querySelectorAll<HTMLElement>("[data-selection-block]");

	blocks.forEach((block) => {
		const tabs = Array.from(block.querySelectorAll<HTMLElement>("[data-selection-tab]"));
		const panels = Array.from(block.querySelectorAll<HTMLElement>("[data-selection-panel]"));

		if (!tabs.length || !panels.length) {
			return;
		}

		let hasActivated = true;

		const activate = (index: string): void => {
			tabs.forEach((tab) => {
				const active = tab.dataset.index === index;
				tab.classList.toggle("is-active", active);
				tab.setAttribute("aria-selected", active ? "true" : "false");
			});

			panels.forEach((panel) => {
				const active = panel.dataset.index === index;
				panel.classList.toggle("is-active", active);
				panel.classList.remove("is-entering");

				if (active) {
					panel.removeAttribute("hidden");
					if (hasActivated) {
						void panel.offsetWidth;
						panel.classList.add("is-entering");
					}
				} else {
					panel.setAttribute("hidden", "");
				}
			});

			hasActivated = true;
		};

		tabs.forEach((tab) => {
			tab.addEventListener("click", () => {
				const index = tab.dataset.index ?? "0";
				activate(index);
			});
		});
	});
};

export { initSelectionBlocks };
