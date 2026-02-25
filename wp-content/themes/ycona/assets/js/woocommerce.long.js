/* custom header search product */
jQuery(document).ready(function ($) {
   var delay_timer; // Set a timer
   var delay_time = 500; // Set delay time in milliseconds

   // Function to perform the AJAX request
   function perform_search() {
      var search_term = $("#search_term").val().trim();

      // If search_term is empty, clear the results and exit from this function
      if (!search_term) {
         $("#search_term").removeClass("active-border");
         $(".submit-button").removeClass("active-border");
         $("#search-results").html("");
         return;

      }
      $.ajax({
         url: wt_ajax.ajaxurl,
         type: "GET",
         data: {
            action: "custom_woocommerce_product_search",
            search_term: search_term,
         },

         success: function (response) {
            if (response.trim() === "") {
               $("#search-results").html("No results found");
            } else {
               $("#search_term").addClass("active-border");
               $(".submit-button").addClass("active-border");
               $("#search-results").html(
                  response + '<span class="close-icon-search"></span>',
               );
            }

         },
      });
   }

   // Trigger search on input
   $("#search_term").on("input", function () {
      // Clear existing timer on each input event
      clearTimeout(delay_timer);

      // Set the new timer
      delay_timer = setTimeout(perform_search, delay_time);
   });

   // Trigger search on form submit
   $("#wt-shop-custom-search-form").on("submit", function (e) {
      e.preventDefault();
      perform_search();
   });

   // Clear search results when clicked on close icon
   $("#search-results").on("click", ".close-icon-search", function (e) {
      $("#search-results").html("");
   });
});

/* custom header search mobil product */

jQuery(document).ready(function ($) {
   var delay_timer_mobile; // Set a timer for mobile
   var delay_time = 500; // Set delay time in milliseconds

   // Function to perform the AJAX request
   function perform_search_mobile() {
      var search_term_mobile = $("#search_term-mobile").val().trim();

      // Check if search_term is empty
      if (!search_term_mobile) {
         $("#search-results-mobile").html("");
         return;
      }

      // Perform AJAX request
      $.ajax({
         url: wt_ajax.ajaxurl,
         type: "GET",
         data: {
            action: "custom_woocommerce_product_search_mobile",
            search_term_mobile: search_term_mobile,
         },
         success: function (response) {
            $("#search-results-mobile").html(
               response + '<span class="close-icon-search"></span>',
            );
         },
      });
   }

   // Trigger search on input
   $("#search_term-mobile").on("input", function () {
      clearTimeout(delay_timer_mobile); // Clear existing timer on each input event
      delay_timer_mobile = setTimeout(perform_search_mobile, delay_time); // Set the new timer
   });

   // Trigger search on form submit
   $("#wt-shop-custom-search-form-mobile").on("submit", function (e) {
      e.preventDefault();
      perform_search_mobile();
   });

   // Clear search results when clicked on close icon
   $("#search-results-mobile").on("click", ".close-icon-search", function (e) {
      $("#search-results-mobile").html("");
   });
});

/* Mini cart panel – open/close */
document.addEventListener("DOMContentLoaded", function () {
   var trigger = document.getElementById("wt-mini-cart-trigger");
   var panel = document.getElementById("wt-mini-cart-panel");
   var close_btn = document.getElementById("wt-mini-cart-close");
   if (!trigger || !panel) return;
   var panel_model = panel.getAttribute("data-model") || "panel";
   var is_dropdown = panel_model === "dropdown";
   var locked_by_click = false;
   var hover_close_timer = null;

   function open_panel() {
      panel.classList.add("wt-mini-cart-panel-open");
      panel.setAttribute("aria-hidden", "false");
      panel.setAttribute("data-open", "true");
      trigger.setAttribute("aria-expanded", "true");
      if (is_dropdown) {
         document.body.classList.add("wt-mini-cart-dropdown-open");
      } else {
         document.body.classList.add("wt-mini-cart-overlay-open");
      }
   }
   function close_panel() {
      panel.classList.remove("wt-mini-cart-panel-open");
      panel.setAttribute("aria-hidden", "true");
      panel.setAttribute("data-open", "false");
      trigger.setAttribute("aria-expanded", "false");
      document.body.classList.remove("wt-mini-cart-overlay-open");
      document.body.classList.remove("wt-mini-cart-dropdown-open");
      locked_by_click = false;
   }

   function schedule_close_from_hover() {
      if (!is_dropdown || locked_by_click) return;
      if (hover_close_timer) clearTimeout(hover_close_timer);
      hover_close_timer = setTimeout(function () {
         if (!locked_by_click) close_panel();
      }, 140);
   }

   function clear_hover_close() {
      if (hover_close_timer) {
         clearTimeout(hover_close_timer);
         hover_close_timer = null;
      }
   }

   trigger.addEventListener("click", function (e) {
      if (!is_dropdown) {
         if (panel.getAttribute("data-open") === "true") close_panel();
         else open_panel();
         return;
      }

      e.preventDefault();
      clear_hover_close();
      if (panel.getAttribute("data-open") === "true" && locked_by_click) {
         close_panel();
      } else {
         locked_by_click = true;
         open_panel();
      }
   });

   if (is_dropdown) {
      trigger.addEventListener("mouseenter", function () {
         clear_hover_close();
         if (!locked_by_click) open_panel();
      });
      trigger.addEventListener("mouseleave", schedule_close_from_hover);
      panel.addEventListener("mouseenter", clear_hover_close);
      panel.addEventListener("mouseleave", schedule_close_from_hover);
   }
   if (close_btn) {
      close_btn.addEventListener("click", close_panel);
   }
   panel.addEventListener("click", function (e) {
      if (!is_dropdown && e.target === panel) close_panel();
   });
   /* Close when clicking outside (on the overlay / backdrop) */
   document.addEventListener("click", function (e) {
      if (
         panel.getAttribute("data-open") === "true" &&
         !panel.contains(e.target) &&
         !trigger.contains(e.target)
      ) {
         close_panel();
      }
   });
   document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && panel.getAttribute("data-open") === "true")
         close_panel();
   });

});

//autoupdate cart number + mini cart fragments
jQuery(document).ready(function ($) {
   if (typeof wc_add_to_cart_params === "undefined") return;

   // Function to refresh cart fragments (updates count + mini cart content)
   function refresh_cart_fragments() {
      $.ajax({
         url: wc_add_to_cart_params.wc_ajax_url
            .toString()
            .replace("%%endpoint%%", "get_refreshed_fragments"),
         type: "POST",
         data: { action: "woocommerce_get_refreshed_fragments" },
         success: function (response) {
            if (response && response.fragments) {
               $.each(response.fragments, function (key, html) {
                  $(key).replaceWith(html);
               });
               // Re-run cart page UI (Apply coupon / Update cart in cart_totals) after DOM was replaced
               $(document.body).trigger("wc_fragments_refreshed");
            }
         },
      });
   }

   // Legacy: update .menu-basket-items-total if present
   function update_cart_count() {
      $.ajax({
         url: wc_add_to_cart_params.ajax_url,
         type: "POST",
         data: { action: "woocommerce_get_refreshed_fragments" },
         success: function (response) {
            if (response && response.fragments) {
               $.each(response.fragments, function (key, html) {
                  $(key).replaceWith(html);
               });
               $(document.body).trigger("wc_fragments_refreshed");
               if (response.fragments["span.menu-basket-items-total"]) {
                  var new_cart_count = $(
                     response.fragments["span.menu-basket-items-total"],
                  ).text();
                  $(".menu-basket-items-total").text(new_cart_count);
               }
            }
         },
      });
   }

   $(document.body).on("added_to_cart", function () {
      refresh_cart_fragments();
   });
   $(document.body).on("removed_from_cart", function () {
      refresh_cart_fragments();
   });
   $(document.body).on("updated_cart_totals", function () {
      refresh_cart_fragments();
   });
   $(document.body).on("wc_fragment_refresh", function () {
      refresh_cart_fragments();
   });

   // Remove item in mini cart panel (AJAX)
   $(document).on(
      "click",
      ".wt-mini-cart-content .remove_from_cart_button",
      function (e) {
         e.preventDefault();
         var $this = $(this);
         var cart_item_key =
            $this.data("cart_item_key") ||
            ($this.attr("href").match(/remove_item=([^&]+)/) || [])[1];
         if (!cart_item_key) return;
         $.post(
            wc_add_to_cart_params.wc_ajax_url
               .toString()
               .replace("%%endpoint%%", "remove_item"),
            { cart_item_key: cart_item_key },
            function () {
               $(document.body).trigger("wc_fragment_refresh");
            },
         );
      },
   );

   // Legacy: product-remove in other cart views
   $(document).on("click", ".product-remove .remove", function (e) {
      e.preventDefault();
      var $this = $(this);
      var cart_item_key = ($this.attr("href") || "").split("remove_item=")[1];
      if (cart_item_key) cart_item_key = cart_item_key.split("&")[0];
      if (!cart_item_key) return;
      $.post(
         wc_add_to_cart_params.wc_ajax_url
            .toString()
            .replace("%%endpoint%%", "remove_item"),
         { cart_item_key: cart_item_key },
         function () {
            $this.closest("tr.cart_item").remove();
            $(document.body).trigger("wc_fragment_refresh");
         },
      );
   });
});

/* Login / Register modals – submit via AJAX, show error under button (do not close modal) */
jQuery(document).ready(function ($) {
   var $login_form = $("#wt-shop-login .woocommerce-form-login");
   var $register_form = $("#wt-shop-register .woocommerce-form-register");
   var $login_errors = $("#wt-shop-login-errors");
   var $register_errors = $("#wt-shop-register-errors");

   function show_modal_error($el, message) {
      $el.addClass("wt-shop-modal-errors-visible")
         .html(message)
         .attr("aria-live", "assertive");
   }
   function clear_modal_error($el) {
      $el.removeClass("wt-shop-modal-errors-visible")
         .empty()
         .attr("aria-live", "polite");
   }

   $login_form.on("submit", function (e) {
      e.preventDefault();
      var $form = $(this);
      clear_modal_error($login_errors);
      $.ajax({
         url: typeof wt_ajax !== "undefined" ? wt_ajax.ajaxurl : "",
         type: "POST",
         data: $form.serialize() + "&action=wt_shop_modal_login",
         success: function (response) {
            if (
               response &&
               response.success &&
               response.data &&
               response.data.redirect
            ) {
               window.location.href = response.data.redirect;
            } else {
               show_modal_error(
                  $login_errors,
                  response && response.data && response.data.message
                     ? response.data.message
                     : "Login failed.",
               );
            }
         },
         error: function (xhr, status, err) {
            var msg =
               xhr.responseJSON &&
               xhr.responseJSON.data &&
               xhr.responseJSON.data.message
                  ? xhr.responseJSON.data.message
                  : xhr.responseText || "Login failed. Please try again.";
            show_modal_error($login_errors, msg);
         },
      });
   });

   function get_register_error_message(response_or_xhr, is_xhr) {
      var data = is_xhr
         ? response_or_xhr.responseJSON ||
           (function () {
              try {
                 return response_or_xhr.responseText
                    ? JSON.parse(response_or_xhr.responseText)
                    : null;
              } catch (e) {
                 return null;
              }
           })()
         : response_or_xhr;
      if (data && data.data && typeof data.data.message === "string")
         return data.data.message;
      if (is_xhr && response_or_xhr.responseText)
         return response_or_xhr.responseText;
      return null;
   }

   $register_form.on("submit", function (e) {
      e.preventDefault();
      var $form = $(this);
      clear_modal_error($register_errors);
      $.ajax({
         url: typeof wt_ajax !== "undefined" ? wt_ajax.ajaxurl : "",
         type: "POST",
         dataType: "json",
         data: $form.serialize() + "&action=wt_shop_modal_register",
         success: function (response) {
            if (
               response &&
               response.success === true &&
               response.data &&
               response.data.redirect
            ) {
               window.location.href = response.data.redirect;
            } else {
               var msg = get_register_error_message(response, false);
               show_modal_error($register_errors, msg || "Registration failed.");
            }
         },
         error: function (xhr, status, err) {
            var msg = get_register_error_message(xhr, true);
            show_modal_error(
               $register_errors,
               msg || "Registration failed. Please try again.",
            );
         },
      });
   });

   $("#wt-shop-login, #wt-shop-register").on("shown.bs.modal", function () {
      var id = $(this).attr("id");
      if (id === "wt-shop-login") clear_modal_error($login_errors);
      else if (id === "wt-shop-register") {
         clear_modal_error($register_errors);
         if (typeof grecaptcha !== "undefined" && typeof grecaptcha.reset === "function") grecaptcha.reset();
      }
   });

   /* Password strength meter (register modal) */
   var $pwd_input = $("#wt-shop-register #reg_password");
   var $strength_fill = $("#wt-shop-password-strength-fill");
   var $strength_text = $("#wt-shop-password-strength-text");
   var strength_labels = {
      0: "",
      1: "Weak",
      2: "Fair",
      3: "Good",
      4: "Strong",
   };

   function get_password_strength(pwd) {
      if (!pwd || !pwd.length) return 0;
      var score = 0;
      if (pwd.length >= 8) score++;
      if (pwd.length >= 12) score++;
      if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
      if (/\d/.test(pwd)) score++;
      if (/[^a-zA-Z0-9]/.test(pwd)) score++;
      if (score <= 1) return 1;
      if (score <= 2) return 2;
      if (score <= 4) return 3;
      return 4;
   }

   function update_password_strength() {
      var pwd = $pwd_input.val();
      var level = get_password_strength(pwd);
      var label = strength_labels[level];
      var level_class =
         level === 1
            ? "weak"
            : level === 2
              ? "fair"
              : level === 3
                ? "good"
                : level === 4
                  ? "strong"
                  : "";
      var $container = $("#wt-shop-password-strength");
      $container.removeClass(
         "wt-shop-password-strength-weak wt-shop-password-strength-fair wt-shop-password-strength-good wt-shop-password-strength-strong",
      );
      $strength_fill.removeClass(
         "wt-shop-password-strength-fill-weak wt-shop-password-strength-fill-fair wt-shop-password-strength-fill-good wt-shop-password-strength-fill-strong",
      );
      if (level > 0) {
         $strength_fill.addClass(
            "wt-shop-password-strength-fill-" + level_class,
         );
         $strength_fill.css("width", (level / 4) * 100 + "%");
         $container.addClass("wt-shop-password-strength-" + level_class);
      } else {
         $strength_fill.css("width", "0%");
      }
      $strength_text.text(label);
   }

   $pwd_input.on("input", update_password_strength);
   $("#wt-shop-register").on("shown.bs.modal", function () {
      $strength_fill
         .css("width", "0%")
         .removeClass(
            "wt-shop-password-strength-fill-weak wt-shop-password-strength-fill-fair wt-shop-password-strength-fill-good wt-shop-password-strength-fill-strong",
         );
      $("#wt-shop-password-strength").removeClass(
         "wt-shop-password-strength-weak wt-shop-password-strength-fair wt-shop-password-strength-good wt-shop-password-strength-strong",
      );
      $strength_text.text("");
   });
});

// Show/hide password button – works in registration, login, edit account
(function () {
   function remove_duplicate_password_buttons() {
      document.querySelectorAll(".password-input").forEach(function (wrap) {
         var buttons = [].slice.call(
            wrap.querySelectorAll(".show-password-input, .display-password"),
         );
         var kept = false;
         buttons.forEach(function (btn) {
            if (btn.getAttribute("type") !== "button") {
               btn.remove();
            } else if (kept) {
               btn.remove();
            } else {
               kept = true;
            }
         });
      });
   }

   function init_password_toggle() {
      remove_duplicate_password_buttons();
      document.body.addEventListener(
         "click",
         function (e) {
            var btn =
               e.target.closest(".show-password-input") ||
               e.target.closest(".display-password");
            if (!btn || !btn.closest(".password-input")) return;
            e.preventDefault();
            e.stopPropagation();
            if (btn.getAttribute("type") !== "button") return;
            var wrap = btn.closest(".password-input");
            var input = wrap.querySelector(
               'input[type="password"], input[type="text"]',
            );
            if (!input) return;
            var is_password = input.type === "password";
            input.type = is_password ? "text" : "password";
            btn.classList.remove("show-password-input", "display-password");
            btn.classList.add(
               is_password ? "display-password" : "show-password-input",
            );
            btn.setAttribute(
               "aria-label",
               is_password ? "Hide password" : "Show password",
            );
         },
         true,
      );
   }
   if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", init_password_toggle);

   } else {
      init_password_toggle();
   }

   // Re-run cleanup when login/register modal is shown (avoids duplicate if another script adds a button)
   document
      .querySelectorAll("#wt-shop-login, #wt-shop-register")
      .forEach(function (modal) {
         modal.addEventListener(
            "shown.bs.modal",
            remove_duplicate_password_buttons,
         );
      });
})();

/* ===== Shop sidebar – toggle subcategories ===== */
document.addEventListener("DOMContentLoaded", function () {
   document.querySelectorAll(".wt-shop-cat-toggle").forEach(function (btn) {
      btn.addEventListener("click", function () {
         var li = btn.closest(".wt-shop-cat-item");
         if (li) {
            li.classList.toggle("wt-shop-cat-open");
         }
      });
   });
});

/* ===== Shop sidebar – mobile collapse toggle ===== */
document.addEventListener("DOMContentLoaded", function () {
   document.querySelectorAll(".wt-shop-sidebar-mobile-toggle").forEach(function (btn) {
      btn.addEventListener("click", function () {
         var target_id = btn.getAttribute("aria-controls");
         var panel = target_id ? document.getElementById(target_id) : null;
         if (!panel) return;
         var is_open = btn.getAttribute("aria-expanded") === "true";
         btn.setAttribute("aria-expanded", is_open ? "false" : "true");
         panel.classList.toggle("wt-shop-sidebar-open", !is_open);
      });
   });
});

/* ===== Shop sidebar – dual-range price slider ===== */
document.addEventListener("DOMContentLoaded", function () {
   var track = document.querySelector(".wt-shop-price-slider-track");
   if (!track) return;

   var min_range = track.querySelector(".wt-shop-price-min-range");
   var max_range = track.querySelector(".wt-shop-price-max-range");
   var fill      = track.querySelector(".wt-shop-price-slider-fill");
   var form      = track.closest(".wt-shop-filters-form");
   var min_input = form ? form.querySelector(".wt-shop-price-min-input") : null;
   var max_input = form ? form.querySelector(".wt-shop-price-max-input") : null;

   if (!min_range || !max_range || !fill) return;

   var global_min = parseFloat(min_range.min);
   var global_max = parseFloat(min_range.max);

   function update_fill() {
      var min_val = parseFloat(min_range.value);
      var max_val = parseFloat(max_range.value);
      var range   = global_max - global_min;
      if (range <= 0) return;
      var left_pct  = ((min_val - global_min) / range) * 100;
      var right_pct = ((max_val - global_min) / range) * 100;
      fill.style.left  = left_pct + "%";
      fill.style.width  = (right_pct - left_pct) + "%";
   }

   min_range.addEventListener("input", function () {
      if (parseFloat(min_range.value) > parseFloat(max_range.value)) {
         min_range.value = max_range.value;
      }
      if (min_input) min_input.value = min_range.value;
      update_fill();
   });

   max_range.addEventListener("input", function () {
      if (parseFloat(max_range.value) < parseFloat(min_range.value)) {
         max_range.value = min_range.value;
      }
      if (max_input) max_input.value = max_range.value;
      update_fill();
   });

   if (min_input) {
      min_input.addEventListener("change", function () {
         var v = parseFloat(min_input.value);
         if (isNaN(v) || v < global_min) v = global_min;
         if (v > parseFloat(max_range.value)) v = parseFloat(max_range.value);
         min_input.value = v;
         min_range.value = v;
         update_fill();
      });
   }

   if (max_input) {
      max_input.addEventListener("change", function () {
         var v = parseFloat(max_input.value);
         if (isNaN(v) || v > global_max) v = global_max;
         if (v < parseFloat(min_range.value)) v = parseFloat(min_range.value);
         max_input.value = v;
         max_range.value = v;
         update_fill();
      });
   }

   // Init fill position.
   update_fill();
});

/* ===== Shop sidebar – rating filter highlight ===== */
document.addEventListener("DOMContentLoaded", function () {
   var items = document.querySelectorAll(".wt-shop-filter-rating-item");
   if (!items.length) return;

   items.forEach(function (item) {
      item.addEventListener("click", function () {
         /* Remove active from all */
         items.forEach(function (el) {
            el.classList.remove("wt-shop-filter-rating-active");
         });
         /* Add active to clicked */
         item.classList.add("wt-shop-filter-rating-active");
      });
   });
});

/* Checkout: hide default error list, mark invalid fields with red border and scroll to first error */
(function () {
   function wt_checkout_highlight_errors() {
      var form = document.querySelector("form.woocommerce-checkout");
      if (!form) return;
      var invalid = form.querySelectorAll(".form-row.woocommerce-invalid");
      if (invalid.length === 0) return;
      invalid.forEach(function (row) {
         row.classList.add("wt-checkout-field-error");
      });
      invalid[0].scrollIntoView({ behavior: "smooth", block: "center" });
   }

   document.addEventListener("DOMContentLoaded", function () {
      if (typeof jQuery === "undefined") return;
      jQuery(document.body).on("checkout_error", function () {
         setTimeout(wt_checkout_highlight_errors, 100);
      });
      jQuery(document.body).on("updated_checkout", function () {
         wt_checkout_highlight_errors();
      });
   });
})();

/* ──────────────────────────────────────────────
   Products Swiper – related / upsell / cross-sell
   ────────────────────────────────────────────── */
(function () {
   "use strict";
   document.addEventListener("DOMContentLoaded", function () {
      if (typeof Swiper === "undefined") return;

      var wraps = document.querySelectorAll(".wt-shop-products-swiper-wrap");
      wraps.forEach(function (wrap) {
         var swiper_el = wrap.querySelector(".wt-shop-products-swiper");
         var btn_prev  = wrap.querySelector(".wt-shop-swiper-btn-prev");
         var btn_next  = wrap.querySelector(".wt-shop-swiper-btn-next");

         if (!swiper_el) return;

         var swiper_instance = new Swiper(swiper_el, {
            slidesPerView: 1,
            spaceBetween:  16,
            loop:          false,
            grabCursor:    true,
            speed:         500,
            observer:      true,
            observeParents: true,

            breakpoints: {
               576: {
                  slidesPerView: 2,
                  spaceBetween:  20
               },
               992: {
                  slidesPerView: 4,
                  spaceBetween:  24
               }
            }
         });

         function toggle_navigation_and_class() {

            var slides_per_view = swiper_instance.params.slidesPerView;
            var total_slides    = swiper_instance.slides.length;

            var products_section = wrap.closest('.up-sells, .upsells, .related, .cross-sells');

            if (total_slides <= slides_per_view) {

               /* Hide buttons */
               if (btn_prev) btn_prev.classList.add('d-none');
               if (btn_next) btn_next.classList.add('d-none');

               /* Add helper class */
               if (products_section) {
                  products_section.classList.add('less-then-four');
               }

            } else {


               if (btn_prev) btn_prev.classList.remove('d-none');
               if (btn_next) btn_next.classList.remove('d-none');

               if (products_section) {
                  products_section.classList.remove('less-then-four');
               }
            }
         }

         /* Run on init */
         toggle_navigation_and_class();

         /* Run on resize */
         swiper_instance.on('resize', function () {
            toggle_navigation_and_class();
         });

         /* Wire up custom prev / next buttons */
         if (btn_prev) {
            btn_prev.addEventListener("click", function () {
               swiper_instance.slidePrev();
            });
         }
         if (btn_next) {
            btn_next.addEventListener("click", function () {
               swiper_instance.slideNext();
            });
         }
      });
   });
})();
