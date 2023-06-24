<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$ratingValue = $this->ratingType;
$ratingShow = 'small-star';
if ($this->ratingType == 'rating_editor') {
    $ratingType = 'editor';
} elseif ($this->ratingType == 'rating_avg') {
    $ratingType = 'overall';
} else {
    $ratingType = 'user';
}
?>
<div class="sm-content-list">
    <ul data-role="listview" data-inset="false">
        <li data-icon="arrow-r">
            <a href="<?php echo $this->sitestoreproduct->getHref() ?>">
                <?php echo $this->itemPhoto($this->sitestoreproduct, 'thumb.icon'); ?>
                                <h3><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->sitestoreproduct->getTitle(), $this->truncation); ?></h3>
                                <?php if ($ratingValue == 'rating_both'): ?>
                                    <p><?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                                        <br/>
                                        <?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->rating_users, 'user', $ratingShow); ?></p>
                                <?php else: ?>
                                    <p><?php echo $this->showRatingStarSitestoreproduct($this->sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?></p>
                                <?php endif; ?>

                                <?php if (empty($this->category_id)): ?>
                                    <p><?php echo '<b>' . $this->sitestoreproduct->getCategory()->getTitle(true) . '</b>'; ?></p>
                                <?php endif; ?>

                                <p>
                                <?php
                                // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
                                echo $this->getProductInfo($this->sitestoreproduct, $this->identity, 'list_view', 0, $this->showinStock);
                                ?>
                                </p>
                                <p>
                                    <?php
                                    $contentsArray = array();
                                    if (!empty($this->showContent) && in_array('postedDate', $this->showContent)):
                                        $contentsArray[] = $this->timestamp(strtotime($this->sitestoreproduct->creation_date));
                                    endif;
                                    if (!empty($this->postedby)):
                                        $contentsArray[] = $this->translate('created by') . ' <b>' . $this->sitestoreproduct->getOwner()->getTitle() . '</b>';
                                        if (!empty($contentsArray)) {
                                            echo join(" - ", $contentsArray);
                                        }
                                        ?>
            <?php endif; ?>   
                                </p> 
            </a>
        </li>
    </ul>
</div>