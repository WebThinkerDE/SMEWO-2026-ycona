<?php
/**
 * Demo Data Importer for WebThinker Glossary
 *
 * Run once via: WP Admin → Glossary → Import Demo Data
 * Or visit: /wp-admin/admin.php?page=wt-glossary-demo-import
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Admin menu entry ─────────────────────────────────────────── */

function wt_glossary_demo_menu() {
    add_submenu_page(
        'edit.php?post_type=wt_glossary_term',
        __( 'Import Demo Data', 'wt-glossary' ),
        __( 'Import Demo Data', 'wt-glossary' ),
        'manage_options',
        'wt-glossary-demo-import',
        'wt_glossary_demo_page'
    );
}
add_action( 'admin_menu', 'wt_glossary_demo_menu' );

/* ── Admin page ───────────────────────────────────────────────── */

function wt_glossary_demo_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    $imported = false;
    $count    = 0;

    if ( isset( $_POST['wt_glossary_import_demo'] ) && check_admin_referer( 'wt_glossary_demo_import' ) ) {
        $count    = wt_glossary_insert_demo_terms();
        $imported = true;
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Import Glossary Demo Data', 'wt-glossary' ); ?></h1>

        <?php if ( $imported ) : ?>
            <div class="notice notice-success">
                <p><?php printf( esc_html__( 'Successfully imported %d glossary terms.', 'wt-glossary' ), $count ); ?></p>
            </div>
        <?php endif; ?>

        <p><?php esc_html_e( 'Click the button below to import sample glossary terms for testing and demonstration purposes.', 'wt-glossary' ); ?></p>

        <form method="post">
            <?php wp_nonce_field( 'wt_glossary_demo_import' ); ?>
            <input type="hidden" name="wt_glossary_import_demo" value="1">
            <?php submit_button( __( 'Import Demo Terms', 'wt-glossary' ), 'primary', 'submit', true ); ?>
        </form>
    </div>
    <?php
}

/* ── Demo terms data ──────────────────────────────────────────── */

function wt_glossary_get_demo_terms() {

    return array(

        // ── A ───────────────────────────────────────────────
        array(
            'title'      => 'API',
            'content'    => '<p>An <strong>Application Programming Interface (API)</strong> is a set of rules and protocols that allows different software applications to communicate with each other. APIs define the methods and data formats that programs can use to request and exchange information.</p>
<h3>How APIs Work</h3>
<p>APIs work on a request-response model. A client sends a request to an API endpoint, and the server processes the request and returns a response, typically in JSON or XML format.</p>
<h3>Types of APIs</h3>
<ul>
<li><strong>REST API</strong> – Uses HTTP methods (GET, POST, PUT, DELETE)</li>
<li><strong>SOAP API</strong> – Uses XML-based messaging protocol</li>
<li><strong>GraphQL</strong> – Query language for APIs developed by Facebook</li>
<li><strong>WebSocket API</strong> – Enables real-time bidirectional communication</li>
</ul>
<h3>Example</h3>
<p>When you use a weather app on your phone, it sends an API request to a weather service to retrieve the current forecast for your location.</p>',
            'short_desc' => 'A set of rules and protocols that allows different software applications to communicate with each other.',
            'synonyms'   => 'Application Programming Interface, Web API, REST API',
            'category'   => 'Technology',
        ),

        array(
            'title'      => 'Agile',
            'content'    => '<p><strong>Agile</strong> is a project management and software development methodology that emphasizes iterative development, collaboration, and flexibility. It breaks projects into small increments called sprints, typically lasting 1–4 weeks.</p>
<h3>Core Principles</h3>
<ul>
<li>Individuals and interactions over processes and tools</li>
<li>Working software over comprehensive documentation</li>
<li>Customer collaboration over contract negotiation</li>
<li>Responding to change over following a plan</li>
</ul>
<h3>Popular Agile Frameworks</h3>
<p><strong>Scrum</strong>, <strong>Kanban</strong>, and <strong>Extreme Programming (XP)</strong> are the most widely adopted Agile frameworks in the industry.</p>',
            'short_desc' => 'A project management methodology emphasizing iterative development, collaboration, and adaptability.',
            'synonyms'   => 'Agile Methodology, Agile Development',
            'category'   => 'Project Management',
        ),

        array(
            'title'      => 'Authentication',
            'content'    => '<p><strong>Authentication</strong> is the process of verifying the identity of a user, device, or system. It ensures that the entity requesting access is who or what it claims to be.</p>
<h3>Common Authentication Methods</h3>
<ul>
<li><strong>Password-based</strong> – Username and password combination</li>
<li><strong>Multi-Factor Authentication (MFA)</strong> – Combines two or more verification methods</li>
<li><strong>Biometric</strong> – Fingerprint, face recognition, or iris scan</li>
<li><strong>Token-based</strong> – JWT, OAuth tokens</li>
<li><strong>Single Sign-On (SSO)</strong> – One login for multiple applications</li>
</ul>',
            'short_desc' => 'The process of verifying the identity of a user, device, or system before granting access.',
            'synonyms'   => 'Auth, Identity Verification, Login',
            'category'   => 'Security',
        ),

        // ── B ───────────────────────────────────────────────
        array(
            'title'      => 'Backend',
            'content'    => '<p>The <strong>Backend</strong> (also called server-side) refers to the part of a software application that runs on the server. It handles business logic, database operations, authentication, and serves data to the frontend.</p>
<h3>Common Backend Technologies</h3>
<ul>
<li><strong>Languages:</strong> PHP, Python, Node.js, Java, Ruby, Go</li>
<li><strong>Frameworks:</strong> Laravel, Django, Express.js, Spring Boot</li>
<li><strong>Databases:</strong> MySQL, PostgreSQL, MongoDB, Redis</li>
</ul>
<p>The backend communicates with the frontend via APIs, processing requests and returning structured data.</p>',
            'short_desc' => 'The server-side part of an application that handles logic, database operations, and data processing.',
            'synonyms'   => 'Server-side, Backend Development',
            'category'   => 'Technology',
        ),

        array(
            'title'      => 'Bootstrap',
            'content'    => '<p><strong>Bootstrap</strong> is a popular open-source CSS framework developed by Twitter. It provides pre-built responsive components, a flexible grid system, and utility classes for rapid web development.</p>
<h3>Key Features</h3>
<ul>
<li>12-column responsive grid system</li>
<li>Pre-designed UI components (buttons, cards, modals, navbars)</li>
<li>Utility classes for spacing, typography, and layout</li>
<li>JavaScript plugins (carousel, dropdown, tooltips)</li>
<li>Customizable via Sass variables</li>
</ul>
<h3>Versions</h3>
<p>Bootstrap 5 (latest major version) dropped jQuery dependency and introduced improved grid, RTL support, and enhanced utilities.</p>',
            'short_desc' => 'An open-source CSS framework providing responsive components and a grid system for rapid web development.',
            'synonyms'   => 'Bootstrap CSS, Twitter Bootstrap, BS5',
            'category'   => 'Technology',
        ),

        // ── C ───────────────────────────────────────────────
        array(
            'title'      => 'CMS',
            'content'    => '<p>A <strong>Content Management System (CMS)</strong> is a software application that allows users to create, manage, and modify digital content without needing specialized technical knowledge.</p>
<h3>Popular CMS Platforms</h3>
<ul>
<li><strong>WordPress</strong> – Powers over 40% of all websites</li>
<li><strong>Drupal</strong> – Enterprise-grade CMS</li>
<li><strong>Joomla</strong> – Flexible and extensible</li>
<li><strong>Shopify</strong> – E-commerce focused</li>
</ul>
<h3>Benefits</h3>
<p>A CMS separates content from design, enabling non-technical users to publish content through an intuitive admin interface while developers handle the underlying templates and functionality.</p>',
            'short_desc' => 'A software application that enables users to create, manage, and publish digital content without coding.',
            'synonyms'   => 'Content Management System, Web CMS',
            'category'   => 'Technology',
        ),

        array(
            'title'      => 'CSS',
            'content'    => '<p><strong>Cascading Style Sheets (CSS)</strong> is a stylesheet language used to describe the presentation and visual formatting of HTML documents. CSS controls layout, colors, fonts, spacing, animations, and responsive design.</p>
<h3>Key Concepts</h3>
<ul>
<li><strong>Selectors</strong> – Target HTML elements for styling</li>
<li><strong>Box Model</strong> – Content, padding, border, margin</li>
<li><strong>Flexbox</strong> – One-dimensional layout system</li>
<li><strong>Grid</strong> – Two-dimensional layout system</li>
<li><strong>Media Queries</strong> – Responsive design breakpoints</li>
<li><strong>Custom Properties</strong> – CSS variables for reusable values</li>
</ul>',
            'short_desc' => 'A stylesheet language used to control the visual presentation and layout of HTML documents.',
            'synonyms'   => 'Cascading Style Sheets, Stylesheet',
            'category'   => 'Technology',
        ),

        // ── D ───────────────────────────────────────────────
        array(
            'title'      => 'Database',
            'content'    => '<p>A <strong>Database</strong> is an organized collection of structured data stored electronically. Databases are managed by Database Management Systems (DBMS) and allow efficient storage, retrieval, and manipulation of data.</p>
<h3>Types of Databases</h3>
<ul>
<li><strong>Relational (SQL)</strong> – MySQL, PostgreSQL, SQLite</li>
<li><strong>NoSQL</strong> – MongoDB, CouchDB, Cassandra</li>
<li><strong>In-Memory</strong> – Redis, Memcached</li>
<li><strong>Graph</strong> – Neo4j, ArangoDB</li>
</ul>',
            'short_desc' => 'An organized collection of structured data stored and managed electronically for efficient access.',
            'synonyms'   => 'DB, Data Store, DBMS',
            'category'   => 'Technology',
        ),

        array(
            'title'      => 'Deployment',
            'content'    => '<p><strong>Deployment</strong> is the process of making a software application available for use by moving it from a development environment to a production server. Modern deployment practices emphasize automation, reliability, and rollback capabilities.</p>
<h3>Deployment Strategies</h3>
<ul>
<li><strong>Blue-Green Deployment</strong> – Two identical production environments</li>
<li><strong>Rolling Deployment</strong> – Gradual update across servers</li>
<li><strong>Canary Deployment</strong> – Release to a small subset of users first</li>
<li><strong>Continuous Deployment (CD)</strong> – Automated deployment on every commit</li>
</ul>',
            'short_desc' => 'The process of releasing and making a software application available on a production server.',
            'synonyms'   => 'Release, Go-Live, Ship',
            'category'   => 'DevOps',
        ),

        // ── E ───────────────────────────────────────────────
        array(
            'title'      => 'E-Learning',
            'content'    => '<p><strong>E-Learning</strong> (electronic learning) refers to education and training delivered through digital platforms. It encompasses online courses, virtual classrooms, interactive modules, and multimedia content.</p>
<h3>Types of E-Learning</h3>
<ul>
<li><strong>Synchronous</strong> – Live, real-time sessions (webinars, virtual classrooms)</li>
<li><strong>Asynchronous</strong> – Self-paced content (recorded videos, reading materials)</li>
<li><strong>Blended Learning</strong> – Combination of online and in-person instruction</li>
<li><strong>Microlearning</strong> – Short, focused learning units</li>
</ul>
<h3>Benefits</h3>
<p>E-Learning offers flexibility, scalability, cost-effectiveness, and the ability to track learner progress through Learning Management Systems (LMS).</p>',
            'short_desc' => 'Education and training delivered through digital platforms including online courses and virtual classrooms.',
            'synonyms'   => 'Electronic Learning, Online Learning, Digital Learning',
            'category'   => 'Education',
        ),

        // ── F ───────────────────────────────────────────────
        array(
            'title'      => 'Frontend',
            'content'    => '<p>The <strong>Frontend</strong> (also called client-side) is the part of a web application that users interact with directly in their browser. It encompasses everything visible on screen — layout, design, typography, buttons, forms, and animations.</p>
<h3>Core Technologies</h3>
<ul>
<li><strong>HTML</strong> – Structure and content</li>
<li><strong>CSS</strong> – Visual styling and layout</li>
<li><strong>JavaScript</strong> – Interactivity and dynamic behavior</li>
</ul>
<h3>Popular Frameworks</h3>
<p>React, Vue.js, Angular, and Svelte are widely used JavaScript frameworks for building modern frontend applications.</p>',
            'short_desc' => 'The client-side part of a web application that users see and interact with in their browser.',
            'synonyms'   => 'Client-side, Frontend Development, UI',
            'category'   => 'Technology',
        ),

        // ── G ───────────────────────────────────────────────
        array(
            'title'      => 'Gutenberg',
            'content'    => '<p><strong>Gutenberg</strong> is the block-based editor introduced in WordPress 5.0. Named after Johannes Gutenberg, the inventor of the printing press, it replaced the classic TinyMCE editor with a modular, block-based editing experience.</p>
<h3>Key Features</h3>
<ul>
<li>Block-based content editing (paragraphs, images, videos, etc.)</li>
<li>Custom block development using React</li>
<li>Full Site Editing (FSE) capabilities</li>
<li>Pattern library for reusable block arrangements</li>
<li>Template editing within the block editor</li>
</ul>
<h3>Custom Blocks</h3>
<p>Developers can create custom blocks using JavaScript (React) for the editor interface and PHP for server-side rendering.</p>',
            'short_desc' => 'The block-based content editor in WordPress that enables modular page building with reusable blocks.',
            'synonyms'   => 'Block Editor, WordPress Editor, WP Block Editor',
            'category'   => 'WordPress',
        ),

        // ── H ───────────────────────────────────────────────
        array(
            'title'      => 'HTTP',
            'content'    => '<p><strong>Hypertext Transfer Protocol (HTTP)</strong> is the foundational protocol used for transferring data on the World Wide Web. It defines how messages are formatted and transmitted between web browsers and servers.</p>
<h3>HTTP Methods</h3>
<ul>
<li><strong>GET</strong> – Retrieve data</li>
<li><strong>POST</strong> – Submit data</li>
<li><strong>PUT</strong> – Update existing data</li>
<li><strong>DELETE</strong> – Remove data</li>
<li><strong>PATCH</strong> – Partially update data</li>
</ul>
<h3>HTTPS</h3>
<p><strong>HTTPS</strong> (HTTP Secure) adds encryption via TLS/SSL, ensuring data transmitted between client and server is encrypted and secure.</p>',
            'short_desc' => 'The foundational protocol for transferring data on the web between browsers and servers.',
            'synonyms'   => 'Hypertext Transfer Protocol, HTTPS',
            'category'   => 'Technology',
        ),

        // ── J ───────────────────────────────────────────────
        array(
            'title'      => 'JSON',
            'content'    => '<p><strong>JavaScript Object Notation (JSON)</strong> is a lightweight data interchange format that is easy for humans to read and write, and easy for machines to parse and generate. It has become the standard format for API communication.</p>
<h3>Syntax Example</h3>
<pre><code>{
    "name": "WebThinker Glossary",
    "version": "1.0.0",
    "features": ["search", "A-Z navigation", "tooltips"],
    "active": true
}</code></pre>
<h3>JSON vs XML</h3>
<p>JSON is generally preferred over XML for web APIs because it is more compact, faster to parse, and natively supported by JavaScript.</p>',
            'short_desc' => 'A lightweight data interchange format widely used for API communication and configuration files.',
            'synonyms'   => 'JavaScript Object Notation',
            'category'   => 'Technology',
        ),

        // ── L ───────────────────────────────────────────────
        array(
            'title'      => 'LMS',
            'content'    => '<p>A <strong>Learning Management System (LMS)</strong> is a software platform designed to create, deliver, manage, and track educational courses and training programs. LMS platforms are widely used in corporate training, academic institutions, and online education.</p>
<h3>Core Features</h3>
<ul>
<li>Course creation and management</li>
<li>Student enrollment and progress tracking</li>
<li>Assessment and certification</li>
<li>Discussion forums and collaboration tools</li>
<li>Reporting and analytics</li>
</ul>
<h3>Popular LMS Platforms</h3>
<p>Moodle, Canvas, Blackboard, LearnDash (WordPress), and TalentLMS are among the most widely used LMS platforms.</p>',
            'short_desc' => 'A software platform for creating, delivering, and tracking educational courses and training programs.',
            'synonyms'   => 'Learning Management System, Learning Platform',
            'category'   => 'Education',
        ),

        // ── M ───────────────────────────────────────────────
        array(
            'title'      => 'Multilingualism',
            'content'    => '<p><strong>Multilingualism</strong> in web development refers to the ability of a website or application to present content in multiple languages. This is essential for reaching international audiences and providing localized user experiences.</p>
<h3>Implementation Approaches</h3>
<ul>
<li><strong>WPML</strong> – WordPress Multilingual Plugin for content translation</li>
<li><strong>Polylang</strong> – Free WordPress multilingual plugin</li>
<li><strong>i18n libraries</strong> – Internationalization frameworks (gettext, react-intl)</li>
<li><strong>URL-based</strong> – Subdomains, subdirectories, or URL parameters per language</li>
</ul>
<h3>Best Practices</h3>
<p>Use proper <code>hreflang</code> tags for SEO, store translations in a structured format, and consider cultural adaptation beyond mere text translation.</p>',
            'short_desc' => 'The ability of a website or application to present content in multiple languages for international audiences.',
            'synonyms'   => 'Mehrsprachigkeit, i18n, Internationalization, Localization',
            'category'   => 'Technology',
        ),

        // ── P ───────────────────────────────────────────────
        array(
            'title'      => 'Plugin',
            'content'    => '<p>A <strong>Plugin</strong> is a software component that adds specific functionality to an existing application. In WordPress, plugins extend the core functionality without modifying the core files.</p>
<h3>WordPress Plugin Structure</h3>
<ul>
<li>Main plugin file with header comment defining metadata</li>
<li>Hooks (actions and filters) to interact with WordPress core</li>
<li>Custom Post Types, shortcodes, and Gutenberg blocks</li>
<li>Admin settings pages</li>
<li>Frontend assets (CSS, JavaScript)</li>
</ul>
<h3>Plugin Directory</h3>
<p>The official WordPress Plugin Directory hosts over 60,000 free plugins covering SEO, security, e-commerce, forms, and more.</p>',
            'short_desc' => 'A software component that adds specific functionality to an existing application like WordPress.',
            'synonyms'   => 'Plug-in, Extension, Add-on, Module',
            'category'   => 'WordPress',
        ),

        // ── R ───────────────────────────────────────────────
        array(
            'title'      => 'Responsive Design',
            'content'    => '<p><strong>Responsive Design</strong> is a web design approach that ensures websites automatically adapt their layout and content to fit different screen sizes and devices — from desktop monitors to tablets and smartphones.</p>
<h3>Key Techniques</h3>
<ul>
<li><strong>Fluid Grids</strong> – Percentage-based column widths</li>
<li><strong>Flexible Images</strong> – Images that scale with their container</li>
<li><strong>Media Queries</strong> – CSS rules that apply at specific breakpoints</li>
<li><strong>Mobile-First</strong> – Designing for mobile screens first, then scaling up</li>
</ul>
<h3>Common Breakpoints</h3>
<p>Typical breakpoints include 576px (mobile), 768px (tablet), 992px (desktop), and 1200px (large desktop).</p>',
            'short_desc' => 'A web design approach ensuring websites adapt their layout to fit different screen sizes and devices.',
            'synonyms'   => 'RWD, Mobile-First Design, Adaptive Design',
            'category'   => 'Design',
        ),

        // ── S ───────────────────────────────────────────────
        array(
            'title'      => 'SEO',
            'content'    => '<p><strong>Search Engine Optimization (SEO)</strong> is the practice of improving a website\'s visibility and ranking in search engine results pages (SERPs). SEO involves technical, content, and off-page strategies to drive organic traffic.</p>
<h3>Key Areas</h3>
<ul>
<li><strong>On-Page SEO</strong> – Title tags, meta descriptions, heading structure, content quality</li>
<li><strong>Technical SEO</strong> – Site speed, mobile-friendliness, structured data, XML sitemaps</li>
<li><strong>Off-Page SEO</strong> – Backlinks, social signals, brand mentions</li>
<li><strong>Local SEO</strong> – Google My Business, local citations, reviews</li>
</ul>',
            'short_desc' => 'The practice of improving a website\'s visibility and ranking in search engine results to drive organic traffic.',
            'synonyms'   => 'Search Engine Optimization, Search Optimization',
            'category'   => 'Marketing',
        ),

        array(
            'title'      => 'Shortcode',
            'content'    => '<p>A <strong>Shortcode</strong> is a WordPress-specific markup tag that lets you embed dynamic content or functionality within posts and pages using a simple bracket syntax like <code>[shortcode_name]</code>.</p>
<h3>Usage</h3>
<p>Shortcodes can accept attributes: <code>[gallery columns="3" size="medium"]</code>. They can also wrap content: <code>[highlight]Important text[/highlight]</code>.</p>
<h3>Creating Shortcodes</h3>
<p>Developers create shortcodes using <code>add_shortcode()</code> in PHP. The callback function processes the attributes and returns HTML output.</p>
<h3>Gutenberg Alternative</h3>
<p>While shortcodes remain fully supported, WordPress now recommends using Gutenberg blocks for new functionality, as they provide a visual editing experience.</p>',
            'short_desc' => 'A WordPress markup tag that embeds dynamic content or functionality in posts and pages using bracket syntax.',
            'synonyms'   => 'WP Shortcode, Shortcode Tag',
            'category'   => 'WordPress',
        ),

        // ── U ───────────────────────────────────────────────
        array(
            'title'      => 'UI/UX',
            'content'    => '<p><strong>UI (User Interface)</strong> refers to the visual elements users interact with, while <strong>UX (User Experience)</strong> encompasses the overall experience and satisfaction a user has when interacting with a product.</p>
<h3>UI Design Principles</h3>
<ul>
<li>Consistency in visual elements</li>
<li>Clear visual hierarchy</li>
<li>Accessible color contrast and typography</li>
<li>Responsive and adaptive layouts</li>
</ul>
<h3>UX Design Principles</h3>
<ul>
<li>User-centered design process</li>
<li>Intuitive navigation and information architecture</li>
<li>Minimal cognitive load</li>
<li>Continuous user testing and iteration</li>
</ul>',
            'short_desc' => 'UI covers visual design elements; UX encompasses the overall user satisfaction and interaction experience.',
            'synonyms'   => 'User Interface, User Experience, UX Design, UI Design',
            'category'   => 'Design',
        ),

        // ── W ───────────────────────────────────────────────
        array(
            'title'      => 'WordPress',
            'content'    => '<p><strong>WordPress</strong> is the world\'s most popular open-source Content Management System (CMS), powering over 40% of all websites. Originally created as a blogging platform in 2003, it has evolved into a full-featured CMS and application framework.</p>
<h3>Key Features</h3>
<ul>
<li>Theme system for visual design customization</li>
<li>Plugin architecture with 60,000+ free plugins</li>
<li>Gutenberg block editor</li>
<li>REST API for headless usage</li>
<li>Custom Post Types and Taxonomies</li>
<li>Robust user role and permission system</li>
</ul>
<h3>WordPress.org vs WordPress.com</h3>
<p><strong>WordPress.org</strong> is the self-hosted, open-source software. <strong>WordPress.com</strong> is a hosted platform by Automattic offering managed WordPress hosting.</p>',
            'short_desc' => 'The world\'s most popular open-source CMS, powering over 40% of all websites globally.',
            'synonyms'   => 'WP, WordPress CMS, WordPress.org',
            'category'   => 'WordPress',
        ),

        array(
            'title'      => 'WPML',
            'content'    => '<p><strong>WPML (WordPress Multilingual Plugin)</strong> is a premium WordPress plugin that enables complete multilingual website management. It allows translating all content types including posts, pages, custom post types, taxonomies, menus, and theme/plugin strings.</p>
<h3>Key Features</h3>
<ul>
<li>Content translation with professional translation management</li>
<li>Language switcher widgets and shortcodes</li>
<li>SEO-friendly URL structures per language</li>
<li>WooCommerce multilingual support</li>
<li>String translation for themes and plugins</li>
<li>Automatic translation via DeepL, Google Translate</li>
</ul>
<h3>Integration</h3>
<p>WPML integrates with most themes and plugins. For custom development, use <code>suppress_filters => false</code> in WP_Query and register strings with <code>wpml-config.xml</code>.</p>',
            'short_desc' => 'A premium WordPress plugin for complete multilingual website management and content translation.',
            'synonyms'   => 'WordPress Multilingual Plugin, WP Multilingual',
            'category'   => 'WordPress',
        ),
    );
}

/* ── Insert demo terms ────────────────────────────────────────── */

function wt_glossary_insert_demo_terms() {

    $demo_terms = wt_glossary_get_demo_terms();
    $count = 0;
    $inserted_ids = array(); // Track IDs for related terms linking

    foreach ( $demo_terms as $term_data ) {

        // Check if term already exists (by title)
        $existing = get_page_by_title( $term_data['title'], OBJECT, 'wt_glossary_term' );
        if ( $existing ) {
            $inserted_ids[ $term_data['title'] ] = $existing->ID;
            continue;
        }

        $post_id = wp_insert_post( array(
            'post_type'    => 'wt_glossary_term',
            'post_title'   => $term_data['title'],
            'post_content' => $term_data['content'],
            'post_status'  => 'publish',
            'post_author'  => get_current_user_id(),
        ));

        if ( is_wp_error( $post_id ) ) {
            continue;
        }

        // Save meta
        update_post_meta( $post_id, '_wt_glossary_short_desc', $term_data['short_desc'] );
        update_post_meta( $post_id, '_wt_glossary_synonyms', $term_data['synonyms'] );

        // Assign category taxonomy
        if ( ! empty( $term_data['category'] ) ) {
            wp_set_object_terms( $post_id, $term_data['category'], 'wt_glossary_category' );
        }

        // Auto-assign letter
        wt_glossary_assign_letter( $post_id );

        $inserted_ids[ $term_data['title'] ] = $post_id;
        $count++;
    }

    // Set up related terms (cross-link some terms together)
    $relations = array(
        'API'               => array( 'Backend', 'JSON', 'HTTP' ),
        'Backend'           => array( 'Frontend', 'API', 'Database' ),
        'Frontend'          => array( 'Backend', 'CSS', 'Bootstrap' ),
        'CSS'               => array( 'Bootstrap', 'Frontend', 'Responsive Design' ),
        'Bootstrap'         => array( 'CSS', 'Frontend', 'Responsive Design' ),
        'CMS'               => array( 'WordPress', 'Plugin', 'Gutenberg' ),
        'Gutenberg'         => array( 'WordPress', 'Shortcode', 'Plugin' ),
        'WordPress'         => array( 'Plugin', 'Gutenberg', 'WPML', 'CMS' ),
        'WPML'              => array( 'WordPress', 'Multilingualism' ),
        'Plugin'            => array( 'WordPress', 'Gutenberg', 'Shortcode' ),
        'Shortcode'         => array( 'WordPress', 'Gutenberg', 'Plugin' ),
        'E-Learning'        => array( 'LMS', 'Multilingualism' ),
        'LMS'               => array( 'E-Learning' ),
        'Responsive Design' => array( 'CSS', 'Bootstrap', 'Frontend' ),
        'SEO'               => array( 'HTTP', 'CMS' ),
        'UI/UX'             => array( 'Frontend', 'Responsive Design', 'CSS' ),
        'Deployment'        => array( 'Backend', 'Agile' ),
        'Agile'             => array( 'Deployment' ),
    );

    foreach ( $relations as $term_title => $related_titles ) {
        if ( ! isset( $inserted_ids[ $term_title ] ) ) {
            continue;
        }
        $related_post_ids = array();
        foreach ( $related_titles as $related_title ) {
            if ( isset( $inserted_ids[ $related_title ] ) ) {
                $related_post_ids[] = $inserted_ids[ $related_title ];
            }
        }
        if ( ! empty( $related_post_ids ) ) {
            update_post_meta( $inserted_ids[ $term_title ], '_wt_glossary_related', $related_post_ids );
        }
    }

    return $count;
}
