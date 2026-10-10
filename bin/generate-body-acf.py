#!/usr/bin/env python3
"""Generate body_sections ACF JSON from the approved Windpeak flex list."""

import json
import time
from pathlib import Path


def wysiwyg(
	key,
	label,
	name,
	instructions="You can add headers, text and buttons using the tools below. For SEO reasons, do not use an H1.",
	width="",
	media_upload=0,
	parent=None,
):
	field = {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "wysiwyg",
		"instructions": instructions,
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"default_value": "",
		"allow_in_bindings": 1,
		"tabs": "all",
		"toolbar": "full",
		"media_upload": media_upload,
		"delay": 0,
	}
	if parent:
		field["parent_repeater"] = parent
	return field


def text(key, label, name, width="", instructions="", parent=None, default=""):
	field = {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "text",
		"instructions": instructions,
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"default_value": default,
		"maxlength": "",
		"allow_in_bindings": 1,
		"placeholder": "",
		"prepend": "",
		"append": "",
	}
	if parent:
		field["parent_repeater"] = parent
	return field


def textarea(key, label, name, width="", rows=3, instructions=""):
	return {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "textarea",
		"instructions": instructions,
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"default_value": "",
		"maxlength": "",
		"allow_in_bindings": 1,
		"placeholder": "",
		"rows": rows,
		"new_lines": "",
	}


def number_field(key, label, name, default=0, width="33"):
	return {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "number",
		"instructions": "",
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"default_value": default,
		"min": "",
		"max": "",
		"placeholder": "",
		"step": "",
		"prepend": "",
		"append": "",
	}


def image(key, label, name, width="", instructions="Use a clear, high-quality image. Avoid tiny or blurry images.", parent=None):
	field = {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "image",
		"instructions": instructions,
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"return_format": "array",
		"library": "all",
		"min_width": "",
		"min_height": "",
		"min_size": "",
		"max_width": "",
		"max_height": "",
		"max_size": "",
		"mime_types": "jpg,jpeg,png,webp,gif,svg",
		"allow_in_bindings": 1,
		"preview_size": "medium",
	}
	if parent:
		field["parent_repeater"] = parent
	return field


def link(key, label, name, width=""):
	return {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "link",
		"instructions": "",
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"return_format": "array",
	}


def select_field(key, label, name, choices, default="", width="", parent=None):
	field = {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "select",
		"instructions": "",
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"choices": choices,
		"default_value": default,
		"return_format": "value",
		"multiple": 0,
		"allow_null": 1,
		"ui": 0,
		"ajax": 0,
		"placeholder": "",
	}
	if parent:
		field["parent_repeater"] = parent
	return field


def fa(key, label, name, width="", parent=None):
	field = {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "font-awesome",
		"instructions": "Pick from the Solid set for a consistent look.",
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": width, "class": "", "id": ""},
		"icon_sets": ["solid"],
		"custom_icon_set": "",
		"fa_live_preview": "",
		"default_label": "",
		"default_value": "",
		"save_format": "element",
		"allow_null": 1,
		"show_preview": 1,
		"enqueue_fa": 0,
		"allow_in_bindings": 0,
	}
	if parent:
		field["parent_repeater"] = parent
	return field


def repeater(key, label, name, sub_fields, button="Add Item", collapsed="", layout="block"):
	return {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "repeater",
		"instructions": "",
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": "", "class": "", "id": ""},
		"layout": layout,
		"pagination": 0,
		"min": 0,
		"max": 0,
		"collapsed": collapsed,
		"button_label": button,
		"rows_per_page": 20,
		"sub_fields": sub_fields,
	}


def gallery(key, label, name):
	return {
		"key": key,
		"label": label,
		"name": name,
		"aria-label": "",
		"type": "gallery",
		"instructions": "",
		"required": 0,
		"conditional_logic": 0,
		"wrapper": {"width": "", "class": "", "id": ""},
		"return_format": "array",
		"library": "all",
		"min": 0,
		"max": 0,
		"min_width": "",
		"min_height": "",
		"min_size": "",
		"max_width": "",
		"max_height": "",
		"max_size": "",
		"mime_types": "jpg,jpeg,png,webp",
		"insert": "append",
		"preview_size": "medium",
	}


def layout(key, name, label, sub_fields):
	return {
		"key": key,
		"name": name,
		"label": label,
		"display": "block",
		"sub_fields": sub_fields,
		"min": "",
		"max": "",
	}


def build():
	layouts = {}

	layouts["layout_659a3e18d6a34"] = layout(
		"layout_659a3e18d6a34",
		"text_block",
		"Text Block",
		[wysiwyg("field_plwp_1001", "Content", "content")],
	)

	layouts["layout_659abc6d9d13a"] = layout(
		"layout_659abc6d9d13a",
		"image_text_block",
		"Image + Text Block",
		[
			image("field_plwp_1004", "Image", "image", "50"),
			wysiwyg("field_plwp_1003", "Content", "content", width="50"),
		],
	)

	layouts["layout_wpk_cta"] = layout(
		"layout_wpk_cta",
		"cta_block",
		"CTA Block",
		[
			textarea(
				"field_wpk_cta_heading",
				"Heading",
				"heading",
				width="50",
				rows=3,
				instructions="One line per stacked heading line.",
			),
			wysiwyg(
				"field_wpk_cta_content",
				"Content",
				"content",
				width="50",
				instructions="Supporting text beside the heading. For SEO reasons, do not use an H1.",
			),
			link("field_wpk_cta_link", "Button", "link"),
		],
	)

	layouts["layout_69d5dc9fabe40"] = layout(
		"layout_69d5dc9fabe40",
		"list_block",
		"List Block",
		[
			wysiwyg("field_plwp_1010", "Intro", "intro"),
			repeater(
				"field_plwp_1013",
				"List Items",
				"list_items",
				[
					fa("field_wpk_list_icon", "Icon", "icon", "20", parent="field_plwp_1013"),
					text("field_plwp_1011", "List Item Title", "list_item_title", "80", parent="field_plwp_1013"),
				],
				button="Add List Item",
				collapsed="field_plwp_1011",
			),
			link("field_plwp_1014", "Button (Optional)", "link"),
		],
	)

	layouts["layout_69d5dd69abe44"] = layout(
		"layout_69d5dd69abe44",
		"quote_block",
		"Quote Block",
		[
			wysiwyg(
				"field_plwp_1021",
				"Intro",
				"intro",
				instructions="Section heading and any lead-in. For SEO reasons, do not use an H1.",
			),
			wysiwyg(
				"field_plwp_1022",
				"Quote",
				"quote",
				instructions="You can add italics, links, and other basic formatting. For SEO reasons, do not use any headers or buttons here.",
			),
			text("field_plwp_1024", "Name", "name", "50"),
			text("field_plwp_1025", "Attribution", "attribution", "50", "Title, company, location, etc."),
		],
	)

	layouts["layout_69d5ddc9abe4c"] = layout(
		"layout_69d5ddc9abe4c",
		"form_block",
		"Form Block",
		[
			wysiwyg("field_plwp_1040", "Intro", "intro"),
			{
				"key": "field_plwp_1041",
				"label": "Form",
				"name": "form_id",
				"aria-label": "",
				"type": "select",
				"instructions": "Choose the Gravity Form to display. Forms are listed by title.",
				"required": 0,
				"conditional_logic": 0,
				"wrapper": {"width": "", "class": "", "id": ""},
				"choices": {},
				"default_value": False,
				"return_format": "value",
				"multiple": 0,
				"allow_null": 1,
				"ui": 1,
				"ajax": 0,
				"placeholder": "Select a form",
				"create_options": 0,
				"save_options": 0,
			},
		],
	)

	layouts["layout_69d5ddafabe4a"] = layout(
		"layout_69d5ddafabe4a",
		"accordion_block",
		"Accordion Block",
		[
			image(
				"field_wpk_acc_image",
				"Image",
				"image",
				instructions="Optional rounded banner above the accordion.",
			),
			wysiwyg("field_plwp_1035", "Intro", "intro"),
			repeater(
				"field_plwp_1038",
				"Accordion Items",
				"accordion_items",
				[
					text("field_plwp_1036", "Item Title", "accordion_title", "35", parent="field_plwp_1038"),
					wysiwyg(
						"field_plwp_1037",
						"Item Content",
						"accordion_content",
						instructions="You can add text, inline links and buttons. For SEO reasons, do not use any headers here.",
						parent="field_plwp_1038",
					),
				],
				button="Add Accordion Item",
				collapsed="field_plwp_1036",
			),
		],
	)

	layouts["layout_wpk_headers_text"] = layout(
		"layout_wpk_headers_text",
		"headers_text_block",
		"Headers and Text Block",
		[
			text("field_wpk_ht_header", "Header", "header", instructions="Small section label (e.g. THE PROCESS)."),
			image("field_wpk_ht_image", "Side Image", "image", "50"),
			select_field(
				"field_wpk_ht_side",
				"Image Side",
				"image_side",
				{"left": "Left", "right": "Right"},
				"right",
				"50",
			),
			repeater(
				"field_wpk_ht_items",
				"Items",
				"items",
				[
					text("field_wpk_ht_item_title", "Item Title", "item_title", "40", parent="field_wpk_ht_items"),
					wysiwyg(
						"field_wpk_ht_item_text",
						"Item Text",
						"item_text",
						instructions="For SEO reasons, do not use any headers here.",
						width="60",
						parent="field_wpk_ht_items",
					),
				],
				button="Add Item",
				collapsed="field_wpk_ht_item_title",
			),
			link("field_wpk_ht_link", "Button (Optional)", "link"),
		],
	)

	layouts["layout_wpk_bio"] = layout(
		"layout_wpk_bio",
		"bio_block",
		"Bio Block",
		[
			image(
				"field_wpk_bio_icon",
				"Heading Icon",
				"heading_icon",
				"30",
				"Optional icon beside the heading (e.g. waving hand).",
			),
			wysiwyg("field_wpk_bio_content", "Content", "content", width="70"),
			gallery("field_wpk_bio_photos", "Photos", "photos"),
		],
	)

	layouts["layout_wpk_projects"] = layout(
		"layout_wpk_projects",
		"projects_block",
		"Projects Block",
		[
			wysiwyg("field_wpk_proj_intro", "Intro", "intro"),
			repeater(
				"field_wpk_proj_items",
				"Projects",
				"projects",
				[
					image("field_wpk_proj_shot", "Screenshot", "screenshot", "40", parent="field_wpk_proj_items"),
					select_field(
						"field_wpk_proj_side",
						"Image Side",
						"image_side",
						{"left": "Left", "right": "Right"},
						"left",
						"20",
						parent="field_wpk_proj_items",
					),
					wysiwyg("field_wpk_proj_content", "Content", "content", width="40", parent="field_wpk_proj_items"),
				],
				button="Add Project",
				collapsed="field_wpk_proj_content",
			),
			link("field_wpk_proj_link", "Button (Optional)", "link"),
		],
	)

	layouts["layout_wpk_lead"] = layout(
		"layout_wpk_lead",
		"lead_capture_block",
		"Lead Capture Block",
		[
			wysiwyg("field_wpk_lead_intro", "Intro", "intro"),
			image("field_wpk_lead_shot", "Screenshot", "screenshot", "40"),
			wysiwyg("field_wpk_lead_content", "Content", "content", width="60"),
			link("field_wpk_lead_link", "Button", "link"),
		],
	)

	layouts["layout_wpk_selection"] = layout(
		"layout_wpk_selection",
		"selection_block",
		"Selection Block",
		[
			wysiwyg("field_wpk_sel_intro", "Intro", "intro"),
			repeater(
				"field_wpk_sel_services",
				"Services",
				"services",
				[
					fa("field_wpk_sel_icon", "Icon", "icon", "20", parent="field_wpk_sel_services"),
					text("field_wpk_sel_label", "Label", "label", "30", parent="field_wpk_sel_services"),
					wysiwyg(
						"field_wpk_sel_detail",
						"Detail Content",
						"detail_content",
						width="50",
						parent="field_wpk_sel_services",
					),
				],
				button="Add Service",
				collapsed="field_wpk_sel_label",
			),
		],
	)

	layouts["layout_wpk_pricing"] = layout(
		"layout_wpk_pricing",
		"pricing_block",
		"Pricing Block",
		[
			wysiwyg("field_wpk_price_intro", "Intro", "intro"),
			text("field_wpk_price_incl_label", "Includes Label", "includes_label"),
			repeater(
				"field_wpk_price_includes",
				"Includes",
				"includes",
				[text("field_wpk_price_incl_item", "Include Label", "include_label", parent="field_wpk_price_includes")],
				button="Add Include",
				collapsed="field_wpk_price_incl_item",
				layout="table",
			),
			repeater(
				"field_wpk_price_plans",
				"Plans",
				"plans",
				[
					text("field_wpk_plan_price", "Price", "price", "20", parent="field_wpk_price_plans"),
					text(
						"field_wpk_plan_suffix",
						"Price Suffix",
						"price_suffix",
						"15",
						parent="field_wpk_price_plans",
						default="/mo",
					),
					text("field_wpk_plan_title", "Plan Title", "plan_title", "30", parent="field_wpk_price_plans"),
					text("field_wpk_plan_tokens", "Tokens Line", "tokens_line", "35", parent="field_wpk_price_plans"),
					text(
						"field_wpk_plan_best_label",
						"Best For Label",
						"best_for_label",
						"30",
						parent="field_wpk_price_plans",
						default="Best for",
					),
					text("field_wpk_plan_best", "Best For", "best_for", "70", parent="field_wpk_price_plans"),
				],
				button="Add Plan",
				collapsed="field_wpk_plan_title",
			),
			wysiwyg("field_wpk_price_footer", "Footer", "footer"),
		],
	)

	layouts["layout_wpk_dyn_price"] = layout(
		"layout_wpk_dyn_price",
		"dynamic_pricing_block",
		"Dynamic Pricing Block",
		[
			wysiwyg("field_wpk_dyn_intro", "Intro", "intro"),
			repeater(
				"field_wpk_dyn_options",
				"Payment Options",
				"payment_options",
				[
					text("field_wpk_dyn_opt_title", "Option Title", "option_title", "35", parent="field_wpk_dyn_options"),
					wysiwyg(
						"field_wpk_dyn_opt_text",
						"Option Text",
						"option_text",
						width="65",
						parent="field_wpk_dyn_options",
					),
				],
				button="Add Payment Option",
				collapsed="field_wpk_dyn_opt_title",
			),
			text("field_wpk_dyn_calc_title", "Calculator Title", "calculator_title", default="Pricing Calculator"),
			number_field("field_wpk_dyn_base", "Base Site Price", "base_site", 900),
			number_field("field_wpk_dyn_monthly", "Monthly Service", "monthly_service", 69),
			number_field("field_wpk_dyn_term", "Term Months", "term_months", 12),
			number_field("field_wpk_dyn_crm", "VacationCRM Addon", "addon_vacation_crm", 250),
			number_field("field_wpk_dyn_copy", "White Glove Copy Addon", "addon_white_glove_copy", 400),
			number_field("field_wpk_dyn_design", "Graphic Design Addon", "addon_graphic_design", 400),
		],
	)

	layouts["layout_wpk_case"] = layout(
		"layout_wpk_case",
		"case_study_block",
		"Case Study Block",
		[
			image("field_wpk_case_shot", "Screenshot", "screenshot"),
			text("field_wpk_case_eyebrow", "Eyebrow", "eyebrow", "30"),
			text("field_wpk_case_title", "Title", "title", "40"),
			text("field_wpk_case_subtitle", "Subtitle", "subtitle", "30"),
			wysiwyg("field_wpk_case_intro", "Intro", "intro"),
			repeater(
				"field_wpk_case_sections",
				"Sections",
				"sections",
				[
					text(
						"field_wpk_case_sec_title",
						"Section Title",
						"section_title",
						"35",
						parent="field_wpk_case_sections",
					),
					wysiwyg(
						"field_wpk_case_sec_text",
						"Section Text",
						"section_text",
						width="65",
						parent="field_wpk_case_sections",
					),
				],
				button="Add Section",
				collapsed="field_wpk_case_sec_title",
			),
			text("field_wpk_case_work_heading", "Work Heading", "work_heading"),
			repeater(
				"field_wpk_case_work",
				"Work Items",
				"work_items",
				[
					fa("field_wpk_case_work_icon", "Icon", "icon", "25", parent="field_wpk_case_work"),
					text("field_wpk_case_work_label", "Item Label", "item_label", "75", parent="field_wpk_case_work"),
				],
				button="Add Work Item",
				collapsed="field_wpk_case_work_label",
				layout="table",
			),
			link("field_wpk_case_link", "Button (Optional)", "link"),
		],
	)

	group = {
		"key": "group_659a3e1127750",
		"title": "Body Section",
		"fields": [
			{
				"key": "field_659a3e1139195",
				"label": "Body Sections",
				"name": "body_sections",
				"aria-label": "",
				"type": "flexible_content",
				"instructions": "",
				"required": 0,
				"conditional_logic": 0,
				"wrapper": {"width": "", "class": "", "id": ""},
				"layouts": layouts,
				"min": "",
				"max": "",
				"button_label": "Add New Block",
			}
		],
		"location": [
			[{"param": "post_template", "operator": "==", "value": "default"}],
			[{"param": "page_type", "operator": "==", "value": "front_page"}],
			[{"param": "post_template", "operator": "==", "value": "frontpage.php"}],
		],
		"menu_order": 2,
		"position": "acf_after_title",
		"style": "default",
		"label_placement": "top",
		"instruction_placement": "label",
		"hide_on_screen": "",
		"active": True,
		"description": "",
		"show_in_rest": 0,
		"display_title": "",
		"allow_ai_access": False,
		"ai_description": "",
		"modified": int(time.time()),
	}

	out = Path(__file__).resolve().parents[1] / "acf-json" / "group_659a3e1127750.json"
	with out.open("w", encoding="utf-8") as handle:
		json.dump(group, handle, indent="\t")
		handle.write("\n")
	print(f"Wrote {out}")
	print("Layouts:", [layouts[k]["name"] for k in layouts])


if __name__ == "__main__":
	build()
