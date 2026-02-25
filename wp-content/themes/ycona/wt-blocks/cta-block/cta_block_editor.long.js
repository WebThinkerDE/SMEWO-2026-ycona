(function (wp, blocks, element, blockEditor, components) {
	var el = element.createElement,
		Fragment = element.Fragment,
		registerBlockType = blocks.registerBlockType,
		MediaUpload = blockEditor.MediaUpload,
		InspectorControls = blockEditor.InspectorControls,
		RichText = blockEditor.RichText,
		useBlockProps = blockEditor.useBlockProps,
		SelectControl = components.SelectControl,
		TextControl = components.TextControl;

	registerBlockType("wt/cta-block", {
		apiVersion: 3,
		title: "CTA Block",
		icon: "phone",
		category: 'wt-shop-blocks',
		description: "Call-to-action: description, working time, phone, email. Right side: talking person image (can overflow top/bottom on frontend).",
		example: {},

		attributes: {
			description: { type: "string" },
			working_time: { type: "string" },
			phone: { type: "string" },
			email: { type: "string" },
			image: { type: "object" },
			image_alt: { type: "string" },
			class_name: { type: "string" },
			space_top: { type: "string" },
			space_bottom: { type: "string" },
			background_color: { type: "string" },
			text_color: { type: "string" },
		},

		edit: function (props) {
			var attrs = props.attributes;
			var set = props.setAttributes;

			if (!attrs.space_top) set({ space_top: "yes" });
			if (!attrs.space_bottom) set({ space_bottom: "yes" });
			if (!attrs.background_color) set({ background_color: "light-gray" });
			if (!attrs.text_color) set({ text_color: "black" });

			function updateImage(media) {
				set({ image: media ? { id: media.id, url: media.url } : null });
			}

			var imgUrl = attrs.image && attrs.image.url ? attrs.image.url : "";
			var blockProps = useBlockProps ? useBlockProps({ className: "webthiker-block worker-block cta-block-editor" }) : { className: "webthiker-block worker-block cta-block-editor" };

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					{ key: "inspector", class: "wt-shop-SelectControl" },
					el(
						"div",
						{ className: "webthiker-block-sidebar-element" },
						el("strong", null, "Section background"),
						el(SelectControl, {
							value: attrs.background_color || "light-gray",
							options: [
								{ value: "white", label: "White" },
								{ value: "light-gray", label: "Light gray" },
								{ value: "primary", label: "Primary" },
								{ value: "primary-dark", label: "Primary dark" },
								{ value: "secondary", label: "Secondary" },
								{ value: "gray", label: "Gray" },
								{ value: "black", label: "Black" },
							],
							onChange: function (val) { set({ background_color: val }); },
						}),
						el("strong", null, "Text color"),
						el(SelectControl, {
							value: attrs.text_color || "black",
							options: [
								{ value: "white", label: "White" },
								{ value: "black", label: "Black" },
								{ value: "primary", label: "Primary" },
								{ value: "secondary", label: "Secondary" },
								{ value: "gray", label: "Gray" },
							],
							onChange: function (val) { set({ text_color: val }); },
						}),
						el("strong", null, "Abstand oben"),
						el(SelectControl, {
							value: attrs.space_top || "yes",
							options: [
								{ value: "yes", label: "Ja" },
								{ value: "no", label: "Nein" },
							],
							onChange: function (val) { set({ space_top: val }); },
						}),
						el("strong", null, "Abstand unten"),
						el(SelectControl, {
							value: attrs.space_bottom || "yes",
							options: [
								{ value: "yes", label: "Ja" },
								{ value: "no", label: "Nein" },
							],
							onChange: function (val) { set({ space_bottom: val }); },
						})
					)
				),
				el(
					"div",
					blockProps,
					el("h3", null, "CTA Block"),
					el(
						"dl",
						null,
						el("dt", null, "Description"),
						el(
							"dd",
							null,
							el(RichText, {
								value: attrs.description || "",
								onChange: function (val) { set({ description: val }); },
								placeholder: "Short description or headline…",
								multiline: "p",
								tagName: "div",
							})
						),
						el("dt", null, "Working time"),
						el(
							"dd",
							null,
							el(TextControl, {
								value: attrs.working_time || "",
								onChange: function (val) { set({ working_time: val }); },
								placeholder: "e.g. Mo–Fr 9:00–18:00",
							})
						),
						el("dt", null, "Phone"),
						el(
							"dd",
							null,
							el(TextControl, {
								value: attrs.phone || "",
								onChange: function (val) { set({ phone: val }); },
								placeholder: "+49 123 456789",
							})
						),
						el("dt", null, "Email"),
						el(
							"dd",
							null,
							el(TextControl, {
								value: attrs.email || "",
								onChange: function (val) { set({ email: val }); },
								placeholder: "contact@example.com",
							})
						),
						el("dt", null, "Image (talking person)"),
						el(
							"dd",
							null,
							el(MediaUpload, {
								onSelect: updateImage,
								allowedTypes: ["image"],
								value: attrs.image && attrs.image.id ? attrs.image.id : undefined,
								render: function (ref) {
									var open = ref.open;
									return el(
										"button",
										{ type: "button", className: "button", onClick: open },
										"Choose image"
									);
								},
							}),
							imgUrl
								? el("div", { style: { marginTop: "8px" } }, el("img", { src: imgUrl, alt: "", style: { maxWidth: "200px", height: "auto" } }))
								: null
						),
						el("dt", null, "Image alt text"),
						el(
							"dd",
							null,
							el(TextControl, {
								value: attrs.image_alt || "",
								onChange: function (val) { set({ image_alt: val }); },
								placeholder: "Alt text",
							})
						),
						el("dt", null, "Section background"),
						el("dd", null, attrs.background_color || "light-gray"),
						el("dt", null, "Text color"),
						el("dd", null, attrs.text_color || "black"),
						el("dt", null, "Abstand oben"),
						el("dd", null, (attrs.space_top || "yes") === "yes" ? "Ja" : "Nein"),
						el("dt", null, "Abstand unten"),
						el("dd", null, (attrs.space_bottom || "yes") === "yes" ? "Ja" : "Nein")
					)
				)
			);
		},

		save: function () {
			return null;
		},
	});
})(window.wp, window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
