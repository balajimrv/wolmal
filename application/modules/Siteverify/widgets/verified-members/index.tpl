<div id="verifyed_members_ajax_responsed">
  <?php
  if (!empty($this->is_ajax) && empty($this->loadFlage)):
    $widId = !empty($this->identity) ? $this->identity : null;
    ?>
    <script type="text/javascript">
      window.addEvent('domready', function() {
        var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $widId) ?>;
        var request = new Request.HTML({
          url: url,
          method: 'get',
          data: {
            format: 'html',
            'loadFlage': 1,
            'user_id': '<?php echo $this->user_id; ?>',
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            if($("verifyed_members_ajax_responsed"))
              $("verifyed_members_ajax_responsed").innerHTML = responseHTML;
          }
        });
        request.send();
      });
    </script>

    <?php
  endif;


  if (!empty($this->showContent)):
    ?>
    <h3><?php echo $this->translate("Most Verified Members"); ?></h3>
    <ul>
        <?php if (COUNT($this->paginator)): ?>
          <?php foreach ($this->paginator as $verification): ?>
            <?php $sitemember = Engine_Api::_()->getItem('user', $verification['resource_id']); ?>
            <li>
              <?php echo $this->htmlLink($sitemember->getHref(), $this->itemPhoto($sitemember, 'thumb.icon', ''), array('class' => 'verifiedmembers_thumb')) ?>
              <i class="sitemember_list_verify_label"></i>
              <div class="verifiedmembers_info">
                <div class='verifiedmembers_name'>
                  <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle())); ?>                  
                </div>

                <div class='verifiedmembers_friends'>
                  <?php echo $this->translate("verified by"); ?>&nbsp;<a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'content-verify-member-list', 'resource_id' => $verification['resource_id']), 'default', true) ?>');"><?php echo $this->translate(array('%s member', '%s members', $verification['verifyCount']), $this->locale()->toNumber($verification['verifyCount'])) ?></a>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <script type="text/javascript">
            if($$(".layout_siteverify_verified_members"))
              $$(".layout_siteverify_verified_members").destroy();
          </script>
        <?php endif; ?>
      </ul>
  <?php endif; ?>
</div>