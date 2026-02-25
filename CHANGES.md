# Changelog – Ycona theme (WooCommerce & single product)

Dokumenti përshkruan **të gjitha** ndryshimet e bëra në temën Ycona nga fillimi i sesionit të punës (rreth 5–6 orë më parë) deri më tani: **përkthimet gjermanisht (de_DE)**, Theme Options dhe CPT të përkthyer, checkout, mini-cart, stepper, validim, select-e, faqja e vetme e produktit, badge sale, PWA, related/upsells/cross-sells me Swiper, filtër rating me theks vizual, reviews si sidebar, mbyllje mini-cart me klik jashtë, dhe rregullime të tjera.

---

## Konventa e emërtimit (Cursor / projekt)

- **PHP dhe JavaScript**: `snake_case`.
- **CSS dhe HTML ID**: `kebab-case`.
- **Mos ndrysho kurrë** skedarët `*.min.js` dhe `*.min.css` – puno vetëm me `*.long.js`, `*.long.css` dhe skedarët e personalizuar.

---

## 1. Redesign i faqes së Checkout (Kasse)

- Layout **dy kolonash** si te Warenkorb (cart): formulari majtas, përmbledhja e porosisë djathtas.
- Seksione në formë **kartash** (card-like).
- **Order summary** ngjitur (sticky) në desktop.
- Stilet në `woocommerce.long.css`, strukturë/override në template-et e checkout-it.

---

## 2. Mini-Cart: navigim Cart ↔ Checkout

- Butoni i mini-cart-it në header të çojë te **Warenkorb** kur përdoruesi është në faqen e checkout, dhe te **Kasse** (checkout) kur është në cart ose shop.
- Logjika e ndihmës (helper) e implementuar në **`woocommerce/hooks/mini-cart.php`** (p.sh. `wc_get_cart_url()`, `wc_get_checkout_url()` sipas kontekstit).

---

## 3. Stepper / Breadcrumb te Cart, Checkout dhe Thank You

- Breadcrumb vizual: **Warenkorb → Kasse → Bestellung abgeschlossen**.
- Shfaqet në faqet: cart, checkout, thank you.
- Hapat: aktual (ngjyrë primary), i përfunduar (i zi), ardhshëm (gri).
- Logjika në **`woocommerce/hooks/support-woocommerce.php`**, stilet në **`woocommerce.long.css`**.

---

## 4. Validimi në Checkout (UX)

- Lista e default e njoftimeve të WooCommerce për gabimet fshihet ose minimizohet.
- Fushat me gabim **theksohen me border të kuq**.
- **Scroll te fusha e parë me gabim** (CSS + JavaScript) për ta sjellur përdoruesin te gabimi.
- Kodi në **`assets/js/woocommerce.long.js`** (evente `checkout_error`, `updated_checkout`; funksion p.sh. `wt_checkout_highlight_errors`).

---

## 5. Stilizim i të gjitha `<select>` (WooCommerce)

- Të gjitha `<select>` të WooCommerce duken si input-et e rregullt: i njëjti border, padding, radius.
- **Select2/SelectWoo** çaktivizohen në faqet e caktuara të WooCommerce (checkout, account, etj.) që të përdoret pamja native/custom.
- Shigjeta e dropdown-it: fillimisht si **SVG** në `background-image`, pastaj zëvendësuar me **Bootstrap Icons** – **`bi-chevron-down`** në të zezë.
- Arrow aplikohet edhe për select-et brenda `.woocommerce-input-wrapper` dhe për fushat e adresës; përdoren selektorë të qartë dhe `:has(select)` që ikona të mos dalë te input-et e thjeshtë.
- Për **variation selects** (tabela `.woocommerce table.variations td.value select`): pamja native heqet, dimensionet, border dhe fokus; shigjeta `bi-chevron-down` si `::after` te `td.value` (pozicionim me `calc`).
- Stilet në **`woocommerce.long.css`**.

---

## 6. SelectWoo / Select2 stub

- Pasi Select2/SelectWoo çaktivizohen, plugina (p.sh. “Woo Checkout Field Editor Pro”) mund të thërrasin `$(...).selectWoo()` ose `$(...).select2()` dhe të japin gabim.
- U shtua një **stub script** (handle `selectWoo`) që definon `jQuery.fn.selectWoo` dhe `jQuery.fn.select2` si funksione që kthejnë `this`, që të mos thyhen pluginat.
- Regjistrim/enqueue në **`support-woocommerce.php`**.

---

## 7. Faqja e vetme e produktit (Single Product) – redesign

- **Tabs** e WooCommerce rikthyer si parazgjedhje: Beschreibung (Description), Zusatzinformationen (Additional Information), Bewertungen (Reviews).
- **Galeri**: navigim next/previous, zoom, pamje moderne; thumbnails me hover.
- Butoni zoom si buton rrethor me **Bootstrap Icons** `bi-zoom-in` (codepoint `\f62c`).
- Pjesa e poshtme (related / upsell) në fillim me grid, më vonë me Swiper (shiko më poshtë).
- Template **`woocommerce/content-single-product.php`**: `do_action('woocommerce_before_single_product_summary')` përpara summary, hequr elemente custom (p.sh. “Money Back 30 Days”, shigjetat scroll) që përshtaten me default WooCommerce.
- Stilet në **`woocommerce.long.css`** (`.ycona-single-product`, gallery, tabs, panels).

---

## 8. “Ähnliche Produkte” dhe Upsells – design si te Shop

- Related products dhe Upsells në single product të kenë **të njëjtin design të kartave** si në faqen e shop-it: grid (më vonë Swiper), karta me imazh, titull, çmim, butona, sale badge, star rating.
- Stilet për `.ycona-single-product-long .related.products` dhe `.upsells.products` (dhe më vonë `.cross-sells.products`) në **`woocommerce.long.css`**, të njëjta klasa si te shop (`.woocommerce-loop-product__link`, `.ycona-shop-product-desc`, etj.).

---

## 9. Badge për përqindje zbritje (Sale percentage)

- Badge dinamik (p.sh. **-17%**) pranë badge “Angebot” (Sale) në karta produktesh.
- Llogaritja: `(regular - sale) / regular * 100`; për **variable** merret përqindja maksimale ndër variacionet.
- Filter **`woocommerce_sale_flash`** në **`support-woocommerce.php`** (funksioni `ycona_sale_percentage_badge`); HTML: `<span class="woocommerce-sale-percent">-XX%</span>`.
- Ngjyrë **secondary** e temës; pozicionim: në shop/related/upsell djathtas lart; në galerinë e single product **majtas**, poshtë badge “Angebot” (që zoom të mbetet djathtas).
- Stilet për `.woocommerce-sale-percent` në **`woocommerce.long.css`**; për single product edhe `.ycona-single-product-top > .onsale` dhe `> .woocommerce-sale-percent` me `position: absolute`, `z-index: 10`, që badge-t të mbivendosen mbi galeri.

---

## 10. Sale badge në single product – dukshmëri

- Badge “Angebot” dhe përqindja nuk shfaqeshin mbi galeri sepse dalnin si sibling të galerisë, jo brenda saj.
- Zgjidhje: **`.ycona-single-product-top`** me `position: relative`; **`.ycona-single-product-top > .onsale`** dhe **`> .woocommerce-sale-percent`** me `position: absolute`, `z-index: 10`, koordinata (top/left) që të jenë majtas, njëra poshtë tjetrës.
- Në **`woocommerce.long.css`**.

---

## 11. WooCommerce JavaScript i veçantë (woocommerce.long.js)

- I gjithë JS specifik për WooCommerce u **nxorr** nga **`assets/js/functions.long.js`** dhe u vendos në **`assets/js/woocommerce.long.js`**.
- Përfshirë: kërkim header (desktop/mobile), panel mini-cart, cart fragments, modale login/register, password strength meter, show/hide password, shop sidebar toggles, price slider, **checkout error highlighting** (scroll + border të kuq).
- Në **`functions.php`** u shtua enqueue për `ycona-woocommerce-js` (skedar `woocommerce.long.js`) kur WooCommerce është aktiv; varësi: `jquery`, `js-main` (dhe më vonë `wt_swiper_js` për Swiper).

---

## 12. PWA – ikona Android (manifest.json)

- Gabim 404 për **`android-icon-192x192.png`** dhe paralajmërim manifest: ikonat ishin me path root-relative (`/android-icon-...`) dhe kërkoheshin në rrënjën e site-it.
- Në **`assets/img/favicon/manifest.json`** path-et e ikonave u ndërruan në **relative** (pa `/` në fillim), që të zgjidhen drejt nga dosja e favicon-it.

---

## 13. Related, Upsells dhe Cross-sells me Swiper

- Të tre seksionet në single product bëhen **karusel Swiper** në vend të grid-it.
- **Layout**: 4 kolona desktop (≥992px), 2 tablet (≥576px), 1 mobile.
- **Butona** “para / pas” me **Bootstrap Icons** `bi-chevron-left` dhe `bi-chevron-right`, në stilin e temës (rreth, border, hover primary).
- **PHP**: funksioni `ycona_convert_product_list_to_swiper()` në **`support-woocommerce.php`** konverton `<ul class="products"><li>…</li></ul>` në markup Swiper dhe shton butonat; hooks për related dhe upsells (output buffering te template-et). **Cross-sells** shfaqen edhe në single product me funksionin `ycona_single_product_cross_sells()` (priority 25), që nxjerr cross-sell nga produkti aktual dhe e kalon HTML-in nëpër të njëjtin konvertues.
- **JS**: inicializim Swiper në **`woocommerce.long.js`** për çdo `.ycona-products-swiper-wrap`; `observer` / `observeParents` për përditësim.
- **CSS**: **`woocommerce.long.css`** – wrap dhe swiper me `width: 100%`; stilet e kartave të njëjta si te shop; **pa** `width: auto !important` te slide-et që Swiper të vendosë gjerësinë për 4/2/1.
- **functions.php**: enqueue Swiper CSS/JS; `woocommerce.long.js` varet nga `wt_swiper_js`.

---

## 14. Titullat e ndara: Upsells vs Cross-sells

- **Upsells**: titulli “Das passt dazu” me filter **`woocommerce_product_upsells_products_heading`** (`ycona_upsells_heading()`).
- **Cross-sells**: titulli “Das könnte dir auch gefallen” me funksionin `ycona_cross_sells_heading()` te `ycona_single_product_cross_sells()`.

---

## 15. Rregullim layout Swiper (4 në rresht desktop)

- Problemi: në desktop nuk dilnin 4 produkte në rresht.
- Shkak: `width: auto !important` te `.swiper-slide.product` e mbushte gjerësinë e llogaritur nga Swiper; container pa `width: 100%`.
- Ndryshime: heqja e `width: auto !important`; `width: 100%` për seksionet related/upsells/cross-sells dhe për `.ycona-products-swiper-wrap` / `.ycona-products-swiper`; `observer` / `observeParents` në Swiper.

---

## 16. Përshkrimi i shkurtër për variacionet (short description)

- Variacionet (p.sh. “Product Test 3 (Kopie) - M, 2”) nuk kishin short description në loop sepse WooCommerce e ruan vetëm te produkti prind.
- Në **`woocommerce/content-product.php`**: nëse `get_short_description()` është bosh dhe produkti është `variation`, merret short description nga **produkti prind** (`wc_get_product( $product->get_parent_id() )`) dhe përdoret i njëjti përpunim (trim 100 karaktere) dhe output në `.ycona-shop-product-desc`.

---

## Përkthimet dhe lokalizimi (germanisht de_DE)

E gjithë pjesa e mëposhtme u diskutua dhe u implementua në sesion: .po/.mo, Theme Options, CPT backend, forma “Sie”, dhe skripti për kompilim .mo.

### 16a. Ngarkimi i text domain dhe skedarët de_DE

- **`functions.php`**: funksioni `ycona_load_textdomain()` – `load_theme_textdomain('ycona', get_template_directory() . '/languages')` te `after_setup_theme`, që WordPress të gjejë përkthimet.
- Krijimi i **`languages/de_DE.po`** dhe **`languages/de_DE.mo`** me të gjitha vargjet e nxjerra nga tema (text domain `ycona`).
- Nxjerrja e vargjeve të përkthyeshme: `__()`, `_e()`, `esc_html__()`, `esc_html_e()`, `esc_attr__()`, `esc_attr_e()` me domen `'ycona'`.

### 16b. Theme Options – të gjitha vargjet e përkthyer

- **`theme-options.php`**: të gjitha vargjet e dukshme u mbyllën me funksione përkthimi: sidebar (General, Design Settings, Social Media, Payments, Footer, Settings, “Theme Options”), tituj panele, përshkrime, emra ngjyrash, butona, placeholder-e, mesazhe në footer (“Don’t forget to save…”, “Preview Changes”), përshkrimet e upload-it të logo-s në footer.
- Shtuar dhjetëra hyrje në **`de_DE.po`** për këto vargje (përfundimisht 394 hyrje në .po/.mo).

### 16c. Gjermanisht zyrtar (forma “Sie”)

- Në **`de_DE.po`** të gjitha formulimet informale (“du”) u zëvendësuan me formën zyrtare “Sie” (p.sh. “Passe … an” → “Passen Sie … an”, “deiner” → “Ihrer”) – rreth 30 ndryshime, që faqja të duket profesionale.

### 16d. CPT backend – etiketa të përkthyeshme

- **`wt-cpt/accordion.php`**: “Accordion #%d” dhe “Accordion” në PHP dhe në template-in JS për shtimin e akordeonëve të rinj.
- **`wt-cpt/cards.php`**: “Cards”, “Card #%d”, “Card” si titull dhe për kartat e reja.
- **`wt-cpt/testimonials.php`**: “Testimonials”, “Testimonial #%d”, “Testimonial”, plus opsionet e dropdown-it (“Tick Green”, “Tick Orange”, “Tick Blue”) – të gjitha me `__()`, `sprintf()`, ose `esc_html__()` siç duhet për PHP dhe për output në JS (template literals me prefix të përkthyer).
- Hyrjet përkatëse u shtuan në **`de_DE.po`** (p.sh. “Akkordeon #%d”, “Karte #%d”, “Kundenstimme #%d”, “Häkchen Grün”, etj.).

### 16e. Kompilimi i .mo (compile-mo.php)

- Në mjedise ku **`msgfmt`** (GNU gettext) nuk është i disponueshëm (p.sh. Windows pa gettext në PATH), u krijuar **`languages/compile-mo.php`** – skript PHP që lexon `.po`, e parse-on dhe shkruan `.mo` në formatin binar, që WordPress ta ngarkojë.
- Skedari mbahet si **kopje rezervë** për herën tjetër që përditësohen përkthimet (nuk fshihet).

---

## Skedarët e prekur (përmbledhje)

| Skedar | Përmbledhje ndryshimesh |
|--------|-------------------------|
| `wp-content/themes/ycona/functions.php` | Enqueue WooCommerce CSS/JS, Swiper, **load_theme_textdomain** për `ycona` |
| `wp-content/themes/ycona/theme-options.php` | Të gjitha vargjet e Theme Options me `_e()`, `esc_html_e()`, `esc_attr_e()` për de_DE |
| `wp-content/themes/ycona/wt-cpt/accordion.php` | Etiketa “Accordion #%d” / “Accordion” të përkthyeshme (PHP + JS) |
| `wp-content/themes/ycona/wt-cpt/cards.php` | “Cards”, “Card #%d”, “Card” të përkthyeshme |
| `wp-content/themes/ycona/wt-cpt/testimonials.php` | “Testimonials”, “Testimonial #%d”, opsione dropdown (Tick Green/Orange/Blue) të përkthyeshme |
| `wp-content/themes/ycona/languages/de_DE.po` | Të gjitha vargjet e temës + përkthime gjermanisht (Sie), ~394 hyrje |
| `wp-content/themes/ycona/languages/de_DE.mo` | Kompilim nga .po (WordPress e ngarkon) |
| `wp-content/themes/ycona/languages/compile-mo.php` | Skript PHP për .po → .mo kur msgfmt nuk është i disponueshëm |
| `wp-content/themes/ycona/woocommerce/hooks/support-woocommerce.php` | Stepper, sale percentage badge, select stub, Swiper conversion, cross-sells, titullat upsells/cross-sells |
| `wp-content/themes/ycona/woocommerce/hooks/mini-cart.php` | Logjika e butonit cart/checkout |
| `wp-content/themes/ycona/woocommerce/content-single-product.php` | Layout single product, gallery hook, heqje elementësh custom |
| `wp-content/themes/ycona/woocommerce/content-product.php` | Short description nga prindi për variacionet |
| `wp-content/themes/ycona/assets/js/functions.long.js` | Heqje e kodit WooCommerce (kalim në woocommerce.long.js) |
| `wp-content/themes/ycona/assets/js/woocommerce.long.js` | Checkout validation, Swiper init, mini-cart (mbyllje me klik jashtë), **rating filter highlight**, search, login/register, etj. |
| `wp-content/themes/ycona/assets/css/woocommerce.long.css` | Checkout, stepper, select, single product, gallery, tabs, related/upsells/cross-sells, sale badges, Swiper, **reviews si sidebar**, **filtri Bewertung (ycona-filter-rating-active)** |
| `wp-content/themes/ycona/assets/css/main.long.css` | Mega menu (X brenda panelit), 404, header, etj. |
| `wp-content/themes/ycona/assets/img/favicon/manifest.json` | Path-et relative për ikonat PWA |
| `wp-content/themes/ycona/404.php` | Faqja 404 – layout, titull, butona, “You may also like” |
| `wp-content/themes/ycona/woocommerce/checkout/thankyou.php` | Faqja Thank You – konfirmim porosie (tekst nga Theme Options) |
| `wp-content/themes/ycona/template-parts/mega-menu.php` | Mega menu panel, butoni X brenda panelit |

---

## Vlerësim kohor (zhvillues me përvojë)

Kohë e nevojshme për të implementuar të gjitha ndryshimet nga e para (vetëm punë implementimi, pa përfshirë testime të plota dhe rishikime):

| Pjesa | Orë (përafërsisht) |
|-------|---------------------|
| Checkout redesign (2 kolona, kartat, sticky summary) | 3–5 |
| Mini-cart: Cart ↔ Checkout | 1–2 |
| Stepper (Warenkorb → Kasse → Bestellung) | 1.5–2 |
| Checkout validation (border, scroll, fshehje notices) | 2–3 |
| Select styling (pamje, çaktivizim Select2, shigjeta Bootstrap) | 3–4 |
| SelectWoo stub | ~0.5 |
| Single product (tabs, galeri, zoom, design) | 4–6 |
| Related/Upsells design si shop | 1.5–2 |
| Sale percentage badge (llogaritje + CSS) | 2–3 |
| Sale badge mbi galeri (pozicionim) | ~0.5–1 |
| Ndarja e WooCommerce JS (woocommerce.long.js) | 1.5–2 |
| PWA manifest (path ikonash) | ~0.25 |
| Swiper (related/upsells/cross-sells, 4/2/1, butona) | 4–5 |
| Titullat + layout 4 në rresht + short description variacione | ~1–2 |
| **Shop sidebar** (kategoritë + filtrat: layout, forma, query pre_get_posts, CSS, JS slider + rating) | 10.5–15 |
| **Përkthimet de_DE** (16a–16e): text domain, .po/.mo, Theme Options, Sie-form, CPT backend, compile-mo.php | ~4–7 orë |

**Total i përafërt (vetëm implementim, pa përkthimet):** ~36–53 orë.  
**Me përkthimet (16a–16e):** ~40–60 orë.  
Me testime, rregullime dhe edge cases: **~50–75 orë** (rreth **56–82 orë** për version production-ready me gjermanisht, duke përfshirë shop sidebar).

---

## Ku janë faqet 404 dhe Thank You

| Faqja | Skedari (rrugë e plotë) |
|-------|--------------------------|
| **404 – Page not found** | `wp-content/themes/ycona/404.php` |
| **Thank You (konfirmim porosie)** | `wp-content/themes/ycona/woocommerce/checkout/thankyou.php` |

- **404**: përmbajtja (numri 404, titull, nëntitull, butona “Zur Startseite” / “Zum Shop”) dhe stilet (`.ycona-404-wrap`, `.ycona-404-hero`, `.ycona-404-number`, etj.) janë në `404.php`; CSS për 404 dhe “You may also like” në **`woocommerce.long.css`** (`.ycona-404-wrap`, `.ycona-btn-404`, `.ycona-404-products`).
- **Thank You**: teksti vjen nga Theme Options (Thank You panel në **`theme-options.php`** – thank_you_heading, thank_you_subheading, thank_you_order_message, etj.); template-i WooCommerce është **`woocommerce/checkout/thankyou.php`** (përdor `ycona_theme_options` për tituj dhe butona). Stilet për thank you (`.ycona-thankyou-wrap`, `.ycona-thankyou-hero`, `.ycona-thankyou-order-card`, etj.) janë në **`woocommerce.long.css`**.

---

## Ku janë filtrat e sidebar-it (shop)

Filtrat e shop-it (Price, Availability, Offers, Rating, Tags) dhe lista e kategorive janë të ndërtuara në **`woocommerce/hooks/support-woocommerce.php`** dhe shfaqen nga **`woocommerce/archive-product.php`**.

| Pjesa | Skedari | Funksioni / vendi |
|-------|---------|-------------------|
| **Layout shop + sidebar** | `woocommerce/archive-product.php` | `.ycona-shop-wrap`, `.ycona-shop-layout`, `.ycona-shop-sidebar-wrap`; thirrje `ycona_shop_category_sidebar()` |
| **Kategoritë (Categories)** | `woocommerce/hooks/support-woocommerce.php` | `ycona_shop_category_sidebar()` – output `<aside id="ycona-sidebar-cats">` me `.ycona-cat-list`, link te kategoritë dhe nënkategoritë |
| **Filtrat (Price, Availability, Rating, Tags)** | `woocommerce/hooks/support-woocommerce.php` | Brenda `ycona_shop_category_sidebar()`: `<aside id="ycona-sidebar-filters">` me formë `.ycona-filters-form` – Price (dual range slider), Availability (in_stock), Offers (on_sale), Rating (rating_filter 5→1 + “All ratings”), Tags (product_tag[]), butona Apply Filters / Reset |
| **Logjika e filtrave (query)** | `woocommerce/hooks/support-woocommerce.php` | `ycona_shop_sidebar_filters_query()` – hook `pre_get_posts`; aplikon min_price, max_price, in_stock, on_sale, rating_filter, product_tag në `WP_Query` |
| **Stilet sidebar + filtra** | `assets/css/woocommerce.long.css` | `.ycona-shop-sidebar`, `.ycona-shop-sidebar-filters`, `.ycona-filter-section`, `.ycona-filter-rating-list`, `.ycona-filter-rating-active`, `.ycona-filter-tags`, `.ycona-price-slider-track`, etj. |
| **Slider çmimi + highlight rating** | `assets/js/woocommerce.long.js` | “Shop sidebar – dual-range price slider” (update fill, min/max inputs); “Shop sidebar – rating filter highlight” (click → shton/heq `ycona-filter-rating-active`) |

**Përmbledhje:** Sidebar-i i shop-it (kategoritë + filtrat) krijohet në **`support-woocommerce.php`** (funksioni `ycona_shop_category_sidebar()` dhe `ycona_shop_sidebar_filters_query()`). Faqja e shop-it **`archive-product.php`** e vendos në layout dhe e thërret këtë funksion; CSS në **`woocommerce.long.css`**, JS për çmim dhe rating në **`woocommerce.long.js`**.

---

## Varësi

- **Swiper**: `assets/css/swiper/swiper-bundle.min.css`, `assets/js/swiper/swiper-bundle.min.js`
- **Bootstrap Icons**: për chevron-down (select), chevron-left/right (Swiper, gallery), zoom-in (gallery)

---

---

## Ndryshimet e fundit (sesioni i sotëm)

Këto janë ndryshimet e bëra **sot** (5 pika). Koha e vlerësuar për një zhvillues që i bën vetë (pa AI) është dhënë për secilën.

### 17. Rezensionen (Reviews) – stil modern si sidebar

- Seksioni **Bewertungen** (reviews) në single product u stilizua si **karta e sidebar-it**: e njëjta bordurë (`#e2e8f0`), `border-radius: 12px`, titull me `border-bottom`, yje me ngjyrat e sidebar-it (`#f59e0b` / `#e2e8f0`), fushat dhe butoni “Senden” në stilin e sidebar-it.
- **Vetëm** `woocommerce.long.css` u ndryshua (konventë: mos prek `woocommerce.css`).
- **Koha e vlerësuar (vetë):** ~1–1.5 orë (lexim stile sidebar, përshtatje selektorësh, responsive).

### 18. Mini-cart – mbyllje me klik jashtë

- Kur përdoruesi klikon **jashtë** panelit të mini-cart (mbi overlay-in e errët ose kudo tjetër), paneli mbyllet.
- Në **`woocommerce.long.js`**: listener `document.addEventListener("click", …)` që kontrollon nëse kliku është jashtë `#wt-mini-cart-panel` dhe jashtë trigger-it; nëse po dhe paneli është i hapur, thirret `close_panel()`.
- **Koha e vlerësuar (vetë):** ~20–30 min (debug pse nuk mbyllej, shtim listener, përjashtim trigger).

### 19. Filtri “Bewertung” – theks vizual për opsionin e zgjedhur

- Kur zgjidhet një rating (5, 4, 3, 2, 1 ose “Alle Bewertungen”), **duket qartë** cili është aktiv.
- Në **`woocommerce.long.css`**: `.ycona-filter-rating-active` me background të lehtë primary (`color-mix`), bordurë primary dhe teksti i etiketës me ngjyrë primary + `font-weight: 600`; `.ycona-filter-rating-item` me `border: 1px solid transparent` që layout të mos lëvizë kur bëhet aktiv.
- **Koha e vlerësuar (vetë):** ~30–45 min (zgjidhje stili që dallon nga hover, test në të gjitha opsionet).

### 20. Filtri “Bewertung” – theks pa reload (JavaScript)

- Theksi vizual duhet të ndryshojë **menjëherë** kur klikohet një opsion, pa pritur reload të faqes.
- Në **`woocommerce.long.js`**: blok i ri “Shop sidebar – rating filter highlight” – për çdo `.ycona-filter-rating-item` një `click` listener që heq `ycona-filter-rating-active` nga të gjithë dhe e shton vetëm te elementi i klikuar.
- **Koha e vlerësuar (vetë):** ~25–40 min (gjetja e skedarit, delegim eventi, test pa reload).

### 21. Konventë: vetëm `*.long.css` / `*.long.js`

- Nuk duhet ndryshuar skedarët e minifikuar (`woocommerce.css`, `woocommerce.min.js`) pa u kërkuar; punohet vetëm me `woocommerce.long.css` dhe `woocommerce.long.js`.
- Nëse ndërkohë u ndryshua `woocommerce.css`, u kthye (revert) në versionin origjinal.

### 22. Mega menu – butoni X (mbyllje) brenda panelit, top right

- Butoni për mbylljen e mega menu (X) u zhvendos **brenda** panelit, në këndin e sipërm djathtas të **`.panel-content.container`**, jo më në header pranë hamburgerit.
- **PHP** (`template-parts/mega-menu.php`): hequr ikona e mbylljes nga `#open-mega-menu` (mbeti vetëm hamburgeri); shtuar `<button class="mega-menu-close" id="mega-menu-close">` brenda `#panel-right` → `.panel-content.container` (klasë shtesë `mega-menu-panel-content`). URL për SVG me `get_template_directory_uri()`.
- **JS** (`assets/js/functions.long.js`): listener për `#mega-menu-close` që heq `active` nga `#open-mega-menu` dhe `open-left` / `open-right` nga panelet (e njëjta sjellje si mbyllja nga hamburgeri).
- **CSS** (`assets/css/main.long.css`): hequr stilet e `.close-icon` nga header; shtuar `.mega-menu-panel-content { position: relative }`, `.mega-menu-close` me `position: absolute; top: 0; right: 0`, madhësi 56×56px, background i bardhë; kur paneli është i hapur (`.panel-right.open-right`) X shfaqet me animacion (opacity 0→1, scale 0.9→1) me delay 0.2s që të vijë së bashku me panelin.
- **Koha e vlerësuar (vetë):** ~35–50 min (ndërrim markup, lidhje klik, pozicionim CSS, animacion me delay, test në mobile).

---

## Shop sidebar – kategoritë dhe filtrat (detyrat e implementimit)

Implementimi i sidebar-it të shop-it (kategoritë + filtrat) përfshin këto **detyra** dhe **orët** e vlerësuara për një njeri.

### Detyra 1: Layout shop + sidebar (archive-product.php)

- Override i **`woocommerce/archive-product.php`**: layout me `.ycona-shop-wrap`, `.ycona-shop-layout`, `.ycona-shop-sidebar-wrap` dhe `.ycona-shop-main`.
- Butona për mobile: **Categories** dhe **Filters** (`.ycona-sidebar-mobile-toggle`) që hapin/mbyllin panelet në mobile.
- Thirrja e `ycona_shop_category_sidebar()` për të nxjerrë kategoritë dhe filtrat.  
**Kohë:** ~1–1.5 orë.

### Detyra 2: Sidebar kategorish (support-woocommerce.php)

- Funksioni **`ycona_shop_category_sidebar()`**: listë kategorish (product_cat) me nënkategoritë, link te shop dhe te kategoritë, klasa aktive, toggle për nënkategoritë.
- Output: `<aside id="ycona-sidebar-cats">` me `.ycona-cat-list`, `.ycona-cat-item`, count badge.  
**Kohë:** ~2–3 orë.

### Detyra 3: Forma e filtrave (Price, Availability, Offers, Rating, Tags)

- Brenda të njëjtit funksion: `<aside id="ycona-sidebar-filters">` me **formë GET** (`.ycona-filters-form`).
- **Price:** dual range slider (min/max), input numra, fill vizual; përdorim `min_price`, `max_price`.
- **Availability:** checkbox “In Stock only” (`in_stock`).
- **Offers:** checkbox “On Sale” (`on_sale`).
- **Rating:** radio 5→1 yje + “All ratings” (`rating_filter`), SVG yje të mbushura/bosh.
- **Tags:** checkbox për product_tag (`product_tag[]`).
- Fusha të fshehura për `orderby`, `paged`; butona **Apply Filters** dhe **Reset**.  
**Kohë:** ~3–4 orë.

### Detyra 4: Query – aplikimi i filtrave (pre_get_posts)

- Funksioni **`ycona_shop_sidebar_filters_query()`**: hook te `pre_get_posts`; lexon `$_GET` (min_price, max_price, in_stock, on_sale, rating_filter, product_tag) dhe ndryshon `WP_Query` (meta_query për rating, tax_query për tag, etj.).  
**Kohë:** ~1.5–2.5 orë.

### Detyra 5: CSS – stilet e sidebar-it dhe filtrave

- Në **`woocommerce.long.css`**: `.ycona-shop-sidebar`, `.ycona-shop-sidebar-filters`, `.ycona-filter-section`, `.ycona-filter-title`, price slider (track, fill, thumb), checkboxes, rating list (`.ycona-filter-rating-item`, `.ycona-filter-rating-active`), tags (pills), butona Apply/Reset, sticky sidebar, responsive (mobile toggles).  
**Kohë:** ~2–3 orë.

### Detyra 6: JavaScript – slider çmimi + theks rating

- Në **`woocommerce.long.js`**: dual-range price slider (update fill, sync min/max inputs); click te `.ycona-filter-rating-item` për të shtuar/hequr `ycona-filter-rating-active` pa reload.  
**Kohë:** ~1–1.5 orë.

**Total për Shop sidebar (detyra 1–6):** **~10.5–15 orë** për një njeri.

---

## Koha totale për ndryshimet e sotme (vetë, pa AI)

| Pika | Përshkrim i shkurtër | Kohë (përafërsisht) |
|------|----------------------|----------------------|
| 17 | Reviews – stil si sidebar | 1–1.5 orë |
| 18 | Mini-cart – mbyllje me klik jashtë | 20–30 min |
| 19 | Filtri Bewertung – theks vizual (CSS) | 30–45 min |
| 20 | Filtri Bewertung – theks pa reload (JS) | 25–40 min |
| 21 | Konventë / revert .css | ~5 min (nëse bëhet gabimisht) |
| 22 | Mega menu – X brenda panelit, top right + animacion | 35–50 min |

**Total për 6 ndryshimet e sotme (17–22):** rreth **3–4 orë** nëse i bën një person vetë (pa dokumentim).  
Me dokumentim dhe test: **~3.5–4.5 orë**.

---

---

## Përmbledhje e plotë e diskutuara dhe të implementuara (prej ~5 orësh)

| Grupi | Çfarë u diskutua / u implementua |
|-------|-----------------------------------|
| **1–15** | Checkout, mini-cart, stepper, validim, select-e, single product, related/upsells/cross-sells, sale badge, PWA, Swiper, short description variacione |
| **16** | Short description për variacionet nga prindi |
| **16a–16e** | Përkthimet de_DE: load_theme_textdomain, .po/.mo, Theme Options të gjitha, forma Sie, CPT (accordion, cards, testimonials), compile-mo.php |
| **17** | Reviews (Bewertungen) – stil modern si sidebar (vetëm .long.css) |
| **18** | Mini-cart – mbyllje me klik jashtë |
| **19–20** | Filtri Bewertung – theks vizual (CSS) dhe theks pa reload (JS) |
| **21** | Konventë: puno vetëm me *.long.css / *.long.js; revert i .css të minifikuar |
| **22** | Mega menu – butoni X brenda panelit (top right) me animacion |
| **23** | Shop sidebar – kategoritë dhe filtrat (layout, kategoritë, Price/Availability/Offers/Rating/Tags, query, CSS, JS) – shiko detyrat 1–6 më lart |

**Koha totale e vlerësuar** që një person t’i bëjë vetë të gjitha këto (1–23 + 16a–16e): **~56–82 orë** (implementim + testime + dokumentim). Pa përkthimet: **~50–75 orë**.

---

## Tabelë e plotë e orëve (real human)

Kohë e vlerësuar për një zhvillues me përvojë që i bën vetë të gjitha ndryshimet (pa AI), në orë (h) ose minuta (min).

| Nr | Përshkrim i shkurtër | Kohë (min–max) |
|----|----------------------|----------------|
| 1 | Checkout redesign (2 kolona, kartat, sticky summary) | 3–5 h |
| 2 | Mini-cart: Cart ↔ Checkout | 1–2 h |
| 3 | Stepper (Warenkorb → Kasse → Bestellung) | 1.5–2 h |
| 4 | Checkout validation (border, scroll, fshehje notices) | 2–3 h |
| 5 | Stilizim i të gjitha &lt;select&gt; (WooCommerce) | 3–4 h |
| 6 | SelectWoo / Select2 stub | 0.5 h |
| 7 | Single product (tabs, galeri, zoom, design) | 4–6 h |
| 8 | Related/Upsells design si shop | 1.5–2 h |
| 9 | Sale percentage badge (llogaritje + CSS) | 2–3 h |
| 10 | Sale badge mbi galeri (pozicionim) | 0.5–1 h |
| 11 | Ndarja e WooCommerce JS (woocommerce.long.js) | 1.5–2 h |
| 12 | PWA manifest (path ikonash) | 0.25 h |
| 13 | Swiper (related/upsells/cross-sells, 4/2/1, butona) | 4–5 h |
| 14 | Titullat Upsells vs Cross-sells | (përfshihet në 13/15) |
| 15 | Layout 4 në rresht + short description variacione | 1–2 h |
| 16 | Short description për variacionet nga prindi | (përfshihet në 15) |
| 16a | Text domain, .po/.mo, nxjerrje vargjesh | 1–1.5 h |
| 16b | Theme Options – të gjitha vargjet e përkthyer | 1.5–2.5 h |
| 16c | Gjermanisht Sie (30+ ndryshime në .po) | 0.5–1 h |
| 16d | CPT backend (accordion, cards, testimonials) | 1–2 h |
| 16e | compile-mo.php (skript .po → .mo) | 0.25–0.5 h |
| 17 | Reviews (Bewertungen) – stil si sidebar | 1–1.5 h |
| 18 | Mini-cart – mbyllje me klik jashtë | 20–30 min |
| 19 | Filtri Bewertung – theks vizual (CSS) | 30–45 min |
| 20 | Filtri Bewertung – theks pa reload (JS) | 25–40 min |
| 21 | Konventë: vetëm *.long.css / *.long.js | ~5 min |
| 22 | Mega menu – X brenda panelit, top right + animacion | 35–50 min |
| **23** | **Shop sidebar – kategoritë + filtrat** (layout, kategoritë, forma Price/Availability/Offers/Rating/Tags, query pre_get_posts, CSS, JS slider + rating highlight) | **10.5–15 h** |

**Total implementim (1–23 + 16a–16e):** ~45.5–67 orë (pa përkthimet: ~40.5–60 orë).  
**+ Testime dhe rregullime:** ~8–12 orë.  
**+ Dokumentim (CHANGES.md, komente):** ~2–3 orë.

**Total i plotë (real human):** **~56–82 orë** (me përkthimet); **~50–75 orë** (pa përkthimet).

---

*Dokumenti mbulon të gjitha ndryshimet dhe diskutimet nga fillimi i sesionit (rreth 5–6 orë më parë) deri më tani: 1–16, përkthimet (16a–16e), ndryshimet e fundit (17–22), vendndodhjen e faqeve 404/Thank You dhe tabelën e plotë të orëve.*
