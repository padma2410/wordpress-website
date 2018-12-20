                            <figure class="gallery-item aux-hover-active <?php echo esc_attr( $isotope_item_classes . ' ' . $item_classes ); ?>">

                                <?php if ( $add_lightbox ) { ?>
                                    <div class="<?php echo ('tiles' != $layout) ? 'aux-frame-mask' : ''; ?> aux-frame-darken">
                                        <a href="<?php echo esc_url( $attachment_url ); ?>" class="aux-lightbox-btn" <?php echo $lightbox_attrs; // already escaped ?>">
                                            <div class="aux-hover-scale-circle-plus">
                                                <span class="aux-symbol-plus"></span>
                                                <span class="aux-symbol-circle"></span>
                                            </div>
                                <?php } elseif ( 'none' != $link ) { ?>
                                        <a href="<?php echo esc_url( $attachment_url ); ?>" >
                                <?php } ?>

                                <?php echo $attachment_media; ?>

                                <?php if ( $add_lightbox || 'none' != $link ) { ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if ( $add_caption ) { ?>
                                    <figcaption class="wp-caption-text gallery-caption">
                                        <?php echo $attachment_caption ?>
                                    </figcaption>
                                <?php } ?>
                            </figure>
