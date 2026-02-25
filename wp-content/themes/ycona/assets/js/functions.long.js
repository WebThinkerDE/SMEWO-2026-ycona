// native dom ready function
var dom_rdy = function (fn) {
   //sanity check
   if (typeof fn !== "function") {
      return;
   }

   //if document is already loaded, run method
   if (document.readyState === "complete") {
      return fn();
   }

   //otherwise, wait until document is loaded
   document.addEventListener("DOMContentLoaded", fn, false);
};

//native on dom ready
dom_rdy(function () {});

document.addEventListener("DOMContentLoaded", function () {
   var header = document.querySelector("header.header-wt-shop");
   var logo_default = document.querySelector(".logo-desktop");
   var logo_active = document.querySelector(".logo-desktop-active");

   // Create a spacer to prevent content jump when header becomes fixed.
   var header_spacer = document.createElement("div");
   header_spacer.className = "wt-shop-header-spacer";
   header_spacer.style.display = "none";
   header.parentNode.insertBefore(header_spacer, header.nextSibling);

   var is_fixed = false;
   var scroll_threshold = 300;
   window.addEventListener("scroll", function () {
      if (window.scrollY > scroll_threshold) {
         if (!is_fixed) {
            header_spacer.style.height = header.offsetHeight + "px";
            header_spacer.style.display = "block";
            is_fixed = true;
         }
         header.classList.add("active");
         if (logo_default) logo_default.classList.add("d-none");
         if (logo_active) logo_active.classList.remove("d-none");
      } else {
         if (is_fixed) {
            header_spacer.style.display = "none";
            is_fixed = false;
         }
         header.classList.remove("active");
         if (logo_default) logo_default.classList.remove("d-none");
         if (logo_active) logo_active.classList.add("d-none");
      }
   });
});

//change border of required fields
jQuery(document).ready(function ($) {
   $("form")
      .find("input[required]")
      .on("invalid", function (e) {
         e.preventDefault();
         $(this).css("border", "1px solid #0BD876");
      });

});

// language switcher
// document.addEventListener('DOMContentLoaded', function () {
//     const activeLang = document.querySelector('.lang-active');
//     const dropdown = document.querySelector('.lang-dropdown');
//
//     if (activeLang) {
//         activeLang.addEventListener('click', function (e) {
//             e.preventDefault();
//             dropdown.classList.toggle('show-lang-dropdown');
//         });
//     }
//
//     document.addEventListener('click', function (e) {
//         if (!e.target.closest('.lang-switcher')) {
//             dropdown.classList.remove('show-lang-dropdown');
//         }
//     });
// });

//contact form 7
document.addEventListener("DOMContentLoaded", function () {
   var cf7_btn = document.querySelector(".wpcf7 .wpcf7-submit");
   if (cf7_btn) {
      var btn_text = cf7_btn.value;
      var custom_button = document.createElement("button");
      custom_button.type = "submit";
      custom_button.className = "wpcf7-submit btn-full btn-full-primary";
      custom_button.innerHTML = "" + btn_text;

      cf7_btn.parentNode.replaceChild(custom_button, cf7_btn);
   }
});

/*Contact form Opening and closing Tab*/
document.addEventListener("DOMContentLoaded", function () {
   var open_mega_menu = document.getElementById("open-mega-menu");
   var panel_left = document.getElementById("panel-left");
   var panel_right = document.getElementById("panel-right");

   open_mega_menu.addEventListener("click", function () {
      this.classList.toggle("active");
      panel_left.classList.toggle("open-left");
      panel_right.classList.toggle("open-right");
   });

   var mega_close = document.getElementById("mega-menu-close");
   if (mega_close) {
      mega_close.addEventListener("click", function () {
         open_mega_menu.classList.remove("active");
         panel_left.classList.remove("open-left");
         panel_right.classList.remove("open-right");
      });
   }
});

// Wait for DOM
document.addEventListener("DOMContentLoaded", () => {
   // Select all arrow wrappers (top-level, sub, and sub-sub menu arrows)
   const arrow_wrappers = document.querySelectorAll(
      ".mega-menu-mobile-arrow, .sub-mega-menu-mobile-arrowe, .sub-sub-mega-menu-mobile-arrow",
   );

   arrow_wrappers.forEach((arrow_wrapper) => {
      // Grab the two arrow images
      const arrow_menu_open = arrow_wrapper.querySelector(".arrow-menu-open");
      const arrow_menu_close = arrow_wrapper.querySelector(".arrow-menu-close");

      // Find the direct child submenu <ul> in the same <li> (works for any depth)
      const parent_li = arrow_wrapper.closest("li");
      const sub_menu = parent_li.querySelector(":scope > ul.dropdown-menu");
      if (!sub_menu) return;

      // Ensure submenu is hidden by default
      sub_menu.classList.add("d-none");

      // Bind click
      arrow_wrapper.addEventListener("click", () => {
         // Toggle open/close icons
         arrow_menu_open.classList.toggle("d-none");
         arrow_menu_close.classList.toggle("d-none");

         // Toggle submenu visibility
         sub_menu.classList.toggle("d-none");
      });
   });
});

(function () {
   var el = document.getElementById('<?php echo esc_js( $root_id ); ?>');
   if (!el || el.dataset.init === '1') return;
   el.dataset.init = '1';
   var btn = el.querySelector('.wt-lang-dd__trigger');
   if (btn) {
      btn.addEventListener('click', function (e) {
         e.preventDefault();
         el.classList.toggle('is-open');
         btn.setAttribute('aria-expanded', el.classList.contains('is-open') ? 'true' : 'false');
      });
   }
   document.addEventListener('click', function (e) {
      if (!el.contains(e.target)) {
         el.classList.remove('is-open');
         if (btn) btn.setAttribute('aria-expanded', 'false');
      }
   });
})();

