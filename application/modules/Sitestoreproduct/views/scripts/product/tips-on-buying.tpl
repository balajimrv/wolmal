<?php $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>

<section class="layout_page_buying">
  <header>
    <div><?php echo $this->translate("Buying Tips") ?></div>
    <div><?php echo $this->translate("When you’re buying any product from our website, we’re committed to help you purchase quality products and keep your transaction safe.") ?></div>   
  </header>
  
  <section class="buying_blocks">
    <ul>
      <li>
      	<?php echo $this->translate("Look at seller's feedback rating for their stores on our website.") ?>
      </li> 
      <li>
      	<?php echo $this->translate("Carefully read the product description, photo, comments and reviews. If you still have questions, the seller should be able to answer them promptly.") ?>
      </li> 
      <li>
      	<?php echo $this->translate("Get in touch with us! Our friendly support team is just an email away.") ?>
      </li>
      <li>
      	<?php echo $this->translate("Be well informed! You’ll find important information in our FAQs.") ?>
      </li>
      <?php if( !empty($this->isBlogListingTypeExists) ) : ?>
        <li>
         <?php echo $this->translate('For some expert style tips and to know what\'s hot lately, %1$svisit our blog%2$s!', '<a href="'.$this->url(array(), "sitereview_general_listtype_".$this->isBlogListingTypeExists->listingtype_id, true).'">', '</a>') ?>
        </li>
      <?php endif; ?>
    </ul>
  </section>
</section>