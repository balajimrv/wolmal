<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/core.js'); ?>
<h2>
  <?php echo $this->translate('Timeline Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <?php if (!empty ($this->message)) : ?>
      <div class='result_message'>
          <?php echo $this->translate($this->message) ?>
      </div>
      <br/>
  <?php endif; ?>
  <?php if (!empty($this->tabsContents) and count($this->tabsContents)) : ?>
      <table class="admin_table" width="100%">
          <thead>
            <tr>
                <th width="20%"><?php echo $this->translate('Widget Title') ?></th>
                <th width="15%"><?php echo $this->translate('Current Icon') ?></th>
                <th width="65%"><?php echo $this->translate('Option') ?></th>
            </tr>
          </thead>
          <tbody>
              <?php foreach ($this->tabsContents as $tab): ?>
                <tr>
                    <td><?php echo $this->translate(empty($tab->params['title']) ? $tab->name : $tab->params['title']) ?></td>
                    <td>
                        <?php
                            $file_icon = 'tab_' . $tab->name . '.png';
                            if (file_exists(APPLICATION_PATH . '/public/timeline/tab_icons/' . $file_icon)):
                                $default = false;
                        ?>
                            <img alt="Icon" id="tab_img_<?php echo $tab->name?>" src="<?php echo $this->baseUrl();?>/public/timeline/tab_icons/<?php echo $file_icon . '?' . md5(rand(1, 1000000));?>"/>
                        <?php else:
                                $default = true;
                        ?>
                            <img alt="Default Icon" id="tab_img_<?php echo $tab->name?>" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/default.png"/>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php
                            if (empty($this->id_form_error) or $this->id_form_error != $tab->name) {
                                $form = new Timeline_Form_Admin_IconUpload();
                                $form->content_id->setValue($tab->name);
                                echo $form;
                            }
                            else {
                                echo $this->form;
                            }
                        ?>
                        
                        <div style="float: left;">
                            |
                            <?php echo $this->partial('_icons_collection.tpl', array('tab_name' => $tab->name, 'icons_collection' => $this->icons_collection)) ?>
                            <?php if (!$default):?>
                                |
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'timeline', 'controller' => 'settings', 'action' => 'delete-icon', 'id' => $tab->name), $this->translate('reset'), array('class' => 'smoothbox')) ?>
                            <?php endif; ?>
                        </div>
                        
                    </td>
                </tr>
              <?php endforeach;?>
              <?php if ($this->show_more): ?>
                <tr>
                    <td><?php echo $this->translate('More') ?></td>
                    <td>
                        <?php
                            $file_icon = 'tab_more.png';
                            if (file_exists(APPLICATION_PATH . '/public/timeline/tab_icons/' . $file_icon)):
                                $default = false;
                        ?>
                        <img alt="Icon" id="tab_img_more" src="<?php echo $this->baseUrl();?>/public/timeline/tab_icons/<?php echo $file_icon . '?' . md5(rand(1, 1000000));?>"/>
                        <?php else:
                                $default = true;
                        ?>
                            <img alt="Default Icon"  id="tab_img_more" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/more.png"/>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php
                            if (empty($this->id_form_error) or $this->id_form_error != 'more') {
                                $form = new Timeline_Form_Admin_IconUpload();
                                $form->content_id->setValue(-1);
                                echo $form;
                            }
                            else {
                                echo $this->form;
                            }
                        ?>
                        
                        <div style="float: left;">
                            |
                            <?php echo $this->partial('_icons_collection.tpl', array('tab_name' => 'more', 'icons_collection' => $this->icons_collection)) ?>
                            <?php if (!$default):?>
                                |
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'timeline', 'controller' => 'settings', 'action' => 'delete-icon', 'id' => -1), $this->translate('reset'), array('class' => 'smoothbox')) ?>
                            <?php endif; ?>
                        </div>
                        
                    </td>
                </tr>
              <?php endif; ?>
          </tbody>
      </table>
      <p>
           <?php echo $this->translate ("Image maximal size 65*65px (width * height). Image extension must be png."); ?>
      </p>
  <?php endif; ?>
</div>