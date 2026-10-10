/**
 * Advisor Micros dynamic pricing calculator.
 * Pricing values come from ACF via data-pricing-config (single source of truth).
 */

type PricingConfig = {
	baseSite: number;
	monthlyService: number;
	termMonths: number;
	addons: {
		vacationCrm: number;
		whiteGloveCopy: number;
		graphicDesign: number;
	};
};

const formatCurrency = (value: number): string => {
	const rounded = Math.round(value * 100) / 100;
	if (Number.isInteger(rounded)) {
		return `$${rounded}`;
	}
	return `$${rounded.toFixed(2)}`;
};

const readConfig = (root: HTMLElement): PricingConfig | null => {
	const raw = root.dataset.pricingConfig;
	if (!raw) {
		return null;
	}

	try {
		return JSON.parse(raw) as PricingConfig;
	} catch {
		return null;
	}
};

const calculate = (
	config: PricingConfig,
	payment: "upfront" | "spread",
	addons: { vacationCrm: boolean; whiteGloveCopy: boolean; graphicDesign: boolean }
): { upfront: number; monthly: number; then: number } => {
	let projectTotal = config.baseSite;
	if (addons.vacationCrm) {
		projectTotal += config.addons.vacationCrm;
	}
	if (addons.whiteGloveCopy) {
		projectTotal += config.addons.whiteGloveCopy;
	}
	if (addons.graphicDesign) {
		projectTotal += config.addons.graphicDesign;
	}

	if (payment === "upfront") {
		return {
			upfront: projectTotal,
			monthly: config.monthlyService,
			then: config.monthlyService,
		};
	}

	return {
		upfront: 0,
		monthly: config.monthlyService + projectTotal / config.termMonths,
		then: config.monthlyService,
	};
};

const initPricingCalculators = (): void => {
	const roots = document.querySelectorAll<HTMLElement>("[data-pricing-calculator]");

	roots.forEach((root) => {
		const config = readConfig(root);
		if (!config) {
			return;
		}

		const upfrontEl = root.querySelector<HTMLElement>("[data-pricing-upfront]");
		const monthlyEl = root.querySelector<HTMLElement>("[data-pricing-monthly]");
		const thenEl = root.querySelector<HTMLElement>("[data-pricing-then]");
		const monthlyLabel = root.querySelector<HTMLElement>("[data-pricing-monthly-label]");
		const thenWrap = root.querySelector<HTMLElement>("[data-pricing-then-wrap]");

		const update = (): void => {
			const payInput = root.querySelector<HTMLInputElement>("[data-pricing-pay]:checked");
			const payment = payInput?.value === "spread" ? "spread" : "upfront";

			const addonState = {
				vacationCrm:
					root.querySelector<HTMLInputElement>('[data-pricing-addon="vacationCrm"]:checked')?.value ===
					"1",
				whiteGloveCopy:
					root.querySelector<HTMLInputElement>('[data-pricing-addon="whiteGloveCopy"]:checked')
						?.value === "1",
				graphicDesign:
					root.querySelector<HTMLInputElement>('[data-pricing-addon="graphicDesign"]:checked')
						?.value === "1",
			};

			const result = calculate(config, payment, addonState);

			if (upfrontEl) {
				upfrontEl.textContent = formatCurrency(result.upfront);
			}
			if (monthlyEl) {
				monthlyEl.textContent =
					payment === "spread"
						? `${formatCurrency(result.monthly)}/mo`
						: formatCurrency(result.monthly);
			}
			if (thenEl) {
				thenEl.textContent = `${formatCurrency(result.then)}/mo`;
			}
			if (monthlyLabel) {
				monthlyLabel.textContent =
					payment === "spread" ? `First ${config.termMonths} Months` : "Monthly";
			}
			if (thenWrap) {
				thenWrap.classList.toggle("hidden", payment !== "spread");
			}
		};

		root.addEventListener("change", (event) => {
			const target = event.target as HTMLElement | null;
			if (target?.matches("[data-pricing-pay], [data-pricing-addon]")) {
				update();
			}
		});

		update();
	});
};

export { initPricingCalculators };
