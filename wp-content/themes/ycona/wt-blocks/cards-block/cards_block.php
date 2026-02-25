<?php
/**
 * Copyright (c) 2025 by Granit Nebiu
 *
 * All rights are reserved. Reproduction or transmission in whole or in part, in
 * any form or by any means, electronic, mechanical or otherwise, is prohibited
 * without the prior written consent of the copyright owner.
 *
 * Functions and definitions
 *
 * @package WordPress
 * @subpackage ycona
 * @author Granit Nebiu
 * @since 1.0
 */

	// rc for cards block
	function wt_cards_block_rc($attributes, $content) {
		global $theme_path;
		
		// Register & enqueue styles
		wp_register_style("wt_cards_block", $theme_path . "/wt-blocks/cards-block/cards_block.css", array("css-main"), "1");
		wp_enqueue_style("wt_cards_block");
		
		$post_ID            = $attributes["post_id"] ?? "";
		$class_name         = $attributes["className"] ?? "default-wt-shop";
		$section_layout     = $attributes["layout"] ?? "container";
		$columns            = $attributes["columns"] ?? "3";
		$uid                = uniqid('cards_');
		$space_bottom       = $attributes["space_bottom"] ?? 'yes';
		$space_top          = $attributes["space_top"] ?? 'yes';
		
		$cards_data         = get_post_meta($post_ID, 'cards_fields', true);
		$cards              = $cards_data["cards"] ?? [];
		$cards_count        = count($cards);
		
		$cards_html = '';
		
		foreach ($cards as $card) {
			$image          = $card["image"] ?? "";
			$card_title     = $card["card_title"] ?? "";
			$description    = $card["description"] ?? "";
			$link_url       = $card["link_url"] ?? "";
			$link_text      = $card["link_text"] ?? "";
			
			$image_html = '';
			if (!empty($image))
			{
				$image_html = "<div class='card-image'><img src='$image' alt='" . esc_attr($card_title) . "' /></div>";
			}
			
			$link_html = '';
			if (!empty($link_url))
			{
				$btn_text = !empty($link_text) ? $link_text : __('Read more', 'ycona');
				$link_html = "<a href='" . esc_url($link_url) . "' class='card-link'>" . esc_html($btn_text) . "</a>";
			}
			
			$title_html = '';
			if (!empty($card_title))
			{
				$title_html = "<h3 class='card-title'>" . esc_html($card_title) . "</h3>";
			}
			
			$description_html = '';
			if (!empty($description))
			{
				$description_html = "<div class='card-description'>$description</div>";
			}
			
			$cards_html .= "
							<div class='col-md-" . (12 / intval($columns)) . " cards-col'>
								<div class='card-item'>
									$image_html
									<div class='card-body'>
										$title_html
										$description_html
										$link_html
									</div>
								</div>
							</div>
							";
		}
		
		$html = "<section id='$uid' class='cards-section cards-section-count-$cards_count cards-columns-$columns $class_name space-bottom-$space_bottom space-top-$space_top'>
					<div class='$section_layout'>
						<div class='row'>
							$cards_html
						</div>
					</div>
				</section>
				";
		
		return $html;
	}
