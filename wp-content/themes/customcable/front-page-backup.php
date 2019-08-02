<?php get_header(); ?>

<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/slideshow-front.php'); ?>
<?php //echo do_shortcode('[slide-anything id="2294"]'); ?>

<div class="lead-ins hidden">
    <a class="col-sm-4 lead-in" href="<?php the_field('box_1_link'); ?>" title="<?php the_field('box_1_title'); ?>">
        <?php
        $image = get_field('box_1_photo');
        if (!empty($image)):
            ?>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
        <?php endif; ?>
        <h3><?php the_field('box_1_title'); ?></h3>
        <p><?php the_field('box_1_text'); ?></p>
    </a>
    <a class="col-sm-4 lead-in" href="<?php the_field('box_2_link'); ?>" title="<?php the_field('box_2_title'); ?>">
        <?php
        $image = get_field('box_2_photo');
        if (!empty($image)):
            ?>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
        <?php endif; ?>
        <h3><?php the_field('box_2_title'); ?></h3>
        <p><?php the_field('box_2_text'); ?></p>
    </a>
    <a class="col-sm-4 lead-in" href="<?php the_field('box_3_link'); ?>" title="<?php the_field('box_3_title'); ?>">
        <?php
        $image = get_field('box_3_photo');
        if (!empty($image)):
            ?>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" class="img-responsive center-block" />
        <?php endif; ?>
        <h3><?php the_field('box_3_title'); ?></h3>
        <p><?php the_field('box_3_text'); ?></p>
    </a>
</div>

<!-- product mockup htmlo starts -->
<div class="title hidden">
    <h2 class="home"><?php the_field('tagline', 'option'); ?></h2>
    <?php echo wp_get_attachment_image(700, 'full', 0, array('class' => 'img-responsive h2-art')); ?>
</div>

<div class="ListingView hidden">

    <div class="productrow">

        <div class="col-xs-12 col-sm-4 col-md-4">
            <h3 class="cat-title">Inside Plant Assemblies</h3>
            <div class="columnView blue-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-grey.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
            <div class="columnView blue-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-grey.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>

            <div class="columnView blue-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-grey.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
        </div>     

        <div class="col-xs-12 col-sm-4 col-md-4">
            <h3 class="cat-title">Outside Plant Assemblies</h3>
            <div class="columnView grey-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-blue.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
            <div class="columnView grey-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-blue.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>

            <div class="columnView grey-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-blue.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
        </div>


        <div class="col-xs-12 col-sm-4 col-md-4">
            <h3 class="cat-title">MTP/MPO</h3>
            <div class="columnView blue-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-grey.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
            <div class="columnView blue-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-grey.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>

            <div class="columnView blue-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-grey.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
        </div>

    </div>

    <div class="title">
        <h2 class="home greentext">Custom Cable & Assembly Solutions</h2>
    	<img width="1200" height="46" src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/graphic-divider-green-1024x39.png" class="img-responsive h2-art">
    </div>
</div>

<div class="ListingView green-sec hidden">

    <div class="productrow">

        <div class="col-xs-12 col-sm-4 col-md-4">
            <h3 class="cat-title">Patch Cords</h3>
            <div class="columnView green-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-geen.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">CAT 5E</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
            <div class="columnView green-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-geen.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">CAT 6</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
            <div class="columnView green-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-geen.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">CAT 6A</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-4 col-md-4">
             <h3 class="cat-title">Patch Cords</h3>
            <div class="columnView green-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-geen.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-4 col-md-4">
              <h3 class="cat-title">Patch Cords</h3>
            <div class="columnView green-box">                        
                <span class="logo-imgleft"><img src="http://localhost/CustomCable/wp-content/plugins/custom_cable_configuration/images/logo-products-geen.png" class="img-responsive center-block" alt="DSC 0053"></span>
                <div class="txtHeight">
                    <a href="#"><h4 class="fw-600">Indoo JUMPERS / PIGTAILS</h4></a>
                    <!-- <p>Exclusive HLC Termination Process</p> -->
                </div>
            </div>
        </div>
    </div>
</div>


<!--- product mockup htmlo ends -->
<?php echo do_shortcode('[product_grouping]'); ?>
<div class="clear"></div>
<div class="shaped-buttons hidden">
    <h2>Industries Served</h2>
    <ul class="nav nav-tabs " role="navigation">
        <li class="shaped-button">
            <a href="<?php echo get_permalink(1095); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="CATV (Cable TV)" class="popover">
                <?php echo wp_get_attachment_image(734, 'medium', 0, array('class' => 'img-responsive')); ?>
            </a>
        </li>
        <li class="shaped-button blue">
            <a href="<?php echo get_permalink(1096); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="OSP (Outside Plant)" class="popover">
                <?php echo wp_get_attachment_image(741, 'medium', 0, array('class' => 'img-responsive')); ?>
            </a>
        </li>
        <li class="shaped-button">
            <a href="<?php echo get_permalink(1097); ?>"  data-container="body" data-toggle="popover" data-placement="bottom" data-content="Data Center" class="popover">
                <?php echo wp_get_attachment_image(736, 'medium', 0, array('class' => 'img-responsive')); ?>
            </a>
        </li>
        <li class="shaped-button blue">
            <a href="<?php echo get_permalink(1098); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Test & Measurement" class="popover">
                <?php echo wp_get_attachment_image(745, 'medium', 0, array('class' => 'img-responsive')); ?>
            </a>
        </li>
        <li class="shaped-button">
            <a href="<?php echo get_permalink(1099); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Telecommunications" class="popover">
                <?php echo wp_get_attachment_image(746, 'medium', 0, array('class' => 'img-responsive')); ?>
            </a>
        </li>
        <li class="shaped-button blue">
            <a href="<?php echo get_permalink(1100); ?>" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Industrial & Military" class="popover">
                <?php echo wp_get_attachment_image(739, 'medium', 0, array('class' => 'img-responsive')); ?>
            </a>
        </li>
    </ul>
</div>

<br /><br />
<?php include(WP_CONTENT_DIR . '/plugins/sea-spider-tyrex/snippets/flex-content.php'); ?>

<?php get_footer(); ?>
