(function (wp, blocks, element, blockEditor, components) {
   var el = element.createElement,
      Fragment = element.Fragment,
      registerBlockType = blocks.registerBlockType,
      MediaUpload = blockEditor.MediaUpload,
      InspectorControls = blockEditor.InspectorControls,
      useBlockProps = blockEditor.useBlockProps,
      RichText = blockEditor.RichText,
      SelectControl = components.SelectControl,
      TextControl = components.TextControl;

   registerBlockType("wt/split-block", {
      apiVersion: 3,
      title: "Split Block",
      icon: "layout",
      category: 'wt-shop-blocks',
      description:
         "Content + image block. Choose image side (left/right) and mobile order (content first or image first).",
      example: {},
      attributes: {
         image: { type: "object" },
         image_alt: { type: "string" },
         image_position_desktop: { type: "string" },
         mobile_order: { type: "string" },
         layout: { type: "string" },
         subheadline: { type: "string" },
         headline: { type: "string" },
         description: { type: "string" },
         background_color: { type: "string" },
         text_color: { type: "string" },
         button_1_text: { type: "string" },
         button_1_link: { type: "string" },
         button_1_target: { type: "string" },
         button_1_style: { type: "string" },
         button_2_text: { type: "string" },
         button_2_link: { type: "string" },
         button_2_target: { type: "string" },
         button_2_style: { type: "string" },
         class_name: { type: "string" },
         space_top: { type: "string" },
         space_bottom: { type: "string" },
      },

      edit: function (props) {
         var attrs = props.attributes;
         var set = props.setAttributes;

         if (!attrs.image_position_desktop)
            set({ image_position_desktop: "right" });
         if (!attrs.mobile_order) set({ mobile_order: "content_first" });
         if (!attrs.layout) set({ layout: "fullWidth" });
         if (!attrs.background_color) set({ background_color: "primary" });
         if (!attrs.text_color) set({ text_color: "white" });
         if (!attrs.button_1_target) set({ button_1_target: "_self" });
         if (!attrs.button_1_style) set({ button_1_style: "outline" });
         if (!attrs.button_2_target) set({ button_2_target: "_self" });
         if (!attrs.button_2_style) set({ button_2_style: "outline" });
         if (!attrs.space_top) set({ space_top: "yes" });
         if (!attrs.space_bottom) set({ space_bottom: "yes" });

         function updateImage(media) {
            set({ image: media ? { id: media.id, url: media.url } : null });
         }

         var imgUrl = attrs.image && attrs.image.url ? attrs.image.url : "";
         var blockProps = useBlockProps({ className: "webthiker-block worker-block split-block-editor" });

         return el(
            Fragment,
            null,
            el(
               InspectorControls,
               { key: "inspector", class: "wt-shop-SelectControl" },
               el(
                  "div",

                  { className: "webthiker-block-sidebar-element" },
                  el("strong", null, "Image position (desktop)"),
                  el(SelectControl, {
                     value: attrs.image_position_desktop,
                     options: [
                        { value: "left", label: "Image left" },
                        { value: "right", label: "Image right" },
                     ],
                     onChange: function (val) {
                        set({ image_position_desktop: val });
                     },
                  }),
                  el("strong", null, "Mobile order"),
                  el(SelectControl, {
                     value: attrs.mobile_order,
                     options: [
                        {
                           value: "content_first",
                           label: "Content before image",
                        },
                        { value: "image_first", label: "Image before content" },
                     ],
                     onChange: function (val) {
                        set({ mobile_order: val });
                     },
                  }),

                  el("strong", null, "Layout"),
                  el(SelectControl, {
                     value: attrs.layout,
                     options: [
                        { value: "fullWidth", label: "Full width" },
                        { value: "boxed", label: "Boxed" },
                     ],
                     onChange: function (val) {
                        set({ layout: val });
                     },
                  }),
                  el("strong", null, "Content background"),
                  el(SelectControl, {
                     value: attrs.background_color,
                     options: [
                        { value: "white", label: "White" },
                        { value: "light-gray", label: "Light gray" },
                        { value: "primary", label: "Primary" },
                        { value: "primary-dark", label: "Primary dark" },
                        { value: "secondary", label: "Secondary" },
                        { value: "gray", label: "Gray" },
                        { value: "black", label: "Black" },
                     ],
                     onChange: function (val) {
                        set({ background_color: val });
                     },
                  }),
                  el("strong", null, "Content text color"),
                  el(SelectControl, {
                     value: attrs.text_color,
                     options: [
                        { value: "white", label: "White" },
                        { value: "black", label: "Black" },
                        { value: "primary", label: "Primary" },
                        { value: "secondary", label: "Secondary" },
                        { value: "gray", label: "Gray" },
                     ],
                     onChange: function (val) {
                        set({ text_color: val });
                     },
                  }),
                  el("strong", null, "Button 1 style"),
                  el(SelectControl, {
                     value: attrs.button_1_style,
                     options: [
                        { value: "full", label: "Primary (filled)" },
                        { value: "outline", label: "Outline" },
                        { value: "secondary", label: "Secondary" },
                     ],
                     onChange: function (val) {
                        set({ button_1_style: val });
                     },
                  }),
                  el("strong", null, "Button 2 style"),
                  el(SelectControl, {
                     value: attrs.button_2_style,
                     options: [
                        { value: "full", label: "Primary (filled)" },
                        { value: "outline", label: "Outline" },
                        { value: "secondary", label: "Secondary" },
                     ],
                     onChange: function (val) {
                        set({ button_2_style: val });
                     },
                  }),
                  el("strong", null, "Open in"),
                  el(SelectControl, {
                     value: attrs.button_1_target,
                     options: [
                        { value: "_self", label: "Same tab" },
                        { value: "_blank", label: "New tab" },
                     ],
                     onChange: function (val) {
                        set({ button_1_target: val, button_2_target: val });
                     },
                  }),
                  el("strong", null, "Abstand oben"),
                  el(SelectControl, {
                     value: attrs.space_top || "yes",
                     options: [
                        { value: "yes", label: "Ja" },
                        { value: "no", label: "Nein" },
                     ],
                     onChange: function (val) {
                        set({ space_top: val });
                     },
                  }),
                  el("strong", null, "Abstand unten"),
                  el(SelectControl, {
                     value: attrs.space_bottom || "yes",
                     options: [
                        { value: "yes", label: "Ja" },
                        { value: "no", label: "Nein" },
                     ],
                     onChange: function (val) {
                        set({ space_bottom: val });
                     },
                  }),
               ),
            ),
            el(
               "div",
               blockProps,
               el("h3", null, "Split Block"),
               el(
                  "dl",
                  null,
                  el("dt", null, "Image"),
                  el(
                     "dd",
                     null,
                     el(MediaUpload, {
                        onSelect: updateImage,
                        allowedTypes: ["image"],
                        value:
                           attrs.image && attrs.image.id
                              ? attrs.image.id
                              : undefined,
                        render: function (_ref) {
                           var open = _ref.open;
                           return el(
                              "button",
                              {
                                 type: "button",
                                 className: "button",
                                 onClick: open,
                              },
                              "Choose image",
                           );
                        },
                     }),
                     imgUrl
                        ? el(
                             "div",
                             { style: { marginTop: "8px" } },
                             el("img", {
                                src: imgUrl,
                                alt: "",
                                style: { maxWidth: "200px", height: "auto" },
                             }),
                          )
                        : null,
                  ),
                  el("dt", null, "Image alt text"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.image_alt || "",
                        onChange: function (val) {
                           set({ image_alt: val });
                        },
                        placeholder: "Alt text for image",
                     }),
                  ),
                  el("dt", null, "Subheadline"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.subheadline || "",
                        onChange: function (val) {
                           set({ subheadline: val });
                        },
                        placeholder: "e.g. By phone or via contact form",
                     }),
                  ),
                  el("dt", null, "Headline"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.headline || "",
                        onChange: function (val) {
                           set({ headline: val });
                        },
                        placeholder: "e.g. Your contact to us",
                     }),
                  ),
                  el("dt", null, "Description (rich text)"),
                  el(
                     "dd",
                     null,
                     el(RichText, {
                        value: attrs.description || "",
                        onChange: function (val) {
                           set({ description: val });
                        },
                        placeholder: "Enter description (bold, italic, links…)",
                        multiline: "p",
                        tagName: "div",
                     }),
                  ),
                  el("dt", null, "Button 1 - Text"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.button_1_text || "",
                        onChange: function (val) {
                           set({ button_1_text: val });
                        },
                        placeholder: "e.g. Contact sales",
                     }),
                  ),
                  el("dt", null, "Button 1 - Link"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.button_1_link || "",
                        onChange: function (val) {
                           set({ button_1_link: val });
                        },
                        placeholder: "https://",
                     }),
                  ),
                  el("dt", null, "Button 2 - Text"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.button_2_text || "",
                        onChange: function (val) {
                           set({ button_2_text: val });
                        },
                        placeholder: "e.g. Contact customer service",
                     }),
                  ),
                  el("dt", null, "Button 2 - Link"),
                  el(
                     "dd",
                     null,
                     el(TextControl, {
                        value: attrs.button_2_link || "",
                        onChange: function (val) {
                           set({ button_2_link: val });
                        },
                        placeholder: "https://",
                     }),
                  ),
                  el("dt", null, "Abstand oben"),
                  el("dd", null, (attrs.space_top || "yes") === "yes" ? "Ja" : "Nein"),
                  el("dt", null, "Abstand unten"),
                  el("dd", null, (attrs.space_bottom || "yes") === "yes" ? "Ja" : "Nein"),
               ),
            ),
         );
      },

      save: function () {
         return null;
      },
   });
})(
   window.wp,
   window.wp.blocks,
   window.wp.element,
   window.wp.blockEditor,
   window.wp.components,
);
