<div class="row">
	<div class="col-xs-10 col-xs-offset-1">
	<h4>
		<?php //if($review_number != ""){ ?>
		<div itemprop="review" itemscope itemtype="http://schema.org/Review" class="review">
			<i class="fa fa-quote-left"></i>
		<?php //if(get_field('review_title')){ ?>
		    <span itemprop="name" class="review-name"><?php the_field('review_title', $review_number); ?></span>
		<? //} ?>
		    <meta itemprop="itemReviewed" content="The Big Marlin Group">
		<?php //if(get_field('review_title')){ ?>
		    <meta itemprop="datePublished" content="<?php the_field('review_date', $review_number); ?>">
		<? //} ?>
		<?php //if(get_field('review_body')){ ?>
		    <div itemprop="reviewBody" class="review-body"><?php the_field('review_body', $review_number); ?></div>
			<i class="fa fa-quote-right"></i>    
		<? // } ?>
		<?php //if(get_field('review_author')){ ?>
		    <div itemprop="author" class="review-author">
				<?php the_field('review_author', $review_number); ?>, 
				<em>
					<?php the_field('review_title_type', $review_number); ?>,
					<?php echo get_the_title($review_number); ?>
				</em>
			</div>
		<? // } ?>
		
		</div>
		<? // } ?>  
	</h4>
	</div>
</div>